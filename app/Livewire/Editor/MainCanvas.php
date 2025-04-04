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
    public $moveEnabled = true; // Flag to control if objects can be moved on the canvas
    
    protected $listeners = [
        'feature-selected' => 'handleFeatureSelected',
        'direct-update-canvas' => 'handleDirectCanvasUpdate',
        'updateFeaturePosition' => 'updateFeaturePosition',
        'removeFeature' => 'removeFeature',
        'remove-feature-requested' => 'removeFeature',
        'clearFeatures' => 'clearFeatures',
        'layer-visibility-changed' => 'handleLayerVisibilityChange',
        'layer-opacity-changed' => 'handleLayerOpacityChange',
        'select-feature-on-canvas' => 'handleSelectFeatureOnCanvas',
        'layers-reordered' => 'handleLayersReordered'
    ];
    
    public function mount($compositeId)
    {
        $this->compositeId = $compositeId;
        $this->composite = Composite::find($compositeId);
        
        Log::info('MainCanvas component mounted', [
            'compositeId' => $this->compositeId,
            'listeners' => $this->listeners
        ]);
        
        // Initially, notify the layer panel of the current features (empty at start)
        $this->dispatch('layers-updated', $this->selectedFeatures);
    }
    
    public function handleFeatureSelected($featureId)
    {
        // Get the feature details from the database
        $feature = \App\Models\FacialFeature::find($featureId);
        
        if ($feature) {
            Log::info('Feature selected in MainCanvas', [
                'featureId' => $featureId,
                'name' => $feature->name,
                'image_path' => $feature->image_path,
                'feature_type' => $feature->feature_type_id
            ]);
            
            // Check if we already have a feature of this type and remove it
            $featureTypeId = $feature->feature_type_id;
            
            // Find and remove features of the same type
            $this->selectedFeatures = array_values(array_filter($this->selectedFeatures, function($existingFeature) use ($featureTypeId) {
                $keepFeature = !isset($existingFeature['feature_type']) || $existingFeature['feature_type'] != $featureTypeId;
                
                if (!$keepFeature) {
                    Log::info('Removing existing feature of the same type', [
                        'removed_feature_id' => $existingFeature['id'],
                        'feature_type' => $featureTypeId
                    ]);
                }
                
                return $keepFeature;
            }));
            
            // Add the feature to the selected features array with default position
            $this->selectedFeatures[] = [
                'id' => $feature->id,
                'image_path' => $feature->image_path,
                'name' => $feature->name,
                'feature_type' => $feature->feature_type_id,
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
                'feature_type' => $feature->feature_type_id,
                'total_features' => count($this->selectedFeatures)
            ]);
            
            // Dispatch the event to the browser (with features array)
            $this->dispatch('update-canvas', ['selectedFeatures' => $this->selectedFeatures]);
            
            // After updating features, notify layer panel
            // Send layers in canvas order - last item in array = top most layer visually
            $this->dispatch('layers-updated', $this->selectedFeatures);

            // Dispatch active feature IDs to the feature library
            $this->dispatchActiveFeatureIds();

        } else {
            Log::error('Feature not found in database', ['featureId' => $featureId]);
        }
    }
    
    /**
     * Helper function to get the defined sort order for a feature type.
     */
    private function getFeatureOrder(array $feature): int
    {
        // REMOVED: Layer ordering logic
        return 100; // Return a constant value since sorting is no longer used
    }
    
    public function handleDirectCanvasUpdate($data)
    {
        Log::info('Direct canvas update received', ['data' => $data]);
        
        if (isset($data['feature'])) {
            $feature = $data['feature'];
            
            // Check if feature has a type and remove other features of the same type
            if (isset($feature['feature_type'])) {
                $featureTypeId = $feature['feature_type'];
                
                // Filter out existing features of the same type
                $this->selectedFeatures = array_values(array_filter($this->selectedFeatures, function($existingFeature) use ($featureTypeId, $feature) {
                    // Keep this feature if it's a different type OR if it's the same feature ID
                    $differentType = !isset($existingFeature['feature_type']) || $existingFeature['feature_type'] != $featureTypeId;
                    $sameFeature = $existingFeature['id'] == $feature['id'];
                    
                    $keepFeature = $differentType || $sameFeature;
                    
                    if (!$keepFeature) {
                        Log::info('Removing existing feature of the same type via direct update', [
                            'removed_feature_id' => $existingFeature['id'],
                            'feature_type' => $featureTypeId,
                            'new_feature_id' => $feature['id']
                        ]);
                    }
                    
                    return $keepFeature;
                }));
            }
            
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
                'feature_type' => $feature['feature_type'] ?? 'not set',
                'total_features' => count($this->selectedFeatures)
            ]);
            
            // Re-dispatch the event to ensure JS picks it up
            $this->dispatch('update-canvas', ['selectedFeatures' => $this->selectedFeatures]);

            // After updating features, notify layer panel
            // Send layers in canvas order - last item in array = top most layer visually
            $this->dispatch('layers-updated', $this->selectedFeatures);

            // Dispatch active feature IDs to the feature library
            $this->dispatchActiveFeatureIds();
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

        // After updating position, notify layer panel of the change
        $this->dispatch('layers-updated', $this->selectedFeatures);
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

        // Notify layer panel that a feature was removed
        $this->dispatch('feature-removed', $featureId);

        // Dispatch updated active feature IDs
        $this->dispatchActiveFeatureIds();
    }
    
    public function clearFeatures()
    {
        // Clear all features from the canvas
        $this->selectedFeatures = [];
        Log::info('All features cleared from canvas');

        // Notify layer panel that all features were cleared
        $this->dispatch('clear-layers');

        // Dispatch updated active feature IDs (which will be empty)
        $this->dispatchActiveFeatureIds();
    }
    
    /**
     * Helper method to dispatch the list of active feature IDs.
     */
    protected function dispatchActiveFeatureIds()
    {
        $activeIds = array_column($this->selectedFeatures, 'id');
        $this->dispatch('active-features-updated', $activeIds);
        Log::info('Dispatched active-features-updated', ['active_ids' => $activeIds]);
    }
    
    public function setTool($tool)
    {
        $this->activeTool = $tool;
        Log::info('Tool set', ['tool' => $tool]);
    }
    
    public function zoomIn($scale = null)
    {
        if ($scale !== null) {
            $this->zoomLevel = round($scale * 100);
        } else {
            $this->zoomLevel = min($this->zoomLevel + 10, 300);
        }
        Log::info('Canvas zoomed in', ['zoomLevel' => $this->zoomLevel]);
        $this->dispatch('zoom-level-changed', ['zoomLevel' => $this->zoomLevel]);
    }
    
    public function zoomOut($scale = null)
    {
        if ($scale !== null) {
            $this->zoomLevel = round($scale * 100);
        } else {
            $this->zoomLevel = max($this->zoomLevel - 10, 50);
        }
        Log::info('Canvas zoomed out', ['zoomLevel' => $this->zoomLevel]);
        $this->dispatch('zoom-level-changed', ['zoomLevel' => $this->zoomLevel]);
    }
    
    public function resetZoom()
    {
        $this->zoomLevel = 100;
        Log::info('Canvas zoom reset', ['zoomLevel' => $this->zoomLevel]);
        $this->dispatch('zoom-level-changed', ['zoomLevel' => $this->zoomLevel]);
    }
    
    /**
     * Handle layer visibility change from LayerPanel
     */
    public function handleLayerVisibilityChange($data)
    {
        Log::info('Layer visibility change received', $data);
        
        // Update the visibility in our features array
        foreach ($this->selectedFeatures as $key => $feature) {
            if ($feature['id'] == $data['layerId']) {
                $this->selectedFeatures[$key]['visible'] = $data['visible'];
                
                // Dispatch event to update canvas in JS
                $this->dispatch('update-feature-visibility', [
                    'featureId' => $data['layerId'],
                    'visible' => $data['visible']
                ]);
                break;
            }
        }
    }
    
    /**
     * Handle layer opacity change from LayerPanel
     */
    public function handleLayerOpacityChange($data)
    {
        Log::info('Layer opacity change received', $data);
        
        // Update the opacity in our features array
        foreach ($this->selectedFeatures as $key => $feature) {
            if ($feature['id'] == $data['layerId']) {
                $this->selectedFeatures[$key]['opacity'] = $data['opacity'];
                
                // Dispatch event to update canvas in JS
                $this->dispatch('update-feature-opacity', [
                    'featureId' => $data['layerId'],
                    'opacity' => $data['opacity']
                ]);
                break;
            }
        }
    }
    
    /**
     * Handle selecting a feature on the canvas from the layer panel
     */
    public function handleSelectFeatureOnCanvas($data)
    {
        Log::info('Select feature on canvas request received', $data);
        
        // Dispatch event to select this feature on the canvas in JS
        $this->dispatch('select-feature', [
            'featureId' => $data['featureId']
        ]);
    }
    
    /**
     * Handle layer reordering from LayerPanel
     */
    public function handleLayersReordered($data)
    {
        Log::info('Layer reordering received', ['layer_count' => count($data['layers'])]);
        
        // Get the layer order as presented in the layer panel
        $orderedLayerIds = array_column($data['layers'], 'id');
        
        Log::info('New layer ordering from panel', [
            'layer_order' => $orderedLayerIds
        ]);
        
        // Rebuild the selectedFeatures array in the new order
        $orderedFeatures = [];
        foreach ($orderedLayerIds as $featureId) {
            foreach ($this->selectedFeatures as $feature) {
                if ($feature['id'] == $featureId) {
                    $orderedFeatures[] = $feature;
                    break;
                }
            }
        }
        
        $this->selectedFeatures = $orderedFeatures;
        
        // Dispatch event to update canvas with new order
        $this->dispatch('update-canvas', [
            'selectedFeatures' => $this->selectedFeatures
        ]);
        
        // Log the new order for debugging
        Log::info('Features reordered', [
            'new_order' => array_column($this->selectedFeatures, 'id')
        ]);
    }
    
    /**
     * Toggle the move mode on the canvas
     */
    public function toggleMoveMode()
    {
        $this->moveEnabled = !$this->moveEnabled;
        Log::info('Move mode toggled', ['moveEnabled' => $this->moveEnabled]);
        
        // Notify JavaScript to update canvas objects - send as both array and object format for compatibility
        $this->dispatch('toggle-move-mode', ['enabled' => $this->moveEnabled]);
        
        // Force a re-render of the component to update the move button appearance
        $this->render();
    }
    
    /**
     * Handle request for feature data when the feature isn't in the canvas yet
     */
    public function requestFeatureData($featureId)
    {
        Log::info('Requested feature data for ID', ['featureId' => $featureId]);
        
        // Check if we already have this feature
        foreach ($this->selectedFeatures as $feature) {
            if ($feature['id'] == $featureId) {
                Log::info('Feature already in selectedFeatures, dispatching directly', ['featureId' => $featureId]);
                
                // Dispatch the feature data directly
                $this->dispatch('direct-update-canvas', [
                    'feature' => $feature
                ]);
                
                return;
            }
        }
        
        // If not in selectedFeatures, fetch it from the database
        $feature = \App\Models\FacialFeature::find($featureId);
        
        if ($feature) {
            Log::info('Fetched feature from database', [
                'featureId' => $featureId,
                'name' => $feature->name,
                'image_path' => $feature->image_path
            ]);
            
            // Create feature data in the expected format
            $featureData = [
                'id' => $feature->id,
                'image_path' => $feature->image_path,
                'name' => $feature->name,
                'feature_type' => $feature->feature_type_id,
                'position' => [
                    'x' => 300, // Center of canvas
                    'y' => 300, // Center of canvas
                    'scale' => 1,
                    'rotation' => 0
                ]
            ];
            
            // Add it to our selectedFeatures array
            $this->selectedFeatures[] = $featureData;
            
            // Dispatch the feature data to the frontend
            $this->dispatch('direct-update-canvas', [
                'feature' => $featureData
            ]);
            
            // Also notify layer panel of the update
            $this->dispatch('layers-updated', $this->selectedFeatures);
        } else {
            Log::error('Feature not found in database', ['featureId' => $featureId]);
        }
    }
    
    public function render()
    {
        // Re-enable dispatch in render to ensure panels update if their state is stale
        // when they become visible again.
        $this->dispatch('layers-updated', $this->selectedFeatures);
        
        return view('livewire.editor.main-canvas');
    }
}
