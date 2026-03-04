<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Models\FoundItem; // <--- Added for Chart
use App\Models\LostItemReport;  // <--- Added for Chart (Assuming your model name)
use Illuminate\Http\Request;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Services\StaffService;
use App\Models\AdminActionLog;
use Illuminate\Support\Facades\DB; // <--- Added for Chart Logic

class StaffController extends Controller
{
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    /**
     * NEW: Admin Analytics Dashboard with Pie Chart Logic
     */
    public function dashboard()
    {
        // 1. Calculate totals for the top summary cards
        $totalFound = FoundItem::count();
        $totalLost = LostItemReport::count(); // Adjust model name if needed
        $totalStaff = Staff::count();

        // 2. Aggregate Found Items by Category (Pie Chart)
        $categories = FoundItem::select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $categoryLabels = array_keys($categories);
        $categoryData = array_values($categories);

        // 3. 🌟 NEW: Get Top 5 "Hotspot" Locations (Horizontal Bar Chart)
        $hotspots = FoundItem::select('found_location', DB::raw('count(*) as total'))
            ->groupBy('found_location')
            ->orderBy('total', 'desc') // Sort highest to lowest
            ->limit(5) // Only take the top 5 worst spots
            ->pluck('total', 'found_location')
            ->toArray();

        $hotspotLabels = array_keys($hotspots);
        $hotspotData = array_values($hotspots);

        // 4. Return to view
        return view('admin.dashboard', compact(
            'totalFound', 
            'totalLost', 
            'totalStaff',
            'categoryLabels', 
            'categoryData',
            'hotspotLabels', // Send hotspot labels to view
            'hotspotData'    // Send hotspot numbers to view
        ));
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $staffMembers = Staff::with('user')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('email', 'like', "%{$search}%");
                      });
            })
            ->paginate(10);

        return view('admin.staff.index', compact('staffMembers', 'search'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(StoreStaffRequest $request)
    {
        $staff = $this->staffService->createStaff($request->validated());
        $adminName = auth()->user()->name ?? 'System Admin';

        AdminActionLog::create([
            'admin_name'  => $adminName, 
            'action_type' => 'CREATE_STAFF',
            'target_name' => $staff->name,
            'details'     => "Action: Registered New Staff, " .
                             "Email: {$staff->user->email}, " .
                             "Username: {$staff->user->username}, " .
                             "Contact: {$staff->contact_number}, " .
                             "Department: {$staff->department}, " .
                             "Role: {$staff->user->role}."
        ]);

        return redirect()->route('admin.staff.index')
                         ->with('success', 'New staff member created successfully.');
    }

    public function edit(Staff $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        $adminName = auth()->user()->name ?? 'System Admin';

        if ($request->has('toggle_status')) {
            $oldStatus = $staff->status;
            $newStatus = $this->staffService->toggleStatus($staff);
            
            AdminActionLog::create([
                'admin_name'  => $adminName,
                'action_type' => $newStatus === 'Blocked' ? 'BLOCK_STAFF' : 'UNBLOCK_STAFF',
                'target_name' => $staff->name,
                'details'     => "Changed status from {$oldStatus} to {$newStatus}."
            ]);

            return back()->with('success', "Staff status updated to {$newStatus}.");
        }

        $this->staffService->updateStaff($staff, $request->validated());

        AdminActionLog::create([
            'admin_name'  => $adminName,
            'action_type' => 'UPDATE_STAFF',
            'target_name' => $staff->name,
            'details'     => "Action: Updated Profile Details, " .
                             "Email: {$staff->user->email}, " .
                             "New Contact: {$staff->contact_number}, " .
                             "New Dept: {$staff->department}."
        ]);

        return redirect()->route('admin.staff.index')
                         ->with('success', 'Staff details updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $user = $staff->user;
        $adminName = auth()->user()->name ?? 'System Admin';

        $logInfo = "Deleted Staff Info: " .
                   "Name: {$staff->name}, " .
                   "Email: " . ($user->email ?? 'N/A') . ", " .
                   "Username: " . ($user->username ?? 'N/A') . ", " .
                   "Contact: {$staff->contact_number}, " .
                   "Department: {$staff->department}.";

        AdminActionLog::create([
            'admin_name'  => $adminName,
            'action_type' => 'DELETE',
            'target_name' => $staff->name,
            'details'     => $logInfo
        ]);

        if ($user) {
            $user->delete(); 
        }
        $staff->delete();

        return redirect()->route('admin.staff.index')
                         ->with('success', 'Staff deleted. Email is now free to use. History saved to logs.');
    }

    /**
     * NEW: Professional Export to Excel (CSV) Feature
     */
    public function exportFoundItems()
    {
        $fileName = 'Airport_Found_Items_Report_' . date('Y-m-d') . '.csv';
        
        // Get all found items, sorted by newest first
        $items = \App\Models\FoundItem::orderBy('created_at', 'desc')->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // The columns you want in your Excel file
        $columns = ['ID', 'Item Name', 'Category', 'Location Found', 'Status', 'Date Logged'];

        $callback = function() use($items, $columns) {
            $file = fopen('php://output', 'w');
            
            // Write the header row
            fputcsv($file, $columns);

            // Write the data rows
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