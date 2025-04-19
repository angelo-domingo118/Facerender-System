<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'], // Unique identifier to prevent duplicates
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // Change 'password' to a secure password
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
