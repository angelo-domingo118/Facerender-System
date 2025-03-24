<?php

namespace Database\Factories;

use App\Models\CaseRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CaseRecord>
 */
class CaseRecordFactory extends Factory
{
    protected $model = CaseRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $incidentTypes = [
            'Theft',
            'Assault',
            'Robbery',
            'Vandalism',
            'Fraud',
            'Harassment',
            'Trespassing',
            'Property Damage',
            'Identity Theft',
            'Cybercrime'
        ];

        $locations = [
            'Manila',
            'Quezon City',
            'Makati',
            'Pasig',
            'Taguig',
            'Pasay',
            'Mandaluyong',
            'San Juan',
            'Manila Bay Area',
            'Bonifacio Global City'
        ];

        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'reference_number' => 'CASE-' . fake()->numberBetween(1000, 9999),
            'status' => fake()->randomElement(['open', 'closed', 'pending', 'archived']),
            'incident_type' => fake()->randomElement($incidentTypes),
            'incident_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'incident_time' => fake()->time(),
            'location' => fake()->randomElement($locations),
            'notes' => fake()->paragraph(),
            'user_id' => User::factory(),
            'is_pinned' => fake()->boolean(20), // 20% chance of being pinned
        ];
    }
}
