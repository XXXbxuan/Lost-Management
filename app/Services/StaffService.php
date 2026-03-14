<?php

namespace App\Services;

use App\Models\User;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffService
{
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

    public function toggleStatus(Staff $staff)
    {
        $newStatus = ($staff->status === 'Active') ? 'Blocked' : 'Active';
        $staff->update(['status' => $newStatus]);
        return $newStatus;
    }
}