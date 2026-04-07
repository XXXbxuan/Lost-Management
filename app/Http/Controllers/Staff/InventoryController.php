<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\MarkInventorySlotServiceRequest;
use App\Http\Requests\Staff\MoveFoundItemRequest;
use App\Http\Requests\Staff\RemoveFoundItemRequest;
use App\Models\AdminActionLog;
use App\Models\FoundItem;
use App\Models\InventoryMovement;
use App\Models\LostItemReport;
use App\Models\MatchRecord;
use App\Models\StorageSlot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function showInventoryMap(Request $request): View
    {
        $zone = $request->get('zone', 'GEN');

        $allowedZones = ['GEN', 'VAULT', 'BAG'];
        if (!in_array($zone, $allowedZones)) {
            $zone = 'GEN';
        }

        $slots = StorageSlot::where('zone_code', $zone)
            ->orderBy('shelf_code')
            ->orderBy('slot_code')
            ->get();

        $occupiedItems = FoundItem::whereIn('status', ['Unclaimed', 'Matched'])
            ->whereNotNull('storage_location')
            ->where('storage_location', 'like', $zone . '-%')
            ->get()
            ->keyBy('storage_location');

        $groupedSlots = $slots->groupBy('shelf_code');

        return view('staff.inventory.index', [
            'zone' => $zone,
            'groupedSlots' => $groupedSlots,
            'occupiedItems' => $occupiedItems,
        ]);
    }

    public function showSlotDetails(string $fullCode): View
    {
        $slot = StorageSlot::where('full_code', $fullCode)->firstOrFail();

        $item = FoundItem::where('storage_location', $fullCode)
            ->whereIn('status', ['Unclaimed', 'Matched'])
            ->first();

        $storageInfo = $this->calculateStorageInfo($item);

        if ($slot->slot_status === 'Service') {
            $displaySlotStatus = 'Service';
        } elseif ($item) {
            $displaySlotStatus = 'Occupied';
        } else {
            $displaySlotStatus = 'Available';
        }

        $occupiedCodes = FoundItem::whereIn('status', ['Unclaimed', 'Matched'])
            ->whereNotNull('storage_location')
            ->pluck('storage_location')
            ->toArray();

        $moveSlots = StorageSlot::orderBy('zone_code')
            ->orderBy('shelf_code')
            ->orderBy('slot_code')
            ->get()
            ->map(function ($storageSlot) use ($occupiedCodes) {
                return [
                    'full_code' => $storageSlot->full_code,
                    'zone_code' => $storageSlot->zone_code,
                    'shelf_code' => $storageSlot->shelf_code,
                    'slot_code' => $storageSlot->slot_code,
                    'slot_status' => $storageSlot->slot_status,
                    'is_occupied' => in_array($storageSlot->full_code, $occupiedCodes),
                ];
            })
            ->values();

        return view('staff.inventory.slot_details', [
            'slot' => $slot,
            'item' => $item,
            'storedDays' => $storageInfo['storedDays'],
            'autoRemoveEligible' => $storageInfo['autoRemoveEligible'],
            'displaySlotStatus' => $displaySlotStatus,
            'moveSlots' => $moveSlots,
        ]);
    }

    public function moveFoundItem(MoveFoundItemRequest $request, int $id): RedirectResponse
    {
        $item = FoundItem::findOrFail($id);
        $validated = $request->validated();

        if (!in_array($item->status, ['Unclaimed', 'Matched'])) {
            return back()->with('error', 'Only active found items can be moved.');
        }

        $targetSlot = StorageSlot::where('full_code', $validated['new_location'])->firstOrFail();

        if ($targetSlot->slot_status === 'Service') {
            return back()->with('error', 'This slot is under service and cannot be selected.');
        }

        if ($this->isSlotOccupiedByActiveItem($targetSlot->full_code, $item->id)) {
            return back()->with('error', 'This slot is already occupied.');
        }

        if ($item->storage_location === $targetSlot->full_code) {
            return back()->with('error', 'Please choose a different slot.');
        }

        $fromLocation = $item->storage_location;

        DB::transaction(function () use ($item, $targetSlot, $fromLocation) {
            $item->update([
                'storage_location' => $targetSlot->full_code,
            ]);

            InventoryMovement::create([
                'found_item_id' => $item->id,
                'from_location' => $fromLocation,
                'to_location' => $targetSlot->full_code,
                'action_type' => 'move',
                'remarks' => 'Item moved from inventory slot details page.',
                'performed_by' => auth()->id(),
            ]);

            $this->writeActionLog(
                'MOVE_FOUND_ITEM',
                "Found Item #{$item->id}",
                "Moved item: {$item->item_name} from [{$fromLocation}] to [{$targetSlot->full_code}]."
            );
        });

        return redirect()
            ->route('staff.inventory.show_slot', $targetSlot->full_code)
            ->with('success', 'Item moved successfully.');
    }

    public function removeFoundItem(RemoveFoundItemRequest $request, int $id): RedirectResponse
    {
        $item = FoundItem::findOrFail($id);
        $validated = $request->validated();

        if ($item->status === 'Claimed') {
            return back()->with('error', 'Claimed items cannot be removed.');
        }

        $storageInfo = $this->calculateStorageInfo($item);
        $autoRemoveEligible = $storageInfo['autoRemoveEligible'];

        $isAdmin = auth()->check() && auth()->user()->role === 'Admin';

        if (!$autoRemoveEligible && !$isAdmin) {
            return back()->with('error', 'Only Admin can remove items before the 90-day unclaimed rule is met.');
        }

        $oldLocation = $item->storage_location;

        DB::transaction(function () use ($item, $validated, $oldLocation, $autoRemoveEligible) {
            $item->update([
                'status' => 'Removed',
                'removed_at' => now(),
                'removal_reason' => $validated['removal_reason'],
                'storage_location' => null,
            ]);

            $activeMatch = MatchRecord::where('foundId', $item->id)
                ->whereIn('status', ['Verified', 'Confirmed', 'Reschedule Requested'])
                ->latest('id')
                ->first();

            if ($activeMatch) {
                $existingNotes = trim((string) $activeMatch->notes);

                $activeMatch->update([
                    'status' => 'Rejected',
                    'notes' => $existingNotes !== ''
                        ? $existingNotes . "\nAuto-updated: linked found item was removed from inventory."
                        : 'Auto-updated: linked found item was removed from inventory.',
                ]);

                $linkedLostItem = LostItemReport::find($activeMatch->lostId);

                if ($linkedLostItem && $linkedLostItem->status !== 'Claimed') {
                    $linkedLostItem->update([
                        'status' => 'LOST',
                    ]);
                }
            }

            InventoryMovement::create([
                'found_item_id' => $item->id,
                'from_location' => $oldLocation,
                'to_location' => null,
                'action_type' => $autoRemoveEligible ? 'auto_remove' : 'remove',
                'remarks' => $validated['removal_reason'],
                'performed_by' => auth()->id(),
            ]);

            $this->writeActionLog(
                $autoRemoveEligible ? 'AUTO_REMOVE_FOUND_ITEM' : 'REMOVE_FOUND_ITEM',
                "Found Item #{$item->id}",
                "Removed item: {$item->item_name} from [{$oldLocation}]. Reason: {$validated['removal_reason']}."
            );
        });

        return redirect()
            ->route('staff.inventory.index', ['zone' => explode('-', $oldLocation)[0] ?? 'GEN'])
            ->with('success', 'Item removed from inventory successfully.');
    }

    public function markSlotAsService(MarkInventorySlotServiceRequest $request, string $fullCode): RedirectResponse
    {
        $slot = StorageSlot::where('full_code', $fullCode)->firstOrFail();
        $validated = $request->validated();

        if ($this->isSlotOccupiedByActiveItem($fullCode)) {
            return back()->with('error', 'This slot is currently occupied. Move or remove the item first.');
        }

        if ($slot->slot_status === 'Service') {
            return back()->with('error', 'This slot is already marked as service.');
        }

        $slot->update([
            'slot_status' => 'Service',
            'remark' => $validated['service_remark'],
        ]);

        $this->writeActionLog(
            'MARK_SLOT_SERVICE',
            "Storage Slot {$slot->full_code}",
            "Marked slot as service. Remark: {$validated['service_remark']}"
        );

        return back()->with('success', 'Slot marked as service successfully.');
    }

    public function restoreServiceSlot(string $fullCode): RedirectResponse
    {
        $slot = StorageSlot::where('full_code', $fullCode)->firstOrFail();

        if ($slot->slot_status !== 'Service') {
            return back()->with('error', 'Only service slots can be restored.');
        }

        $slot->update([
            'slot_status' => 'Available',
            'remark' => null,
        ]);

        $this->writeActionLog(
            'RESTORE_SLOT_SERVICE',
            "Storage Slot {$slot->full_code}",
            'Restored slot from service to available.'
        );

        return back()->with('success', 'Slot restored to available successfully.');
    }

    private function calculateStorageInfo(?FoundItem $item): array
    {
        $storedDays = null;
        $autoRemoveEligible = false;

        if ($item) {
            $baseDate = null;

            if (!empty($item->found_date)) {
                $baseDate = $item->found_date;
            } elseif (!empty($item->created_at)) {
                $baseDate = $item->created_at;
            }

            if ($baseDate) {
                $storedDays = Carbon::parse($baseDate)->diffInDays(now());
            }

            $autoRemoveEligible = $item->status === 'Unclaimed'
                && $storedDays !== null
                && $storedDays >= 90;
        }

        return [
            'storedDays' => $storedDays,
            'autoRemoveEligible' => $autoRemoveEligible,
        ];
    }

    private function isSlotOccupiedByActiveItem(string $fullCode, ?int $ignoreItemId = null): bool
    {
        return FoundItem::whereIn('status', ['Unclaimed', 'Matched'])
            ->where('storage_location', $fullCode)
            ->when($ignoreItemId, function ($query) use ($ignoreItemId) {
                $query->where('id', '!=', $ignoreItemId);
            })
            ->exists();
    }

    private function getActorName(): string
    {
        return auth()->user()->name
            ?: (auth()->user()->username ?: 'Staff');
    }

    private function writeActionLog(string $actionType, string $targetName, string $details): void
    {
        AdminActionLog::create([
            'admin_name' => $this->getActorName(),
            'action_type' => $actionType,
            'target_name' => $targetName,
            'details' => $details,
        ]);
    }
}