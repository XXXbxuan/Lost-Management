<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreFoundItemRequest;
use App\Http\Requests\Staff\UpdateFoundItemRequest;
use App\Models\AdminActionLog;
use App\Models\FoundItem;
use App\Models\StorageSlot;
use App\Models\User;
use App\Services\FoundItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;

class FoundItemController extends Controller
{
    protected FoundItemService $foundItemService;

    public function __construct(FoundItemService $foundItemService)
    {
        $this->foundItemService = $foundItemService;
    }

    public function index(Request $request): View
    {
        $status = $request->query('status', 'All');
        $openItemId = $request->query('open_item');

        $query = FoundItem::query()->latest();
        $perPage = 10;

        if ($status !== 'All') {
            $query->where('status', $status);
        }

        if ($openItemId) {
            $orderedIds = (clone $query)->pluck('id')->values();

            $position = $orderedIds->search(function ($id) use ($openItemId) {
                return (string) $id === (string) $openItemId;
            });

            if ($position !== false) {
                $targetPage = (int) floor($position / $perPage) + 1;

                Paginator::currentPageResolver(function () use ($targetPage) {
                    return $targetPage;
                });
            }
        }

        $foundItems = $query->paginate($perPage)->appends($request->query());

        return view('staff.found_items.index', compact('foundItems', 'status', 'openItemId'));
    }

    public function create(): View
    {
        $mode = 'create';
        $foundItem = null;

        return view('staff.found_items.create', compact('mode', 'foundItem'));
    }

    public function store(StoreFoundItemRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $targetLocation = $data['storage_location'];

        if ($this->isStorageLocationOccupied($targetLocation)) {
            return back()
                ->withInput()
                ->withErrors([
                    'storage_location' => "Error: The slot {$targetLocation} is already occupied! Please choose another one.",
                ]);
        }

        $data = $this->normalizeMultiColor($data);

        $this->foundItemService->createFoundItem(
            $data,
            $request->file('image')
        );

        $successMessage = 'Found item registered successfully.';

        if (!empty($data['finder_email'])) {
            $finder = User::where('email', $data['finder_email'])->first();

            if ($finder) {
                $finder->increment('points', 100);
                $successMessage = 'Item saved successfully and 100 points were awarded to the user who found the item.';
            }
        }

        return redirect()
            ->route('staff.found-items.index')
            ->with('success', $successMessage);
    }

    public function edit(FoundItem $foundItem): RedirectResponse|View
    {
        if ($foundItem->status !== 'Unclaimed') {
            return redirect()
                ->route('staff.found-items.index')
                ->with('error', 'Only unclaimed items can be edited.');
        }

        $mode = 'edit';

        return view('staff.found_items.create', compact('mode', 'foundItem'));
    }

    public function update(UpdateFoundItemRequest $request, FoundItem $foundItem): RedirectResponse
    {
        if ($foundItem->status !== 'Unclaimed') {
            return redirect()
                ->route('staff.found-items.index')
                ->with('error', 'Only unclaimed items can be updated.');
        }

        $validated = $request->validated();
        $targetLocation = $validated['storage_location'];

        if ($this->isStorageLocationOccupied($targetLocation, $foundItem->id)) {
            return back()
                ->withInput()
                ->withErrors([
                    'storage_location' => "Error: The slot {$targetLocation} is already occupied! Please choose another one.",
                ]);
        }

        $validated = $this->normalizeMultiColor($validated);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('found_items', 'public');
        }

        unset($validated['zone'], $validated['shelf'], $validated['slot'], $validated['sub_colors'], $validated['finder_email']);

        $foundItem->update($validated);

        $this->writeActionLog(
            'UPDATE_FOUND_ITEM',
            "Found Item #{$foundItem->id}",
            "Updated found item: {$foundItem->item_name}, Category: {$foundItem->category}, Storage: {$foundItem->storage_location}."
        );

        return redirect()
            ->route('staff.found-items.index')
            ->with('success', 'Found item updated successfully.');
    }

    public function destroy(FoundItem $foundItem): RedirectResponse
    {
        if ($foundItem->status !== 'Unclaimed') {
            return redirect()
                ->route('staff.found-items.index')
                ->with('error', 'Only unclaimed items can be deleted.');
        }

        $this->writeActionLog(
            'DELETE_FOUND_ITEM',
            "Found Item #{$foundItem->id}",
            "Deleted found item: {$foundItem->item_name}, Category: {$foundItem->category}, Storage: {$foundItem->storage_location}."
        );

        $foundItem->delete();

        return redirect()
            ->route('staff.found-items.index')
            ->with('success', 'Found item deleted successfully.');
    }

    public function getOccupiedSlots(Request $request): JsonResponse
    {
        $zone = $request->query('zone');
        $shelf = $request->query('shelf');

        $prefix = $zone . '-' . $shelf . '-';

        $occupiedSlots = FoundItem::whereIn('status', ['Unclaimed', 'Matched'])
            ->where('storage_location', 'LIKE', $prefix . '%')
            ->pluck('storage_location')
            ->map(function ($location) use ($prefix) {
                return str_replace($prefix, '', $location);
            })
            ->unique()
            ->values()
            ->toArray();

        $serviceSlots = StorageSlot::where('zone_code', $zone)
            ->where('shelf_code', $shelf)
            ->where('slot_status', 'Service')
            ->pluck('slot_code')
            ->map(function ($slot) {
                return str_pad($slot, 2, '0', STR_PAD_LEFT);
            })
            ->unique()
            ->values()
            ->toArray();

        return response()->json([
            'occupied' => $occupiedSlots,
            'service' => $serviceSlots,
        ]);
    }

    public function exportFoundItemsReport()
    {
        $fileName = 'Airport_Found_Items_Report_' . now()->format('Y-m-d') . '.csv';
        $items = FoundItem::orderBy('id', 'asc')->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['ID', 'Item Name', 'Category', 'Location Found', 'Status', 'Date Logged'];

        $callback = function () use ($items, $columns) {
            $file = fopen('php://output', 'w');

            fputcsv($file, $columns);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->item_name,
                    $item->category,
                    $item->found_location,
                    $item->status,
                    $item->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function isStorageLocationOccupied(string $storageLocation, ?int $ignoreId = null): bool
    {
        return FoundItem::where('storage_location', $storageLocation)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->where('status', '!=', 'Claimed')
            ->exists();
    }

    private function normalizeMultiColor(array $data): array
    {
        if (!empty($data['sub_colors']) && is_array($data['sub_colors'])) {
            $subColorsString = implode(', ', $data['sub_colors']);

            if (($data['color'] ?? null) === 'Multi-color') {
                $data['color'] = 'Multi-color (' . $subColorsString . ')';
            }
        }

        return $data;
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

    private function getActorName(): string
    {
        return auth()->user()->name
            ?: (auth()->user()->username ?: 'Staff');
    }
}