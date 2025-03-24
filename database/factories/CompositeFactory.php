<?php

namespace Database\Factories;

use App\Models\CaseRecord;
use App\Models\Composite;
use App\Models\User;
use App\Models\Witness;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Composite>
 */
class CompositeFactory extends Factory
{
    protected $model = Composite::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genders = ['Male', 'Female', 'Other'];
        $ethnicities = [
            'Filipino',
            'Chinese-Filipino',
            'Spanish-Filipino',
            'American-Filipino',
            'Japanese-Filipino',
            'Korean-Filipino',
            'Indian-Filipino',
            'Middle Eastern-Filipino'
        ];
        $ageRanges = [
            '18-25',
            '26-35',
            '36-45',
            '46-55',
            '56-65',
            '65+'
        ];
        $heights = [
            '4\'8" - 4\'10"',
            '4\'11" - 5\'1"',
            '5\'2" - 5\'4"',
            '5\'5" - 5\'7"',
            '5\'8" - 5\'10"',
            '5\'11" - 6\'1"',
            '6\'2" and above'
        ];
        $bodyBuilds = [
            'Slim',
            'Average',
            'Athletic',
            'Stocky',
            'Overweight',
            'Muscular'
        ];

        return [
            'case_id' => CaseRecord::factory(),
            'witness_id' => Witness::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'canvas_width' => 800,
            'canvas_height' => 600,
            'final_image_path' => null,
            'suspect_gender' => fake()->randomElement($genders),
            'suspect_ethnicity' => fake()->randomElement($ethnicities),
            'suspect_age_range' => fake()->randomElement($ageRanges),
            'suspect_height' => fake()->randomElement($heights),
            'suspect_body_build' => fake()->randomElement($bodyBuilds),
            'suspect_additional_notes' => fake()->paragraph(),
            'is_pinned' => fake()->boolean(20), // 20% chance of being pinned
        ];
    }
}
