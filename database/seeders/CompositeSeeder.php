<?php

namespace Database\Seeders;

use App\Models\Witness;
use App\Models\Composite;
use Illuminate\Database\Seeder;

class CompositeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Eager load cases and users to prevent N+1 queries when accessing $witness->caseRecord->user_id
        $witnesses = Witness::with('caseRecord.user')->get(); 
        if ($witnesses->isEmpty()) {
            $this->command->info('No witnesses found to create composites for. Skipping CompositeSeeder.');
            return;
        }

        $totalCompositesCreated = 0;

        foreach ($witnesses as $witness) {
            if (!$witness->caseRecord || !$witness->caseRecord->user) {
                // Skip if a witness somehow doesn't have a case or a user associated with the case
                // This can happen if data integrity is compromised or if seeders run in an unexpected order previously
                $this->command->warn("Skipping composite for witness ID {$witness->id} due to missing case or user data.");
                continue;
            }

            $compositeCount = rand(1, 2);
            for ($k = 0; $k < $compositeCount; $k++) {
                Composite::create([
                    'case_id' => $witness->case_id,
                    'witness_id' => $witness->id,
                    'user_id' => $witness->caseRecord->user_id,
                    'title' => 'Composite ' . ($k + 1) . ' by witness ' . $witness->name . ' for case ' . $witness->caseRecord->reference_number,
                    'description' => 'Description of composite sketch based on witness testimony.',
                    'canvas_width' => 800,
                    'canvas_height' => 600,
                    'final_image_path' => 'composites/default.jpg', // Placeholder
                    'suspect_gender' => ['male', 'female'][array_rand(['male', 'female'])],
                    'suspect_ethnicity' => ['asian', 'caucasian', 'african', 'hispanic'][array_rand(['asian', 'caucasian', 'african', 'hispanic'])],
                    'suspect_age_range' => ['18-25', '26-35', '36-45', '46-55', '56+'][array_rand(['18-25', '26-35', '36-45', '46-55', '56+'])],
                    'suspect_height' => ['short', 'average', 'tall'][array_rand(['short', 'average', 'tall'])],
                    'suspect_body_build' => ['thin', 'average', 'athletic', 'heavy'][array_rand(['thin', 'average', 'athletic', 'heavy'])],
                    'suspect_additional_notes' => 'Additional details about the suspect based on witness recollection.',
                ]);
                $totalCompositesCreated++;
            }
        }
        $this->command->info($totalCompositesCreated . ' composites seeded for ' . $witnesses->count() . ' witnesses.');
    }
}
