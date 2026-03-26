<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;//(del)
use Illuminate\Database\Seeder;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. 创建 Admin 的登录账号
        $adminUser = User::create([
            'username' => 'admin01', // 这是我们刚加的字段
            'name' => 'Admin',
            'email' => 'admin@airport.com',
            'password' => Hash::make('password123'), // 初始密码，一定要 Hash
            'role' => 'Admin',
        ]);

        // 2. 创建 Admin 的员工档案 (关联起来)
        Staff::create([
            'user_id' => $adminUser->id, // 关键：把 user_id 填进去，这就关联上了
            'name' => 'Admin',
            'contact_number' => '011-12345678', // 这是我们刚加的字段
            'status' => 'Active',
            'department' => 'Management',
        ]);

        $this->command->info('Admin account created successfully!');
        $this->command->info('Username: admin01');
        $this->command->info('Password: password123');
        $this->call(StorageSlotSeeder::class);
    }
}
