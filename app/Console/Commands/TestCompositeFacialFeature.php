<?php

namespace App\Console\Commands;

use App\Models\Composite;
use App\Models\CompositeFacialFeature;
use App\Models\FacialFeature;
use Illuminate\Console\Command;

class TestCompositeFacialFeature extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:feature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test inserting and retrieving a composite facial feature with JSON data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Composite Facial Feature with JSON Visual Adjustments');
        
        // 1. Find or create a test composite
        $composite = Composite::first();
        
        if (!$composite) {
            $this->info('No composite found. Creating a test composite...');
            $composite = Composite::create([
                'title' => 'Test Composite',
                'description' => 'Created for testing feature storage',
                'canvas_width' => 600,
                'canvas_height' => 600,
                'suspect_gender' => 'male',
            ]);
        }
        
        $this->info("Using composite ID: {$composite->id}");
        
        // 2. Find a facial feature to use
        $facialFeature = FacialFeature::first();
        
        if (!$facialFeature) {
            $this->error('No facial features found in the database. Please run the seeders first.');
            return 1;
        }
        
        $this->info("Using facial feature: {$facialFeature->name} (ID: {$facialFeature->id})");
        
        // 3. Create a composite facial feature with JSON visual adjustments
        $visualAdjustments = [
            'brightness' => 0.2,
            'contrast' => 1.2,
            'saturation' => 0.9,
            'sharpness' => 0.1,
            'feathering' => 0.05,
            'skinTone' => 0.3,
            'customFilter' => [
                'enabled' => true,
                'intensity' => 0.7
            ]
        ];
        
        $feature = CompositeFacialFeature::create([
            'composite_id' => $composite->id,
            'facial_feature_id' => $facialFeature->id,
            'position_x' => 100,
            'position_y' => 150,
            'z_index' => 1,
            'scale_x' => 1.2,
            'scale_y' => 1.2,
            'rotation' => 15,
            'opacity' => 0.85,
            'visible' => true,
            'locked' => false,
            'visual_adjustments' => $visualAdjustments,
        ]);
        
        $this->info("Created composite facial feature with ID: {$feature->id}");
        
        // 4. Retrieve the feature to show how it's stored
        $retrievedFeature = CompositeFacialFeature::find($feature->id);
        
        $this->info("Retrieved feature data:");
        $this->info("Position: ({$retrievedFeature->position_x}, {$retrievedFeature->position_y})");
        $this->info("Scale: ({$retrievedFeature->scale_x}, {$retrievedFeature->scale_y})");
        $this->info("Rotation: {$retrievedFeature->rotation}°");
        $this->info("Opacity: {$retrievedFeature->opacity}");
        
        // 5. Show visual adjustments JSON
        $this->info("Visual Adjustments (stored as JSON):");
        $this->info(json_encode($retrievedFeature->visual_adjustments, JSON_PRETTY_PRINT));
        
        // 6. Test accessing specific adjustment
        $brightness = $retrievedFeature->getVisualAdjustment('brightness', 0);
        $customFilter = $retrievedFeature->getVisualAdjustment('customFilter', []);
        
        $this->info("Accessing specific adjustment:");
        $this->info("Brightness: {$brightness}");
        $this->info("Custom Filter: " . json_encode($customFilter));
        
        // 7. Test updating a specific adjustment
        $retrievedFeature->setVisualAdjustment('brightness', 0.5);
        $retrievedFeature->save();
        
        $this->info("Updated brightness to 0.5");
        
        // 8. Retrieve again to show the update
        $updatedFeature = CompositeFacialFeature::find($feature->id);
        $this->info("After update - Visual Adjustments:");
        $this->info(json_encode($updatedFeature->visual_adjustments, JSON_PRETTY_PRINT));
        
        return 0;
    }
}
