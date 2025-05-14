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
            'Vandalism at public park',
            'Shoplifting at retail store',
            'Theft of unattended backpack',
            'Commercial break-in reported',
            'Vehicle parts stolen overnight',
            'Attempted identity theft online',
            'Credit card fraud complaint',
            'Public disturbance call',
            'Online harassment investigation',
            'Minor hit and run incident',
            'Illegal dumping complaint',
            'Lost property investigation'
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
            'Caloocan - Monumento',
            'Mandaluyong - Shaw Boulevard',
            'Pasay - EDSA Extension',
            'San Juan - Greenhills Shopping Center'
        ];
        
        $incident_types = [
            'vandalism', 'shoplifting', 'theft', 'burglary', 'vehicle crime', 
            'fraud', 'public order', 'cybercrime', 'traffic incident', 'environmental'
        ];

        $statuses = ['open', 'closed', 'pending', 'archived'];
        $totalCasesCreated = 0;

        foreach ($users as $user) {
            $caseCount = rand(5, 8); // Increased number of cases
            
            for ($i = 0; $i < $caseCount; $i++) {
                $baseTitle = $baseCaseTitles[array_rand($baseCaseTitles)];
                $location = $locations[array_rand($locations)];
                $title = $baseTitle . ' in ' . $location . ' (User: ' . $user->name . ')';
                
                $uniqueId = strtoupper(substr(md5($user->name . time() . rand(1000, 9999) . $i), 0, 7));
                $referenceNumber = 'CASE-' . $uniqueId;
                
                CaseRecord::create([
                    'title' => $title,
                    'reference_number' => $referenceNumber,
                    'status' => $statuses[array_rand($statuses)],
                    'description' => 'Details for case: ' . $title,
                    'incident_type' => $incident_types[array_rand($incident_types)],
                    'incident_date' => now()->subDays(rand(1, 60)),
                    'incident_time' => now()->subHours(rand(1, 1440))->format('H:i:s'),
                    'location' => $location,
                    'notes' => 'Initial notes for case: ' . $title,
                    'user_id' => $user->id,
                ]);
                $totalCasesCreated++;
                usleep(10000); 
            }
        }
        $this->command->info($totalCasesCreated . ' cases seeded for ' . $users->count() . ' users.');
    }
}
