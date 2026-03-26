<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\FoundItem;
use App\Models\StorageSlot;
use Illuminate\Http\Request;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
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

        return view('staff.found_items.inventory', [
            'zone' => $zone,
            'groupedSlots' => $groupedSlots,
            'occupiedItems' => $occupiedItems,
        ]);
    }

    public function showSlot($fullCode)
    {
        $slot = StorageSlot::where('full_code', $fullCode)->firstOrFail();

        $item = FoundItem::where('storage_location', $fullCode)
            ->whereIn('status', ['Unclaimed', 'Matched'])
            ->first();

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
                $storedDays = \Carbon\Carbon::parse($baseDate)->diffInDays(now());
            }

            $autoRemoveEligible = $item->status === 'Unclaimed'
                && $storedDays !== null
                && $storedDays >= 90;
        }

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

        return view('staff.found_items.slot_details', [
            'slot' => $slot,
            'item' => $item,
            'storedDays' => $storedDays,
            'autoRemoveEligible' => $autoRemoveEligible,
            'displaySlotStatus' => $displaySlotStatus,
            'moveSlots' => $moveSlots,
        ]);
    }
    public function move(Request $request, $id)
    {
        $item = FoundItem::findOrFail($id);

        $request->validate([
            'new_location' => ['required', 'string', 'exists:storage_slots,full_code'],
        ]);

        if (!in_array($item->status, ['Unclaimed', 'Matched'])) {
            return back()->with('error', 'Only active found items can be moved.');
        }

        $targetSlot = StorageSlot::where('full_code', $request->new_location)->firstOrFail();

        if ($targetSlot->slot_status === 'Service') {
            return back()->with('error', 'This slot is under service and cannot be selected.');
        }

        $isOccupied = FoundItem::whereIn('status', ['Unclaimed', 'Matched'])
            ->where('storage_location', $targetSlot->full_code)
            ->where('id', '!=', $item->id)
            ->exists();

        if ($isOccupied) {
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
        });

        return redirect()
            ->route('staff.inventory.show_slot', $targetSlot->full_code)
            ->with('success', 'Item moved successfully.');
    }
    public function remove(Request $request, $id)
    {
        $item = FoundItem::findOrFail($id);

        $request->validate([
            'removal_reason' => ['required', 'string', 'max:255'],
        ]);

        if ($item->status === 'Claimed') {
            return back()->with('error', 'Claimed items cannot be removed.');
        }

        $storedDays = null;
        $autoRemoveEligible = false;

        if ($item->found_date) {
            $storedDays = \Carbon\Carbon::parse($item->found_date)->diffInDays(now());
            $autoRemoveEligible = $item->status === 'Unclaimed' && $storedDays >= 90;
        }

        $isAdmin = auth()->check() && auth()->user()->role === 'Admin';

        if (!$autoRemoveEligible && !$isAdmin) {
            return back()->with('error', 'Only Admin can remove items before the 90-day unclaimed rule is met.');
        }

        $oldLocation = $item->storage_location;

        DB::transaction(function () use ($item, $request, $oldLocation, $autoRemoveEligible) {
            $item->update([
                'status' => 'Removed',
                'removed_at' => now(),
                'removal_reason' => $request->removal_reason,
                'storage_location' => null,
            ]);

            InventoryMovement::create([
                'found_item_id' => $item->id,
                'from_location' => $oldLocation,
                'to_location' => null,
                'action_type' => $autoRemoveEligible ? 'auto_remove' : 'remove',
                'remarks' => $request->removal_reason,
                'performed_by' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('staff.inventory.show_slot', $oldLocation)
            ->with('success', 'Item removed from inventory successfully.');
    }

}