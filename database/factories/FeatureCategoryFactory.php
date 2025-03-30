<?php

namespace Database\Factories;

use App\Models\FeatureCategory;
use App\Models\FeatureType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FeatureCategory>
 */
class FeatureCategoryFactory extends Factory
{
    protected $model = FeatureCategory::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Define categories for each feature type
        $categoriesByType = [
            'eyes' => ['Almond', 'Round', 'Hooded', 'Monolid', 'Upturned', 'Downturned', 'Wide-set', 'Close-set'],
            'eyebrows' => ['Thick', 'Thin', 'Arched', 'Straight', 'Bushy', 'Sparse'],
            'nose' => ['Flat', 'Asian', 'Snub', 'Roman', 'Aquiline', 'Bulbous', 'Thin'],
            'mouth' => ['Thin', 'Full', 'Wide', 'Heart-shaped', 'Bow-shaped', 'Downturned'],
            'ears' => ['Small', 'Large', 'Protruding', 'Close-set', 'Round', 'Pointed'],
            'hair' => ['Short', 'Medium', 'Long', 'Curly', 'Straight', 'Wavy', 'Bald'],
            'face' => ['Round', 'Oval', 'Square', 'Heart', 'Diamond', 'Rectangular', 'Triangular'],
            'neck' => ['Short', 'Long', 'Medium', 'Thick', 'Thin'],
            'accessories' => ['Glasses', 'Hats', 'Jewelry', 'Scarves', 'Facial hair', 'Piercings']
        ];
        
        // Get a random feature type or use the one provided
        $featureType = FeatureType::factory()->create();
        $typeName = $featureType->name;
        
        // Get available categories for this type
        $categories = $categoriesByType[$typeName] ?? ['Default'];
        
        return [
            'feature_type_id' => $featureType->id,
            'name' => fake()->randomElement($categories),
        ];
    }
    
    /**
     * Configure the model factory to use an existing feature type.
     *
     * @param \App\Models\FeatureType $featureType
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forFeatureType(FeatureType $featureType)
    {
        return $this->state(function (array $attributes) use ($featureType) {
            return [
                'feature_type_id' => $featureType->id,
            ];
        });
    }
}
