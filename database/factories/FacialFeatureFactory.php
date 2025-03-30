<?php

namespace Database\Factories;

use App\Models\FacialFeature;
use App\Models\FeatureCategory;
use App\Models\FeatureType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FacialFeature>
 */
class FacialFeatureFactory extends Factory
{
    protected $model = FacialFeature::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get or create a feature type
        $featureType = FeatureType::factory()->create();
        
        // Get or create a feature category
        $featureCategory = FeatureCategory::factory()
            ->forFeatureType($featureType)
            ->create();
        
        // Generate a unique code for this feature
        $featureCode = strtoupper(substr($featureType->name, 0, 3)) . fake()->unique()->numberBetween(100, 999);
        
        // Default name based on category and type
        $name = "{$featureCategory->name} {$featureType->name} " . substr($featureCode, -3);
        
        // Try to find an actual image path for this feature type
        $possibleImagePaths = $this->getImagePaths($featureType->name);
        $gender = fake()->randomElement(['male', 'female']);
        $imagePath = null;
        
        if (!empty($possibleImagePaths)) {
            // Filter by gender if possible
            $genderSuffix = ($gender === 'male') ? '-m.png' : '-f.png';
            $genderFiltered = array_filter($possibleImagePaths, function($path) use ($genderSuffix) {
                return Str::endsWith($path, $genderSuffix);
            });
            
            if (!empty($genderFiltered)) {
                $imagePath = fake()->randomElement($genderFiltered);
            } else {
                $imagePath = fake()->randomElement($possibleImagePaths);
            }
        } else {
            // Fallback default path format
            $imagePath = "features/{$featureType->name}/default.png";
        }
        
        return [
            'feature_type_id' => $featureType->id,
            'feature_category_id' => $featureCategory->id,
            'feature_code' => $featureCode,
            'name' => $name,
            'image_path' => $imagePath,
            'gender' => $gender,
        ];
    }
    
    /**
     * Get available image paths for a feature type
     * 
     * @param string $featureType
     * @return array
     */
    private function getImagePaths(string $featureType): array
    {
        $paths = [];
        $directory = "features/{$featureType}";
        
        if (Storage::disk('public')->exists($directory)) {
            $files = Storage::disk('public')->files($directory);
            $paths = array_map(function($path) {
                return str_replace('public/', '', $path);
            }, $files);
        }
        
        return $paths;
    }
    
    /**
     * Configure the model factory to use an existing feature type.
     */
    public function forFeatureType(FeatureType $featureType)
    {
        return $this->state(function (array $attributes) use ($featureType) {
            return [
                'feature_type_id' => $featureType->id,
            ];
        });
    }
    
    /**
     * Configure the model factory to use an existing feature category.
     */
    public function forFeatureCategory(FeatureCategory $featureCategory)
    {
        return $this->state(function (array $attributes) use ($featureCategory) {
            return [
                'feature_type_id' => $featureCategory->feature_type_id,
                'feature_category_id' => $featureCategory->id,
            ];
        });
    }
    
    /**
     * Configure the model factory to use a specific gender.
     */
    public function gender(string $gender)
    {
        return $this->state(function (array $attributes) use ($gender) {
            return [
                'gender' => in_array($gender, ['male', 'female']) ? $gender : fake()->randomElement(['male', 'female']),
            ];
        });
    }
}
