<?php

namespace App\Livewire\Editor;

use Livewire\Component;
use App\Models\Composite;
use Illuminate\Support\Facades\Log;

class MainCanvas extends Component
{
    public $compositeId;
    public $composite;
    
    // Properties for canvas state
    public $activeTool = 'move'; // move, scale, rotate
    public $zoomLevel = 100;
    public $selectedFeatures = []; // Array to store selected features
    
    protected $listeners = [
        'feature-selected' => 'handleFeatureSelected',
        'direct-update-canvas' => 'handleDirectCanvasUpdate',
        'updateFeaturePosition' => 'updateFeaturePosition',
        'removeFeature' => 'removeFeature',
        'clearFeatures' => 'clearFeatures'
    ];
    
    public function mount($compositeId)
    {
        $this->compositeId = $compositeId;
        $this->composite = Composite::find($compositeId);
        
        Log::info('MainCanvas component mounted', [
            'compositeId' => $this->compositeId,
            'listeners' => $this->listeners
        ]);
    }
    
    public function handleFeatureSelected($featureId)
    {
        // Get the feature details from the database
        $feature = \App\Models\FacialFeature::find($featureId);
        
        if ($feature) {
            Log::info('Feature selected in MainCanvas', [
                'featureId' => $featureId,
                'name' => $feature->name,
                'image_path' => $feature->image_path
            ]);
            
            // Add the feature to the selected features array with default position
            $this->selectedFeatures[] = [
                'id' => $feature->id,
                'image_path' => $feature->image_path,
                'name' => $feature->name,
                'position' => [
                    'x' => 300, // Center of canvas
                    'y' => 300, // Center of canvas
                    'scale' => 1,
                    'rotation' => 0
                ]
            ];
            
            // Debug log
            Log::info('Feature added to canvas', [
                'feature_id' => $feature->id,
                'total_features' => count($this->selectedFeatures)
            ]);
            
            // Dispatch the event to the browser
            $this->dispatch('update-canvas', ['selectedFeatures' => $this->selectedFeatures]);
            
            // Also dispatch direct event for JS
            $this->dispatch('direct-update-canvas', [
                'feature' => end($this->selectedFeatures)
            ]);
        } else {
            Log::error('Feature not found in database', ['featureId' => $featureId]);
        }
    }
    
    public function handleDirectCanvasUpdate($data)
    {
        Log::info('Direct canvas update received', ['data' => $data]);
        
        if (isset($data['feature'])) {
            $feature = $data['feature'];
            
            // Check if this feature is already in our array
            $exists = false;
            foreach ($this->selectedFeatures as $key => $existingFeature) {
                if ($existingFeature['id'] == $feature['id']) {
                    $exists = true;
                    // Update the existing feature
                    $this->selectedFeatures[$key] = $feature;
                    break;
                }
            }
            
            // If it doesn't exist, add it
            if (!$exists) {
                $this->selectedFeatures[] = $feature;
            }
            
            Log::info('Feature added/updated via direct update', [
                'feature_id' => $feature['id'],
                'total_features' => count($this->selectedFeatures)
            ]);
            
            // Re-dispatch the event to ensure JS picks it up
            $this->dispatch('update-canvas', ['selectedFeatures' => $this->selectedFeatures]);
        }
    }
    
    public function updateFeaturePosition($data)
    {
        Log::info('Updating feature position', ['data' => $data]);
        
        // Find and update the position of a feature in the selectedFeatures array
        foreach ($this->selectedFeatures as $key => $feature) {
            if ($feature['id'] == $data['featureId']) {
                $this->selectedFeatures[$key]['position'] = $data['position'];
                break;
            }
        }
    }
    
    public function removeFeature($featureId)
    {
        // Remove a feature from the selectedFeatures array
        $this->selectedFeatures = array_filter($this->selectedFeatures, function($feature) use ($featureId) {
            return $feature['id'] != $featureId;
        });
        
        // Reindex the array
        $this->selectedFeatures = array_values($this->selectedFeatures);
        
        Log::info('Feature removed from canvas', [
            'feature_id' => $featureId,
            'remaining_features' => count($this->selectedFeatures)
        ]);
    }
    
    public function clearFeatures()
    {
        // Clear all features from the canvas
        $this->selectedFeatures = [];
        Log::info('All features cleared from canvas');
    }
    
    public function setTool($tool)
    {
        $this->activeTool = $tool;
        Log::info('Tool set', ['tool' => $tool]);
    }
    
    public function zoomIn()
    {
        $this->zoomLevel = min($this->zoomLevel + 10, 200);
    }
    
    public function zoomOut()
    {
        $this->zoomLevel = max($this->zoomLevel - 10, 50);
    }
    
    public function resetZoom()
    {
        $this->zoomLevel = 100;
    }
    
    public function render()
    {
        return view('livewire.editor.main-canvas');
    }
}
