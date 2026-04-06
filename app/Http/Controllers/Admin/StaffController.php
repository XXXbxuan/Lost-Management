<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffRequest;
use App\Http\Requests\Admin\UpdateStaffRequest;
use App\Models\AdminActionLog;
use App\Models\Claim;
use App\Models\FoundItem;
use App\Models\LostItemReport;
use App\Models\Staff;
use App\Services\StaffService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    protected StaffService $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    public function analyticsOverview()
    {
        $analytics = $this->buildAnalyticsOverviewData();

        return view('analytics_overview', $analytics);
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $staffMembers = Staff::with('user')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('email', 'like', "%{$search}%");
                        });
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

        $this->logAdminAction(
            'CREATE_STAFF',
            $staff->name,
            "Action: Registered New Staff, " .
            "Email: {$staff->user->email}, " .
            "Username: {$staff->user->username}, " .
            "Contact: {$staff->contact_number}, " .
            "Department: {$staff->department}, " .
            "Role: {$staff->user->role}."
        );

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'New staff member created successfully.');
    }

    public function edit(Staff $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        if ($request->has('toggle_status')) {
            $oldStatus = $staff->status;
            $newStatus = $this->staffService->toggleStatus($staff);

            $this->logAdminAction(
                $newStatus === 'Blocked' ? 'BLOCK_STAFF' : 'UNBLOCK_STAFF',
                $staff->name,
                "Changed status from {$oldStatus} to {$newStatus}."
            );

            return back()->with('success', "Staff status updated to {$newStatus}.");
        }

        $this->staffService->updateStaff($staff, $request->validated());

        $this->logAdminAction(
            'UPDATE_STAFF',
            $staff->name,
            "Action: Updated Profile Details, " .
            "Email: {$staff->user->email}, " .
            "New Contact: {$staff->contact_number}, " .
            "New Dept: {$staff->department}."
        );

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff details updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $user = $staff->user;

        DB::transaction(function () use ($staff, $user) {
            $logInfo = "Deleted Staff Info: " .
                "Name: {$staff->name}, " .
                "Email: " . ($user->email ?? 'N/A') . ", " .
                "Username: " . ($user->username ?? 'N/A') . ", " .
                "Contact: {$staff->contact_number}, " .
                "Department: {$staff->department}.";

            $this->logAdminAction('DELETE', $staff->name, $logInfo);

            if ($user) {
                $user->delete();
            }

            $staff->delete();
        });

        return redirect()
            ->route('admin.staff.index')
            ->with('success', 'Staff deleted. Email is now free to use. History saved to logs.');
    }

        private function buildAnalyticsOverviewData(): array
    {
        $totalFound = FoundItem::count();
        $totalLost = LostItemReport::count();
        $totalStaff = Staff::count();

        $claimedCount = Claim::count();
        $successRate = $totalLost > 0
            ? round(($claimedCount / $totalLost) * 100, 1)
            : 0;

        $categoryStats = FoundItem::selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $hotspotStats = FoundItem::selectRaw('found_location, COUNT(*) as total')
            ->groupBy('found_location')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'found_location');

        return [
            'totalFound' => $totalFound,
            'totalLost' => $totalLost,
            'totalStaff' => $totalStaff,
            'successRate' => $successRate,
            'categoryLabels' => $categoryStats->keys(),
            'categoryData' => $categoryStats->values(),
            'hotspotLabels' => $hotspotStats->keys(),
            'hotspotData' => $hotspotStats->values(),
        ];
    }

    private function logAdminAction(string $actionType, string $targetName, string $details): void
    {
        AdminActionLog::create([
            'admin_name'  => $this->getAdminName(),
            'action_type' => $actionType,
            'target_name' => $targetName,
            'details'     => $details,
        ]);
    }

    private function getAdminName(): string
    {
        return auth()->user()->name ?? 'System Admin';
    }
}