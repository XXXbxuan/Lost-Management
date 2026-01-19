<?php

namespace App\Services; // [必须] 确保这里是 App\Services

use App\Models\User;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffService // [必须] 类名必须和文件名 StaffService 一模一样
{
    /**
     * 创建新员工
     */
    public function createStaff(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'username' => $data['username'],
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
            ]);

            return Staff::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'contact_number' => $data['contact_number'],
                'department' => $data['department'] ?? 'Terminal Operations',
                'status' => 'Active',
            ]);
        });
    }

    /**
     * 更新员工资料
     */
    public function updateStaff(Staff $staff, array $data)
    {
        return DB::transaction(function () use ($staff, $data) {
            $userData = [
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'role' => $data['role'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $staff->user->update($userData);

            $staff->update([
                'name' => $data['name'],
                'contact_number' => $data['contact_number'],
                'department' => $data['department'] ?? $staff->department,
            ]);

            return $staff;
        });
    }

    /**
     * 封禁/解封员工
     */
    public function toggleStatus(Staff $staff)
    {
        $newStatus = ($staff->status === 'Active') ? 'Blocked' : 'Active';
        $staff->update(['status' => $newStatus]);
        return $newStatus;
    }
}