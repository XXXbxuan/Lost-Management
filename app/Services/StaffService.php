<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffService
{
    public function createStaff(array $data): Staff
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

    public function updateStaff(Staff $staff, array $data): Staff
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

            return $staff->fresh('user');
        });
    }

    public function toggleStatus(Staff $staff): string
    {
        $newStatus = $staff->status === 'Active'
            ? 'Blocked'
            : 'Active';

        $staff->update([
            'status' => $newStatus,
        ]);

        return $newStatus;
    }
}