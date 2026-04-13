<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;


    public function run(): void
    {
        $adminUser = User::create([
            'username' => 'admin01', 
            'name' => 'Admin',
            'email' => 'admin@airport.com',
            'password' => Hash::make('password123'), 
            'role' => 'Admin',
        ]);

   
        Staff::create([
            'user_id' => $adminUser->id, 
            'name' => 'Admin',
            'contact_number' => '011-12345678', 
            'status' => 'Active',
            'department' => 'Management',
        ]);

        $this->command->info('Admin account created successfully!');
        $this->command->info('Username: admin01');
        $this->command->info('Password: password123');
        $this->call(StorageSlotSeeder::class);
    }
}
