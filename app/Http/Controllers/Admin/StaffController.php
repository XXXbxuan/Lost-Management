<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Services\StaffService;
use App\Models\AdminActionLog;
// ❌ 删除了 use Auth;

class StaffController extends Controller
{
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
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

    // 保存逻辑
    public function store(StoreStaffRequest $request)
    {
        $staff = $this->staffService->createStaff($request->validated());

        // ✅ 改回 auth()->user()
        // 意思：尝试获取登录用户的名字，如果获取不到（比如没登录），就用 "System Admin"
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

    // 更新逻辑
    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        // ✅ 改回 auth()->user()
        $adminName = auth()->user()->name ?? 'System Admin';

        // 场景 A: 封禁/解封
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

        // 场景 B: 修改资料
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

    // 删除逻辑
    public function destroy(Staff $staff)
    {
        $user = $staff->user;
        
        // ✅ 改回 auth()->user()
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
}