<?php

namespace App\Livewire\Editor;

use Livewire\Component;
use App\Models\Composite;
use App\Services\CompositeFeaturesService;
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
        'update-feature-position' => 'updateFeaturePosition',
        'remove-feature' => 'removeFeature',
        'remove-feature-requested' => 'removeFeature',
        'clear-features' => 'clearFeatures',
        'layer-visibility-changed' => 'handleLayerVisibilityChange',
        'layer-opacity-changed' => 'handleLayerOpacityChange',
        'select-feature-on-canvas' => 'handleSelectFeatureOnCanvas',
        'layers-reordered' => 'handleLayersReordered',
        'layer-transform-updated' => 'handleLayerTransformUpdated',
        'layer-adjustments-updated' => 'handleLayerAdjustmentsUpdated',
        'update-object-position' => 'handleUpdateObjectPosition',
        'reset-layer-adjustments' => 'handleResetLayerAdjustments',
        'request-active-feature-ids' => 'dispatchActiveFeatureIds',
        'save-composite-features' => 'saveCompositeFeaturesHandler',
        'compositeUpdated' => 'loadSavedFeatures',
        'request-features-for-layer-panel' => 'sendFeaturesToLayerPanel'
    ];
    
    public function mount($compositeId)
    {
        $this->compositeId = $compositeId;
        $this->composite = Composite::find($compositeId);
        
        Log::info('MainCanvas component mounted', [
            'compositeId' => $this->compositeId,
            'listeners' => $this->listeners
        ]);
        
        // Load any saved features from the database
        $this->loadSavedFeatures();
        
        // Initially, notify the layer panel of the current features
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
            Log::info('LAYER DEBUG: Dispatching layers-updated from MainCanvas::addFeature', ['features' => $this->selectedFeatures]);
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
                // Store both scaleX and scaleY separately if provided
                if (isset($data['position']['scaleX']) || isset($data['position']['scaleY'])) {
                    // Initialize position object if it doesn't exist
                    if (!isset($this->selectedFeatures[$key]['position'])) {
                        $this->selectedFeatures[$key]['position'] = [];
                    }
                    
                    // Update specific scale values if provided, otherwise keep existing values
                    if (isset($data['position']['scaleX'])) {
                        $this->selectedFeatures[$key]['position']['scaleX'] = $data['position']['scaleX'];
                    }
                    if (isset($data['position']['scaleY'])) {
                        $this->selectedFeatures[$key]['position']['scaleY'] = $data['position']['scaleY'];
                    }
                    
                    // Remove the old 'scale' property if we're now using separate X/Y scales
                    if (isset($this->selectedFeatures[$key]['position']['scale'])) {
                        unset($this->selectedFeatures[$key]['position']['scale']);
                    }
                } else if (isset($data['position']['scale'])) {
                    // If only unified scale is provided, use it
                    $this->selectedFeatures[$key]['position']['scale'] = $data['position']['scale'];
                }
                
                // Update other position properties
                $this->selectedFeatures[$key]['position'] = array_merge(
                    $this->selectedFeatures[$key]['position'] ?? [],
                    $data['position']
                );
                
                break;
            }
        }

        // After updating position, notify layer panel of the change
        $this->dispatch('layers-updated', $this->selectedFeatures);
        
        // Automatically save the updated features to the database
        $this->saveCompositeFeatures();
    }
    
    /**
     * Remove a feature from the canvas and delete it from the database
     */
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
        
        // Also remove the feature from the database
        try {
            $featuresService = app(CompositeFeaturesService::class);
            
            // Find the composite facial feature record based on composite ID and facial feature ID
            $compositeFacialFeatures = \App\Models\CompositeFacialFeature::where('composite_id', $this->compositeId)
                ->where('facial_feature_id', $featureId)
                ->get();
                
            foreach ($compositeFacialFeatures as $feature) {
                $featuresService->removeFeature($feature->id);
            }
            
            Log::info('Feature removed from database', [
                'featureId' => $featureId,
                'compositeId' => $this->compositeId
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing feature from database: ' . $e->getMessage(), [
                'featureId' => $featureId,
                'compositeId' => $this->compositeId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Clear all features from the canvas and the database
     */
    public function clearFeatures()
    {
        // Clear all features from the canvas
        $this->selectedFeatures = [];
        Log::info('All features cleared from canvas');

        // Notify layer panel that all features were cleared
        $this->dispatch('clear-layers');

        // Dispatch updated active feature IDs (which will be empty)
        $this->dispatchActiveFeatureIds();
        
        // Also clear features from the database
        try {
            $featuresService = app(CompositeFeaturesService::class);
            $featuresService->clearFeatures($this->compositeId);
            
            Log::info('All features cleared from database', [
                'compositeId' => $this->compositeId
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing features from database: ' . $e->getMessage(), [
                'compositeId' => $this->compositeId,
                'error' => $e->getMessage()
            ]);
        }
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
        // Support both data formats (new with layers array, old with just newOrder)
        $newOrder = $data['newOrder'] ?? null;
        
        if (!$newOrder || !is_array($newOrder)) {
            Log::warning('Invalid layer reordering data - missing newOrder', ['data' => $data]);
            return;
        }
        
        Log::info('Reordering layers', [
            'newOrder' => $newOrder,
            'format' => isset($data['layers']) ? 'full' : 'ids-only'
        ]);
        
        // Create a new array to hold reordered features
        $reorderedFeatures = [];
        
        // Map featureIds to the current feature objects
        $featureMap = [];
            foreach ($this->selectedFeatures as $feature) {
            $featureMap[$feature['id']] = $feature;
        }
        
        // Reconstruct the features array in the new order
        foreach ($newOrder as $featureId) {
            if (isset($featureMap[$featureId])) {
                $reorderedFeatures[] = $featureMap[$featureId];
            } else {
                Log::warning('Feature not found during reordering', ['featureId' => $featureId]);
                }
            }
        
        // Update the selectedFeatures array
        $this->selectedFeatures = $reorderedFeatures;
        
        // Re-dispatch the event to ensure JS picks it up
        $this->dispatch('update-canvas', [
            'selectedFeatures' => $this->selectedFeatures,
            'forceUpdate' => true  // Add forceUpdate flag to ensure canvas redraws properly
        ]);
        
        // Save the new ordering to the database
        try {
            $featuresService = app(CompositeFeaturesService::class);
        
            // Extract just the feature IDs in the current order for the service
            $orderedIds = array_column($this->selectedFeatures, 'id');
            
            // Update the feature order in the database
            $featuresService->updateFeatureOrder($this->compositeId, $orderedIds);
            
            Log::info('Layer order saved to database', [
                'compositeId' => $this->compositeId,
                'orderedIds' => $orderedIds
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving layer order: ' . $e->getMessage(), [
                'compositeId' => $this->compositeId,
                'error' => $e->getMessage()
            ]);
        }
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
    
    /**
     * Handle layer transform updates from the TransformPanel
     */
    public function handleLayerTransformUpdated($data)
    {
        Log::info('Handling layer transform update', [
            'layerId' => $data['layerId'],
            'transform' => $data['transform']
        ]);
        
        // Find the feature with the specified layer ID
        foreach ($this->selectedFeatures as $key => $feature) {
            if ($feature['id'] == $data['layerId']) {
                // Update the feature's position/transform properties
                $this->selectedFeatures[$key]['position'] = [
                    'x' => $data['transform']['x'],
                    'y' => $data['transform']['y'],
                    'scale' => 1, // Maintain default scale for now
                    'rotation' => $data['transform']['rotation']
                ];
                
                // Add width and height if they're provided
                if (isset($data['transform']['width'])) {
                    $this->selectedFeatures[$key]['width'] = $data['transform']['width'];
                }
                
                if (isset($data['transform']['height'])) {
                    $this->selectedFeatures[$key]['height'] = $data['transform']['height'];
                }
                
                // Dispatch canvas update
                $this->dispatch('update-canvas', ['selectedFeatures' => $this->selectedFeatures]);
                
                // Notify layer panel of the change
                $this->dispatch('layers-updated', $this->selectedFeatures);
                
                break;
            }
        }
    }
    
    /**
     * Handle layer adjustments updated from the adjustment panel
     */
    public function handleLayerAdjustmentsUpdated($data)
    {
        if (!isset($data['layerId']) || !isset($data['adjustments'])) {
            Log::warning('Invalid layer adjustment data', ['data' => $data]);
            return;
        }
        
        $featureId = $data['layerId'];
        $adjustments = $data['adjustments'];
        
        Log::info('Updating layer adjustments', [
            'featureId' => $featureId,
            'adjustments' => $adjustments
        ]);
        
        // Find and update the feature in our local array
        foreach ($this->selectedFeatures as $key => $feature) {
            if ($feature['id'] == $featureId) {
                // Set or merge the adjustments
                $this->selectedFeatures[$key]['adjustments'] = $adjustments;
                break;
            }
        }
                
        // Dispatch canvas update
        $this->dispatch('update-canvas', [
            'selectedFeatures' => $this->selectedFeatures,
            'updateAdjustments' => true,
            'featureId' => $featureId
        ]);
        
        // Save the adjustments to the database
        try {
            $featuresService = app(CompositeFeaturesService::class);
                
            // Find the composite facial feature record
            $compositeFacialFeatures = \App\Models\CompositeFacialFeature::where('composite_id', $this->compositeId)
                ->where('facial_feature_id', $featureId)
                ->get();
                
            foreach ($compositeFacialFeatures as $feature) {
                $featuresService->updateFeature($feature->id, [
                    'visual_adjustments' => $adjustments
                ]);
            }
            
            Log::info('Feature adjustments saved to database', [
                'featureId' => $featureId,
                'compositeId' => $this->compositeId
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving feature adjustments to database: ' . $e->getMessage(), [
                'featureId' => $featureId,
                'compositeId' => $this->compositeId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle position updates from the layer panel
     */
    public function handleUpdateObjectPosition($data)
    {
        Log::info('Handling position update from layer panel', [
            'layerId' => $data['layerId'],
            'x' => $data['x'],
            'y' => $data['y'],
            'data' => $data
        ]);
        
        // Find the feature with the specified layer ID
        $found = false;
        foreach ($this->selectedFeatures as $key => $feature) {
            if ($feature['id'] == $data['layerId']) {
                $found = true;
                // Create position object if it doesn't exist
                if (!isset($this->selectedFeatures[$key]['position'])) {
                    Log::info('Creating new position object for feature', [
                        'featureId' => $feature['id']
                    ]);
                    $this->selectedFeatures[$key]['position'] = [];
                }
                
                // Log previous position for debugging
                Log::info('Previous position values', [
                    'featureId' => $feature['id'],
                    'previous_x' => $this->selectedFeatures[$key]['position']['x'] ?? 'not set',
                    'previous_y' => $this->selectedFeatures[$key]['position']['y'] ?? 'not set'
                ]);
                
                // Update position data
                $this->selectedFeatures[$key]['position']['x'] = $data['x'];
                $this->selectedFeatures[$key]['position']['y'] = $data['y'];
                
                // Maintain other position properties if they exist
                if (isset($feature['position']['rotation'])) {
                    $this->selectedFeatures[$key]['position']['rotation'] = $feature['position']['rotation'];
                }
                
                if (isset($feature['position']['scale'])) {
                    $this->selectedFeatures[$key]['position']['scale'] = $feature['position']['scale'];
                }
                
                // Dispatch event to update the canvas
                $eventData = [
                    'featureId' => $data['layerId'],
                    'x' => $data['x'],
                    'y' => $data['y']
                ];
                Log::info('Dispatching direct-update-object-position event', $eventData);
                $this->dispatch('direct-update-object-position', $eventData);
                
                // Also update the canvas with the full selected features array
                Log::info('Dispatching update-canvas event');
                $this->dispatch('update-canvas', ['selectedFeatures' => $this->selectedFeatures]);
                
                break;
            }
        }
        
        if (!$found) {
            Log::error('Feature not found for position update', [
                'layerId' => $data['layerId'],
                'available_features' => array_column($this->selectedFeatures, 'id')
            ]);
        }
    }
    
    /**
     * Handle resetting layer adjustments from the FeatureAdjustmentPanel
     */
    public function handleResetLayerAdjustments($data)
    {
        Log::info('Handling reset layer adjustments', [
            'layerId' => $data['layerId'],
            'action' => $data['action'] ?? 'reset',
        ]);
        
        // Find the feature with the specified layer ID
        foreach ($this->selectedFeatures as $key => $feature) {
            if ($feature['id'] == $data['layerId']) {
                // Remove the adjustments property if it exists
                if (isset($this->selectedFeatures[$key]['adjustments'])) {
                    unset($this->selectedFeatures[$key]['adjustments']);
                }
                
                // Dispatch event to reset image on the canvas
                $this->dispatch('update-feature-adjustments', [
                    'featureId' => $data['layerId'],
                    'action' => 'reset',
                    'adjustments' => $data['adjustments'] ?? null
                ]);
                
                // Also update the main canvas state
                $this->dispatch('update-canvas', ['selectedFeatures' => $this->selectedFeatures]);
                
                // Update the layer panel
                $this->dispatch('layers-updated', $this->selectedFeatures);
                
                // Also save the reset to the database
                try {
                    $featuresService = app(CompositeFeaturesService::class);
                    
                    // Find the composite facial feature record
                    $compositeFacialFeatures = \App\Models\CompositeFacialFeature::where('composite_id', $this->compositeId)
                        ->where('facial_feature_id', $data['layerId'])
                        ->get();
                    
                    foreach ($compositeFacialFeatures as $feature) {
                        $featuresService->updateFeature($feature->id, [
                            'visual_adjustments' => $data['adjustments'] ?? null
                        ]);
                    }
                    
                    Log::info('Feature adjustments reset saved to database', [
                        'featureId' => $data['layerId'],
                        'compositeId' => $this->compositeId
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error saving reset adjustments to database: ' . $e->getMessage(), [
                        'featureId' => $data['layerId'],
                        'compositeId' => $this->compositeId,
                        'error' => $e->getMessage()
                    ]);
                }
                
                break;
            }
        }
    }
    
    /**
     * Save all current features to the database
     */
    public function saveCompositeFeaturesHandler()
    {
        $result = $this->saveCompositeFeatures();

        if ($result) {
            // Show success notification using WireUI
            $this->js('$wireui.notify({
                title: "Success!",
                description: "Facial features saved successfully",
                icon: "success",
                timeout: 3000
            })');
        } else {
            // Show error notification using WireUI
            $this->js('$wireui.notify({
                title: "Error!",
                description: "Error saving facial features. Please try again.",
                icon: "error",
                timeout: 3000
            })');
        }
    }

    /**
     * Save all current features to the database
     */
    public function saveCompositeFeatures()
    {
        try {
            $featuresService = app(CompositeFeaturesService::class);
            
            // First, clear existing features to avoid duplicates
            $featuresService->clearFeatures($this->compositeId);
            
            // Then add each feature from the in-memory array
            foreach ($this->selectedFeatures as $feature) {
                // Convert opacity from UI scale (0-100) to database scale (0-1)
                $opacityValue = isset($feature['opacity']) ? ($feature['opacity'] / 100) : 1.0;
                
                // Extract the position data and use separate scale_x and scale_y values
                $scaleX = $feature['position']['scaleX'] ?? $feature['position']['scale'] ?? 1.0;
                $scaleY = $feature['position']['scaleY'] ?? $feature['position']['scale'] ?? 1.0;
                
                $attributes = [
                    'position_x' => $feature['position']['x'] ?? 0,
                    'position_y' => $feature['position']['y'] ?? 0,
                    'scale_x' => $scaleX, // Use separate scale values for X and Y
                    'scale_y' => $scaleY, // This preserves aspect ratio
                    'rotation' => $feature['position']['rotation'] ?? 0,
                    'opacity' => $opacityValue, // Now properly scaled for database storage
                    'visible' => $feature['visible'] ?? true,
                    'locked' => $feature['locked'] ?? false,
                ];
                
                Log::info('Saving feature with position and scale', [
                    'featureId' => $feature['id'],
                    'scaleX' => $scaleX,
                    'scaleY' => $scaleY,
                    'x' => $attributes['position_x'],
                    'y' => $attributes['position_y']
                ]);
                
                // Extract visual adjustments if available
                if (isset($feature['adjustments']) && is_array($feature['adjustments'])) {
                    $attributes['visual_adjustments'] = $feature['adjustments'];
                }
                
                // Add the feature to the database
                $featuresService->addFeature(
                    $this->compositeId,
                    $feature['id'],
                    $attributes
                );
            }
            
            Log::info('Composite features saved', [
                'compositeId' => $this->compositeId,
                'featureCount' => count($this->selectedFeatures)
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error saving composite features: ' . $e->getMessage(), [
                'compositeId' => $this->compositeId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * Load saved features from the database when the component mounts or composite is updated
     */
    public function loadSavedFeatures()
    {
        try {
            $featuresService = app(CompositeFeaturesService::class);
            $savedFeatures = $featuresService->getCompositeFeatures($this->compositeId);
            
            // DEBUG: Directly check the visual_adjustments column in the database
            foreach ($savedFeatures as $dbFeature) {
                Log::info('DEBUG - Raw feature data from database', [
                    'featureId' => $dbFeature->facial_feature_id,
                    'scale_x' => $dbFeature->scale_x,
                    'scale_y' => $dbFeature->scale_y,
                    'visual_adjustments' => $dbFeature->visual_adjustments,
                    'visual_adjustments_type' => gettype($dbFeature->visual_adjustments)
                ]);
            }
            
            if ($savedFeatures->isEmpty()) {
                Log::info('No saved features found for composite', [
                    'compositeId' => $this->compositeId
                ]);
                
                // Still reset the canvas and notify panels
                $this->selectedFeatures = [];
                $this->dispatch('canvas-reset');
                $this->dispatch('update-canvas', [
                    'selectedFeatures' => [],
                    'forceUpdate' => true
                ]);
                $this->dispatch('layers-updated', []);
                
                return;
            }
            
            // Convert the database models to the format expected by the canvas
            $this->selectedFeatures = [];
            
            foreach ($savedFeatures as $feature) {
                // Get the facial feature details
                $facialFeature = $feature->facialFeature;
                
                if ($facialFeature) {
                    // Fix opacity scale: Convert database 0-1 scale to UI 0-100 scale
                    $opacity = $feature->opacity * 100;
                    
                    // Ensure adjustments are properly formatted for the UI
                    $adjustments = $feature->visual_adjustments ?? [];
                    
                    // Use separate scale_x and scale_y values to preserve aspect ratio
                    $scaleX = $feature->scale_x;
                    $scaleY = $feature->scale_y;
                    
                    Log::info('Processing feature scale for UI', [
                        'featureId' => $facialFeature->id,
                        'scale_x' => $feature->scale_x,
                        'scale_y' => $feature->scale_y
                    ]);
                    
                    // Build the feature data
                    $featureData = [
                        'id' => $facialFeature->id,
                        'image_path' => $facialFeature->image_path,
                        'name' => $facialFeature->name,
                        'feature_type' => $facialFeature->feature_type_id,
                        'position' => [
                            'x' => $feature->position_x,
                            'y' => $feature->position_y,
                            'scaleX' => $scaleX, // Use separate scale values
                            'scaleY' => $scaleY, // to preserve aspect ratio
                            'rotation' => $feature->rotation
                        ],
                        'opacity' => $opacity, // Now using the converted value (0-100)
                        'visible' => $feature->visible,
                        'locked' => $feature->locked,
                        'adjustments' => $adjustments,
                        'z_index' => $feature->z_index // Add z_index for consistent ordering
                    ];
                    
                    // Add to the features array
                    $this->selectedFeatures[] = $featureData;
                    
                    Log::info('Added feature to UI with scale and adjustments', [
                        'featureId' => $facialFeature->id,
                        'scaleX' => $scaleX,
                        'scaleY' => $scaleY,
                        'has_adjustments' => isset($featureData['adjustments'])
                    ]);
                }
            }
            
            // Sort features by z_index for consistent layer ordering
            // IMPORTANT: For consistency with canvas display, sort ascending (lower z_index first)
            // This means the face (higher z_index) will be drawn last, appearing on top
            usort($this->selectedFeatures, function($a, $b) {
                $aIndex = $a['z_index'] ?? 0;
                $bIndex = $b['z_index'] ?? 0;
                return $aIndex - $bIndex; // Ascending order (lower z_index first)
            });

            Log::info('Sorted features by z_index (ascending) for canvas display consistency', [
                'compositeId' => $this->compositeId,
                'featureOrder' => array_map(function($feature) {
                    return sprintf('%s (ID: %d, Type: %d, z_index: %d, scale: %s)', 
                        $feature['name'], 
                        $feature['id'], 
                        $feature['feature_type'],
                        $feature['z_index'] ?? 0,
                        $feature['position']['scale']
                    );
                }, $this->selectedFeatures)
            ]);
            
            // Ensure canvas is reset before updating
            $this->dispatch('canvas-reset');
            
            // Slight delay to ensure reset completes before update
            // This is handled by queueing in the browser event loop
            $this->dispatch('update-canvas', [
                'selectedFeatures' => $this->selectedFeatures,
                'forceUpdate' => true
            ]);
            
            // For the layer panel, we need to reverse the order
            // Lower z-index shown at bottom of panel, higher z-index at top
            $layerPanelFeatures = array_reverse($this->selectedFeatures);
            
            // Update layer panel with reversed order
            $this->dispatch('layers-updated', $layerPanelFeatures);
            
            Log::info('Loaded saved features for composite', [
                'compositeId' => $this->compositeId,
                'featureCount' => count($this->selectedFeatures)
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading saved features: ' . $e->getMessage(), [
                'compositeId' => $this->compositeId,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function render()
    {
        // Re-enable dispatch in render to ensure panels update if their state is stale
        // when they become visible again.
        // $this->dispatch('layers-updated', $this->selectedFeatures); // Commented out - potentially causing order issues
        
        return view('livewire.editor.main-canvas');
    }

    /**
     * Get the current selected features for JavaScript
     * 
     * @return array
     */
    public function getSelectedFeatures()
    {
        Log::info('getSelectedFeatures requested by JS', [
            'compositeId' => $this->compositeId,
            'featureCount' => count($this->selectedFeatures)
        ]);
        
        return $this->selectedFeatures;
    }

    /**
     * Handle request for features from the layer panel
     */
    public function sendFeaturesToLayerPanel()
    {
        Log::info('Sending features to layer panel on request', [
            'featureCount' => count($this->selectedFeatures)
        ]);
        
        // Send current features to the layer panel
        $this->dispatch('layers-updated', $this->selectedFeatures);
    }

    /**
     * Force a reload of features to the canvas
     * This can be called when there might be issues with canvas display
     */
    public function forceReloadFeatures()
    {
        Log::info('Force reload features requested', [
            'compositeId' => $this->compositeId,
            'featureCount' => count($this->selectedFeatures)
        ]);
        
        // Reset the canvas first
        $this->dispatch('canvas-reset');
        
        // Then force update with current features
        $this->dispatch('update-canvas', [
            'selectedFeatures' => $this->selectedFeatures,
            'forceUpdate' => true
        ]);
        
        // Update layer panel with current features
        $this->dispatch('layers-updated', $this->selectedFeatures);
        
        return true;
    }
}
