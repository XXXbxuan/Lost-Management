<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Services\StaffService;
use App\Models\AdminActionLog;

class StaffController extends Controller
{
    protected $staffService;
    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }
    // 显示员工列表 (对应 Figure 4.21)
    public function index(Request $request)
    {
        $search = $request->input('search');

        // 高效查询：关联 User 表，并支持搜索 Name 或 Email
        $staffMembers = Staff::with('user')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('email', 'like', "%{$search}%");
                      });
            })
            ->paginate(10); // 分页，每页显示10个

        // 我们还没创建 view，但先写好路径
        return view('admin.staff.index', compact('staffMembers', 'search'));
    }
    public function create()
    {
        return view('admin.staff.create');
    }

    /**
     * 3. 保存逻辑 (Store)
     */
    public function store(StoreStaffRequest $request)
    {
        // 1. 创建员工
        $staff = $this->staffService->createStaff($request->validated());

        // 2. [升级版] 记录 Log - 包含 Email 和 Contact
        \App\Models\AdminActionLog::create([
            'admin_name'  => auth()->user()->name,
            'action_type' => 'CREATE_STAFF',
            'target_name' => $staff->name,
            // 这里把 Email, Contact, Username 全部拼接到 details 里
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

    /**
     * 4. 编辑页 (Edit)
     */
    public function edit(Staff $staff)
    {
        return view('admin.staff.edit', compact('staff'));
    }

    /**
     * 5. 更新逻辑 (Update)
     */
    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        // 场景 A: 封禁/解封 (Block/Unblock)
        if ($request->has('toggle_status')) {
            $oldStatus = $staff->status;
            $newStatus = $this->staffService->toggleStatus($staff);
            
            // [新增] 记录 Log
            \App\Models\AdminActionLog::create([
                'admin_name'  => auth()->user()->name,
                'action_type' => $newStatus === 'Blocked' ? 'BLOCK_STAFF' : 'UNBLOCK_STAFF',
                'target_name' => $staff->name,
                'details'     => "Changed status from {$oldStatus} to {$newStatus}."
            ]);

            return back()->with('success', "Staff status updated to {$newStatus}.");
        }

        // 场景 B: 普通修改资料 (Update Info)
        // 先记录一下旧数据，方便对比（可选，这里简单记录）
        $this->staffService->updateStaff($staff, $request->validated());

        // [升级版] 记录 Update Log
        \App\Models\AdminActionLog::create([
            'admin_name'  => auth()->user()->name,
            'action_type' => 'UPDATE_STAFF',
            'target_name' => $staff->name,
            'details'     => "Action: Updated Profile Details, " .
                             "Email: {$staff->user->email}, " . // 记录 Email 方便确认身份
                             "New Contact: {$staff->contact_number}, " .
                             "New Dept: {$staff->department}."
        ]);

        return redirect()->route('admin.staff.index')
                         ->with('success', 'Staff details updated successfully.');
    }

    /**
     * 6. 删除逻辑 (Destroy)
     */
    public function destroy(Staff $staff)
    {
        // 1. 获取关联的 User 账号
        $user = $staff->user;
        $adminName = auth()->user()->name ?? 'System Admin';

        // 2. [关键] 制作“记事本”内容
        // 我们把他所有的重要信息拼接成一段字符串
        $logInfo = "Deleted Staff Info: " .
                   "Name: {$staff->name}, " .
                   "Email: " . ($user->email ?? 'N/A') . ", " .
                   "Username: " . ($user->username ?? 'N/A') . ", " .
                   "Contact: {$staff->contact_number}, " .
                   "Department: {$staff->department}.";

        // 3. 存入 Log 表 (永久保存，像 Notepad 一样)
        AdminActionLog::create([
            'admin_name'  => $adminName,
            'action_type' => 'DELETE',
            'target_name' => $staff->name,
            'details'     => $logInfo // 这里存的就是那段像 Text 一样的信息
        ]);

        // 4. 彻底删除 (Hard Delete) - 释放 Email
        if ($user) {
            $user->delete(); 
        }
        $staff->delete();

        return redirect()->route('admin.staff.index')
                         ->with('success', 'Staff deleted. Email is now free to use. History saved to logs.');
    }
}