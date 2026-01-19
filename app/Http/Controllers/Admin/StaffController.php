<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Services\StaffService;

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
        // 验证通过的数据会自动传入 validated()
        $this->staffService->createStaff($request->validated());

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
        // 检查是不是要切换状态 (Block/Unblock)
        if ($request->has('toggle_status')) {
            $newStatus = $this->staffService->toggleStatus($staff);
            return back()->with('success', "Staff status updated to {$newStatus}.");
        }

        // 普通更新资料
        $this->staffService->updateStaff($staff, $request->validated());

        return redirect()->route('admin.staff.index')
                         ->with('success', 'Staff details updated successfully.');
    }

    /**
     * 6. 删除逻辑 (Destroy)
     */
    public function destroy(Staff $staff)
    {
        $staff->delete(); // 软删除
        return redirect()->route('admin.staff.index')
                         ->with('success', 'Staff account deleted successfully.');
    }
}