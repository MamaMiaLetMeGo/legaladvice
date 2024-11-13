<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminUsers = [
            [
                'name' => 'Primary Admin',
                'email' => 'admin@example.com',
                'password' => 'password123',
            ],
            [
                'name' => 'Secondary Admin',
                'email' => 'admin2@example.com',
                'password' => 'password123',
            ],
        ];

        foreach ($adminUsers as $admin) {
            User::firstOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make($admin['password']),
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('Admin users created or updated successfully.');
    }
}