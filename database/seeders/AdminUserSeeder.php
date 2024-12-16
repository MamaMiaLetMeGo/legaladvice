<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Primary Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'email_verified_at' => now(),
            'role' => User::ROLE_ADMIN,
        ]);
    }
}