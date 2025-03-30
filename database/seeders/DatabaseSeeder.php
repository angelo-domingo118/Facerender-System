<?php

namespace Database\Seeders;

use App\Models\CaseRecord;
use App\Models\Composite;
use App\Models\User;
use App\Models\Witness;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user with simple credentials
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create 5 cases for the test user
        CaseRecord::factory(5)
            ->create(['user_id' => $user->id])
            ->each(function ($case) {
                // Create 2-3 witnesses for each case
                $witnesses = Witness::factory(fake()->numberBetween(2, 3))
                    ->create(['case_id' => $case->id]);

                // Create 1-2 composites for each witness
                $witnesses->each(function ($witness) use ($case) {
                    Composite::factory(fake()->numberBetween(1, 2))
                        ->create([
                            'case_id' => $case->id,
                            'witness_id' => $witness->id,
                            'user_id' => $case->user_id,
                        ]);
                });
            });

        $this->call([
            FeatureLibrarySeeder::class,
        ]);
    }
}
