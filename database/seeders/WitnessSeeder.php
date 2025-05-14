<?php

namespace Database\Seeders;

use App\Models\CaseRecord;
use App\Models\Witness;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WitnessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cases = CaseRecord::all();
        if ($cases->isEmpty()) {
            $this->command->info('No cases found to create witnesses for. Skipping WitnessSeeder.');
            return;
        }

        $totalWitnessesCreated = 0;

        foreach ($cases as $case) {
            $witnessCount = rand(1, 3);
            for ($j = 0; $j < $witnessCount; $j++) {
                Witness::create([
                    'case_id' => $case->id,
                    'name' => 'Witness ' . ($j + 1) . ' for case ' . $case->reference_number,
                    'relationship_to_case' => ['victim', 'bystander', 'relative', 'expert'][array_rand(['victim', 'bystander', 'relative', 'expert'])],
                    'contact_number' => '09' . rand(100000000, 999999999),
                    'age' => rand(18, 65),
                    'gender' => ['male', 'female'][array_rand(['male', 'female'])],
                    'address' => 'Address of witness ' . ($j + 1) . ' for case ' . $case->reference_number,
                    'interview_date' => now()->subDays(rand(1, 15)),
                    'interview_notes' => 'Interview notes for witness ' . ($j + 1) . ' related to case ' . $case->reference_number,
                ]);
                $totalWitnessesCreated++;
            }
        }
        $this->command->info($totalWitnessesCreated . ' witnesses seeded for ' . $cases->count() . ' cases.');
    }
}
