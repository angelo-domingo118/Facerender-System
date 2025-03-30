<?php

namespace Database\Factories;

use App\Models\FeatureType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeatureType>
 */
class FeatureTypeFactory extends Factory
{
    protected $model = FeatureType::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $featureTypes = [
            'eyes',
            'eyebrows',
            'nose',
            'mouth',
            'ears',
            'hair',
            'face',
            'neck',
            'accessories'
        ];
        
        return [
            'name' => fake()->unique()->randomElement($featureTypes),
        ];
    }
}
