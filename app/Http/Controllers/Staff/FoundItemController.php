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
        return view('staff.found_items.create');
    }

    public function store(StoreFoundItemRequest $request)
    {
        $data = $request->validated();

        $request->validate([
            'finder_email' => 'nullable|email'
        ]);

        $targetLocation = $request->input('storage_location'); 

        $isOccupied = \App\Models\FoundItem::where('storage_location', $targetLocation)
            ->where('status', '!=', 'Claimed')
            ->exists();

        if ($isOccupied) {
            return back()
                ->withInput()
                ->withErrors(['storage_location' => "Error: The slot {$targetLocation} is already occupied! Please choose another one."]);
        }

        if ($request->has('sub_colors') && is_array($request->input('sub_colors'))) {
            $subColorsString = implode(', ', $request->input('sub_colors'));
            if ($data['color'] === 'Multi-color') {
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
                $finder->increment('points', 100); // Give 50 points
                $successMessage = 'Item saved successfully and 100 points were awarded to ' . $finder->name . '!';
            }
        }

        return redirect()->route('staff.found-items.index')
                        ->with('success', $successMessage);
    }
    
    public function checkOccupiedSlots(Request $request)
    {
        $zone = $request->query('zone');
        $shelf = $request->query('shelf');

        $prefix = $zone . '-' . $shelf . '-';

        $occupiedItems = \App\Models\FoundItem::where('status', '!=', 'Claimed')
            ->where('storage_location', 'LIKE', $prefix . '%')
            ->get();

        $occupiedSlots = $occupiedItems->map(function ($item) use ($prefix) {
            return str_replace($prefix, '', $item->storage_location);
        })->toArray();

        return response()->json($occupiedSlots);
    }

    public function exportFoundItems()
    {
        $fileName = 'Airport_Found_Items_Report_' . date('Y-m-d') . '.csv';
        
        $items = \App\Models\FoundItem::orderBy('id', 'asc')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Item Name', 'Category', 'Location Found', 'Status', 'Date Logged'];

        $callback = function() use($items, $columns) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, $columns);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->item_name,
                    $item->category,
                    $item->found_location,
                    $item->status,
                    $item->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}