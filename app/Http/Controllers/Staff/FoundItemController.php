<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\FoundItem;
use App\Models\User;
use App\Http\Requests\StoreFoundItemRequest;
use App\Services\FoundItemService;
use Illuminate\Http\Request;

class FoundItemController extends Controller
{
    protected $foundItemService;

    public function __construct(FoundItemService $foundItemService)
    {
        $this->foundItemService = $foundItemService;
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'All');

        $query = FoundItem::latest();

        if ($status !== 'All') {
            $query->where('status', $status);
        }

        $foundItems = $query->paginate(10);

        return view('staff.found_items.index', compact('foundItems', 'status'));
    }

    public function create()
    {
        $mode = 'create';
        $foundItem = null;

        return view('staff.found_items.create', compact('mode', 'foundItem'));
    }

    public function store(StoreFoundItemRequest $request)
    {
        $data = $request->validated();

        $request->validate([
            'finder_email' => 'nullable|email',
        ]);

        $targetLocation = $request->input('storage_location');

        $isOccupied = FoundItem::where('storage_location', $targetLocation)
            ->where('status', '!=', 'Claimed')
            ->exists();

        if ($isOccupied) {
            return back()
                ->withInput()
                ->withErrors([
                    'storage_location' => "Error: The slot {$targetLocation} is already occupied! Please choose another one."
                ]);
        }

        if ($request->has('sub_colors') && is_array($request->input('sub_colors'))) {
            $subColorsString = implode(', ', $request->input('sub_colors'));

            if (($data['color'] ?? null) === 'Multi-color') {
                $data['color'] = 'Multi-color (' . $subColorsString . ')';
            }
        }

        $this->foundItemService->createFoundItem(
            $data,
            $request->file('image')
        );

        $successMessage = 'Found item registered successfully.';

        if ($request->filled('finder_email')) {
            $finder = User::where('email', $request->finder_email)->first();

            if ($finder) {
                $finder->increment('points', 100);
                $successMessage = 'Item saved successfully and 100 points were awarded to the user who found item!';
            }
        }

        return redirect()
            ->route('staff.found-items.index')
            ->with('success', $successMessage);
    }

    public function edit(FoundItem $foundItem)
    {
        if ($foundItem->status !== 'Unclaimed') {
            return redirect()
                ->route('staff.found-items.index')
                ->with('error', 'Only unclaimed items can be edited.');
        }

        $mode = 'edit';

        return view('staff.found_items.create', compact('mode', 'foundItem'));
    }

    public function update(Request $request, FoundItem $foundItem)
    {
        if ($foundItem->status !== 'Unclaimed') {
            return redirect()
                ->route('staff.found-items.index')
                ->with('error', 'Only unclaimed items can be updated.');
        }

        $validated = $request->validate([
            'item_name'        => ['required', 'string', 'max:255'],
            'category'         => ['required', 'string'],
            'brand'            => ['nullable', 'string', 'max:50'],
            'color'            => ['required', 'string'],
            'sub_colors'       => ['nullable', 'array'],
            'sub_colors.*'     => ['string'],
            'serial_number'    => ['nullable', 'string'],
            'found_location'   => ['required', 'string'],
            'flight_number'    => ['nullable', 'string', 'max:20'],
            'found_time'       => ['required', 'date'],
            'description'      => ['nullable', 'string'],
            'zone'             => ['required', 'string'],
            'shelf'            => ['required', 'string'],
            'slot'             => ['required', 'string'],
            'storage_location' => ['required', 'string'],
            'image'            => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        $targetLocation = $request->input('storage_location');

        $isOccupied = FoundItem::where('storage_location', $targetLocation)
            ->where('id', '!=', $foundItem->id)
            ->where('status', '!=', 'Claimed')
            ->exists();

        if ($isOccupied) {
            return back()
                ->withInput()
                ->withErrors([
                    'storage_location' => "Error: The slot {$targetLocation} is already occupied! Please choose another one."
                ]);
        }

        if ($request->has('sub_colors') && is_array($request->input('sub_colors'))) {
            $subColorsString = implode(', ', $request->input('sub_colors'));

            if (($validated['color'] ?? null) === 'Multi-color') {
                $validated['color'] = 'Multi-color (' . $subColorsString . ')';
            }
        }

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('found_items', 'public');
        }

        unset($validated['zone'], $validated['shelf'], $validated['slot'], $validated['sub_colors']);

        $foundItem->update($validated);

        return redirect()
            ->route('staff.found-items.index')
            ->with('success', 'Found item updated successfully.');
    }

    public function destroy(FoundItem $foundItem)
    {
        if ($foundItem->status !== 'Unclaimed') {
            return redirect()
                ->route('staff.found-items.index')
                ->with('error', 'Only unclaimed items can be deleted.');
        }

        $foundItem->delete();

        return redirect()
            ->route('staff.found-items.index')
            ->with('success', 'Found item deleted successfully.');
    }

    public function checkOccupiedSlots(Request $request)
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

        $serviceSlots = \App\Models\StorageSlot::where('zone_code', $zone)
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

    public function exportFoundItems()
    {
        $fileName = 'Airport_Found_Items_Report_' . date('Y-m-d') . '.csv';

        $items = FoundItem::orderBy('id', 'asc')->get();

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
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
}