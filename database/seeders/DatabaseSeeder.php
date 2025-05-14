<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear the database first
        $this->truncateTables();
        
        $this->call([
            UserSeeder::class,
            CaseRecordSeeder::class,
            WitnessSeeder::class,
            CompositeSeeder::class,
            FeatureLibrarySeeder::class,
            AdminUserSeeder::class,
        ]);
        
        $this->command->info('Database seeded successfully with structured seeders!');
    }
    
    /**
     * Truncate tables before seeding
     */
    private function truncateTables(): void
    {
        // Disable foreign key checks to allow truncating tables with foreign keys
        Schema::disableForeignKeyConstraints();
        
        // Tables to truncate
        // Ensure 'users' is truncated in a way that doesn't break AdminUserSeeder if it relies on no users existing
        // Or, if AdminUserSeeder creates a specific admin, ensure it's idempotent or handled.
        // For now, assuming UserSeeder handles general users and AdminUserSeeder handles a specific admin.
        $tables = ['composites', 'witnesses', 'cases', 'users']; // Order might matter for truncation due to FKs, though disableForeignKeyConstraints helps.
        
        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->command->info("Table {$table} truncated");
        }
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }
}
