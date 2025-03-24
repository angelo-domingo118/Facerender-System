<?php

namespace Database\Factories;

use App\Models\CaseRecord;
use App\Models\Witness;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Witness>
 */
class WitnessFactory extends Factory
{
    protected $model = Witness::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genders = ['Male', 'Female', 'Other'];
        $relationships = [
            'Victim',
            'Bystander',
            'Security Guard',
            'Store Owner',
            'Employee',
            'Neighbor',
            'Friend',
            'Family Member',
            'Passerby',
            'Delivery Person'
        ];

        return [
            'case_id' => CaseRecord::factory(),
            'name' => fake()->name(),
            'age' => fake()->numberBetween(18, 80),
            'gender' => fake()->randomElement($genders),
            'contact_number' => '+63' . fake()->numberBetween(9000000000, 9999999999),
            'address' => fake()->address(),
            'relationship_to_case' => fake()->randomElement($relationships),
            'reliability_rating' => fake()->numberBetween(1, 5),
            'interview_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'interview_notes' => fake()->paragraph(),
        ];
    }
}
