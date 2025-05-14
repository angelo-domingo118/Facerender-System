<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CaseRecord;
use Illuminate\Database\Seeder;

class CaseRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->info('No users found to create cases for. Skipping CaseRecordSeeder.');
            return;
        }

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
        $totalCasesCreated = 0;

        foreach ($users as $user) {
            $caseCount = rand(2, 4); // Adjusted from original 3-5 to vary it a bit
            
            for ($i = 0; $i < $caseCount; $i++) {
                $baseTitle = $baseCaseTitles[array_rand($baseCaseTitles)];
                $location = $locations[array_rand($locations)];
                $title = $baseTitle . ' from ' . $location . ' (' . $user->name . ')';
                
                $uniqueId = strtoupper(substr(md5($user->name . time() . rand(1000, 9999) . $i), 0, 7));
                $referenceNumber = 'CASE-' . $uniqueId;
                
                CaseRecord::create([
                    'title' => $title,
                    'reference_number' => $referenceNumber,
                    'status' => $statuses[array_rand($statuses)],
                    'description' => 'Case description for ' . $title,
                    'incident_type' => ['theft', 'fraud', 'property damage', 'identity theft'][array_rand(['theft', 'fraud', 'property damage', 'identity theft'])],
                    'incident_date' => now()->subDays(rand(1, 30)),
                    'incident_time' => now()->subHours(rand(1, 720))->format('H:i:s'), // Simpler time generation
                    'location' => $location,
                    'notes' => 'Additional notes for ' . $title,
                    'user_id' => $user->id,
                ]);
                $totalCasesCreated++;
                usleep(10000); // To ensure unique timestamps for reference numbers if generated rapidly
            }
        }
        $this->command->info($totalCasesCreated . ' cases seeded for ' . $users->count() . ' users.');
    }
}
