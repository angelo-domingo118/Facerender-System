<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'User Tester',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Feint',
                'email' => 'feint@example.com',
                'password' => Hash::make('password'),
            ],
        ];
        
        foreach ($users as $userData) {
            User::create($userData);
        }

        $this->command->info(count($users) . ' initial users seeded.');
    }
}
