<?php

namespace Database\Seeders;

use App\Models\CaseRecord;
use App\Models\Composite;
use App\Models\User;
use App\Models\Witness;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
        
        // Create users with specific credentials
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
            $user = User::create($userData);
            
            // Create different cases for each user
            $this->createCasesForUser($user);
        }

        // Seed the feature library
        $this->call([
            FeatureLibrarySeeder::class,
        ]);
        
        $this->command->info('Database seeded successfully with multiple users and their cases!');
    }
    
    /**
     * Create cases for a specific user
     */
    private function createCasesForUser(User $user): void
    {
        // Base case titles - will be customized per user
        $baseCaseTitles = [
            'Property damage report',
            'Identity theft complaint',
            'Pickpocketing incident',
            'Estafa case filed',
            'Robbery case'
        ];
        
        $locations = [
            'Valenzuela - North Expressway',
            'Quezon City - Commonwealth Avenue',
            'Marikina - Riverbanks',
            'Taguig - BGC',
            'Manila - Quiapo',
            'Pasig - C5 Road',
            'Makati - Ayala Avenue',
            'Paranaque - Airport Road',
            'Caloocan - Monumento'
        ];
        
        $statuses = ['open', 'closed', 'pending', 'archived'];
        
        // Customize cases per user to make them unique
        $caseCount = rand(3, 5);
        
        for ($i = 0; $i < $caseCount; $i++) {
            // Create unique title by combining base title with username and random location
            $baseTitle = $baseCaseTitles[array_rand($baseCaseTitles)];
            $location = $locations[array_rand($locations)];
            $title = $baseTitle . ' from ' . $location . ' (' . $user->name . ')';
            
            // Generate a unique reference number based on username and timestamp
            $uniqueId = strtoupper(substr(md5($user->name . time() . rand(1000, 9999)), 0, 7));
            $referenceNumber = 'CASE-' . $uniqueId;
            
            // Create a case with unique title and reference number
            $case = CaseRecord::create([
                'title' => $title,
                'reference_number' => $referenceNumber,
                'status' => $statuses[array_rand($statuses)],
                'description' => 'Case description for ' . $title,
                'incident_type' => ['theft', 'fraud', 'property damage', 'identity theft'][array_rand(['theft', 'fraud', 'property damage', 'identity theft'])],
                'incident_date' => now()->subDays(rand(1, 30)),
                'incident_time' => now()->subDays(rand(1, 30))->setHour(rand(0, 23))->setMinute(rand(0, 59)),
                'location' => $location,
                'notes' => 'Additional notes for ' . $title,
                'user_id' => $user->id,
            ]);
            
            // Create witnesses for the case
            $witnessCount = rand(1, 3);
            for ($j = 0; $j < $witnessCount; $j++) {
                $witness = Witness::create([
                    'case_id' => $case->id,
                    'name' => 'Witness ' . ($j + 1) . ' for ' . $title,
                    'relationship_to_case' => ['victim', 'bystander', 'relative', 'expert'][array_rand(['victim', 'bystander', 'relative', 'expert'])],
                    'contact_number' => '09' . rand(100000000, 999999999),
                    'age' => rand(18, 65),
                    'gender' => ['male', 'female'][array_rand(['male', 'female'])],
                    'address' => 'Address of witness ' . ($j + 1),
                    'reliability_rating' => rand(1, 5),
                    'interview_date' => now()->subDays(rand(1, 15)),
                    'interview_notes' => 'Interview notes for witness ' . ($j + 1),
                ]);
                
                // Create composites for the witness
                $compositeCount = rand(1, 2);
                for ($k = 0; $k < $compositeCount; $k++) {
                    Composite::create([
                        'case_id' => $case->id,
                        'witness_id' => $witness->id,
                        'user_id' => $user->id,
                        'title' => 'Composite ' . ($k + 1) . ' for ' . $witness->name,
                        'description' => 'Description of composite',
                        'canvas_width' => 800,
                        'canvas_height' => 600,
                        'final_image_path' => 'composites/default.jpg',
                        'suspect_gender' => ['male', 'female'][array_rand(['male', 'female'])],
                        'suspect_ethnicity' => ['asian', 'caucasian', 'african', 'hispanic'][array_rand(['asian', 'caucasian', 'african', 'hispanic'])],
                        'suspect_age_range' => ['18-25', '26-35', '36-45', '46-55', '56+'][array_rand(['18-25', '26-35', '36-45', '46-55', '56+'])],
                        'suspect_height' => ['short', 'average', 'tall'][array_rand(['short', 'average', 'tall'])],
                        'suspect_body_build' => ['thin', 'average', 'athletic', 'heavy'][array_rand(['thin', 'average', 'athletic', 'heavy'])],
                        'suspect_additional_notes' => 'Additional details about the suspect',
                    ]);
                }
            }
            
            // Add a small sleep to ensure unique timestamps for reference numbers
            usleep(10000);
        }
    }
    
    /**
     * Truncate tables before seeding
     */
    private function truncateTables(): void
    {
        // Disable foreign key checks to allow truncating tables with foreign keys
        Schema::disableForeignKeyConstraints();
        
        // Tables to truncate
        $tables = ['composites', 'witnesses', 'cases', 'users'];
        
        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->command->info("Table {$table} truncated");
        }
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }
}
