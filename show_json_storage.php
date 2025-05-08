<?php

// Load Laravel environment
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Import models
use App\Models\CompositeFacialFeature;
use App\Models\Composite;
use App\Models\FacialFeature;

// Function to display data cleanly
function display($title, $data) {
    echo "\n".str_repeat("=", 50)."\n";
    echo $title . "\n";
    echo str_repeat("=", 50)."\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
}

// Get the first composite feature from the database
$feature = CompositeFacialFeature::first();

if (!$feature) {
    echo "No composite facial features found.\n";
    
    // Create a test feature
    $composite = Composite::first();
    $facialFeature = FacialFeature::first();
    
    if (!$composite || !$facialFeature) {
        echo "Error: Missing composite or facial feature.\n";
        exit(1);
    }
    
    // Create a test feature with sample visual adjustments
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
    
    echo "Created a test feature with ID: {$feature->id}\n";
}

// Display how the feature is stored in the database
display("Basic Feature Data", [
    'id' => $feature->id,
    'composite_id' => $feature->composite_id,
    'facial_feature_id' => $feature->facial_feature_id,
    'position_x' => $feature->position_x,
    'position_y' => $feature->position_y,
    'z_index' => $feature->z_index,
    'scale_x' => $feature->scale_x,
    'scale_y' => $feature->scale_y,
    'rotation' => $feature->rotation,
    'opacity' => $feature->opacity,
    'visible' => $feature->visible,
    'locked' => $feature->locked
]);

// Display the visual adjustments as stored in JSON
display("Visual Adjustments (JSON)", $feature->visual_adjustments);

// Get an individual adjustment
$brightness = $feature->getVisualAdjustment('brightness', 0);

display("Individual Adjustment Access", [
    'brightness' => $brightness,
    'brightness_type' => gettype($brightness)
]);

// Update a specific adjustment
$feature->setVisualAdjustment('brightness', 0.5);
$feature->save();

$updatedFeature = CompositeFacialFeature::find($feature->id);
display("Updated Visual Adjustments", $updatedFeature->visual_adjustments);

// Show adding a new property
$updatedFeature->setVisualAdjustment('newEffect', 'sepia');
$updatedFeature->save();

$refreshedFeature = CompositeFacialFeature::find($feature->id);
display("Visual Adjustments with New Property", $refreshedFeature->visual_adjustments);

// Show nested property modification
$customFilter = $feature->getVisualAdjustment('customFilter', []);
if (is_array($customFilter)) {
    // Modify one nested property
    $customFilter['intensity'] = 0.9;
    // Add a new nested property
    $customFilter['name'] = 'vintage';
    
    $feature->setVisualAdjustment('customFilter', $customFilter);
    $feature->save();
    
    $nestedFeature = CompositeFacialFeature::find($feature->id);
    display("Visual Adjustments with Modified Nested Properties", $nestedFeature->visual_adjustments);
}

echo "\nStorage demonstration completed.\n"; 