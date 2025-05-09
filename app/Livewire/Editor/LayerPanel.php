<?php

namespace App\Livewire\Editor;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

class LayerPanel extends Component
{
    public $compositeId;
    public $layers = []; // Stores layer information including visibility, locked state, etc.
    public $selectedLayerId = null; // Currently selected layer ID
    public $opacity = 100;
    public $isLayerLocked = false;
    public $positionX = 0;
    public $positionY = 0;
    public $width = 0;
    public $height = 0;
    
    // Listen for feature updates from MainCanvas
    protected $listeners = [
        'layers-updated' => 'updateLayers',
        'feature-removed' => 'handleFeatureRemoved',
        'features-cleared' => 'clearLayers',
        'fabricjs:object-selected' => 'handleObjectSelected',
        'fabricjs:object-modified' => 'handleObjectModified'
    ];
    
    public function mount($compositeId)
    {
        $this->compositeId = $compositeId;
        
        // Request features from MainCanvas on mount
        $this->requestFeaturesFromCanvas();
        
        Log::info('LayerPanel component mounted', [
            'compositeId' => $this->compositeId
        ]);
        
        // Add a delayed action to select the first layer if none is selected
        // This helps ensure layers are properly loaded before attempting selection
        $this->js("
            setTimeout(() => {
                if (!Livewire.find('" . $this->getId() . "').get('selectedLayerId')) {
                    console.log('Auto-selecting first layer after delay');
                    const layers = Livewire.find('" . $this->getId() . "').get('layers');
                    if (layers && layers.length > 0) {
                        Livewire.find('" . $this->getId() . "').set('selectedLayerId', layers[0].id);
                        Livewire.find('" . $this->getId() . "').call('selectLayer', layers[0].id);
                    }
                }
            }, 1000);
        ");
    }
    
    /**
     * Request features from MainCanvas to initialize the layer panel
     */
    public function requestFeaturesFromCanvas()
    {
        $this->dispatch('request-features-for-layer-panel');
        Log::info('Requested features from MainCanvas');
    }
    
    /**
     * Update the layers information when received from MainCanvas
     */
    public function updateLayers($features)
    {
        Log::info('LAYER DEBUG: Received features in LayerPanel', ['features' => $features]); // Log raw features
        
        // Create the layer representation for each feature
        $layers = [];
        
        foreach ($features as $index => $feature) {
            // Get the feature ID, skipping if not available
            if (!isset($feature['id'])) {
                Log::warning('Feature missing ID, skipping', ['feature' => $feature]);
                continue;
            }
            
            $featureId = $feature['id'];
            
            // Ensure opacity is always in the 0-100 range for UI display
            $opacity = isset($feature['opacity']) ? $feature['opacity'] : 100;
            // If opacity is in 0-1 range, convert to 0-100 range
            if ($opacity <= 1 && $opacity > 0) {
                $opacity = $opacity * 100;
                Log::info('Converted opacity from 0-1 to 0-100 scale', [
                    'featureId' => $featureId,
                    'original' => $feature['opacity'],
                    'converted' => $opacity
                ]);
            }
            
            // Store z_index value to ensure consistent layer ordering
            $zIndex = $feature['z_index'] ?? $index;
            
            $layers[] = [
                'id' => $featureId,
                'name' => $feature['name'] ?? ('Feature ' . ($index + 1)),
                'visible' => $feature['visible'] ?? true,
                'locked' => $feature['locked'] ?? false,
                // Add other properties that might be needed in the layer panel
                'position' => $feature['position'] ?? ['x' => 0, 'y' => 0],
                'opacity' => $opacity,
                'width' => $feature['width'] ?? null,
                'height' => $feature['height'] ?? null,
                'z_index' => $zIndex,
                'feature_type' => $feature['feature_type'] ?? null
            ];
        }
        
        // Store the layers in our component state
        $this->layers = $layers;
        
        // Log layer ordering for debugging
        $layerOrder = array_map(function($layer) {
            return sprintf('%s (ID: %d, Type: %d, z_index: %d)', 
                $layer['name'], 
                $layer['id'], 
                $layer['feature_type'] ?? 0,
                $layer['z_index'] ?? 0
            );
        }, $this->layers);
        
        Log::info('LAYER DEBUG: Final layers array set in LayerPanel', [
            'layerOrder' => $layerOrder
        ]);
        
        // If we have layers, select the first one by default if none is selected
        if (!empty($this->layers) && empty($this->selectedLayerId)) {
            $this->selectLayer($this->layers[0]['id']);
        }
    }
    
    /**
     * Find a layer by its ID
     */
    private function findLayer($layerId)
    {
        foreach ($this->layers as $layer) {
            if ($layer['id'] == $layerId) {
                return $layer;
            }
        }
        
        return null;
    }
    
    /**
     * Toggle a layer's visibility
     */
    public function toggleVisibility($layerId)
    {
        foreach ($this->layers as $key => $layer) {
            if ($layer['id'] == $layerId) {
                $this->layers[$key]['visible'] = !$this->layers[$key]['visible'];
                
                // Dispatch event to update canvas
                $this->dispatch('layer-visibility-changed', [
                    'layerId' => $layerId,
                    'visible' => $this->layers[$key]['visible']
                ]);
                
                Log::info('Layer visibility toggled', [
                    'layerId' => $layerId, 
                    'visible' => $this->layers[$key]['visible']
                ]);
                break;
            }
        }
    }
    
    /**
     * Select a layer for editing
     */
    public function selectLayer($layerId)
    {
        // Skip if already selected to prevent infinite loops
        if ($this->selectedLayerId == $layerId) {
            Log::info('Layer already selected, skipping', ['layerId' => $layerId]);
            return;
        }
        
        $this->selectedLayerId = $layerId;
        
        $layer = $this->findLayer($layerId);
        
        if ($layer) {
            $this->opacity = $layer['opacity'];
            $this->isLayerLocked = $layer['locked'];
            
            // Set position values if available
            if (isset($layer['position'])) {
                $this->positionX = $layer['position']['x'] ?? 0;
                $this->positionY = $layer['position']['y'] ?? 0;
            }
            
            // Dispatch event to update canvas with the full layer data to ensure consistent format
            $this->dispatch('layer-selected', $layer);
            
            Log::info('Layer selected', ['layerId' => $layerId]);
        } else {
            Log::warning('Attempted to select non-existent layer', ['layerId' => $layerId]);
        }
    }
    
    /**
     * Update layer opacity
     */
    public function updateOpacity()
    {
        if ($this->selectedLayerId) {
            foreach ($this->layers as $key => $layer) {
                if ($layer['id'] == $this->selectedLayerId) {
                    $this->layers[$key]['opacity'] = $this->opacity;
                    
                    // Dispatch event to update canvas
                    $this->dispatch('layer-opacity-changed', [
                        'layerId' => $this->selectedLayerId,
                        'opacity' => $this->opacity
                    ]);
                    
                    Log::info('Layer opacity updated', [
                        'layerId' => $this->selectedLayerId, 
                        'opacity' => $this->opacity
                    ]);
                    break;
                }
            }
        }
    }
    
    /**
     * Handle a feature being removed
     */
    public function handleFeatureRemoved($featureId)
    {
        // Remove the layer with the specified feature ID
        $this->layers = array_values(array_filter($this->layers, function($layer) use ($featureId) {
            return $layer['id'] != $featureId;
        }));
        
        // If the removed feature was selected, select another feature or clear selection
        if ($this->selectedLayerId == $featureId) {
            if (!empty($this->layers)) {
                $this->selectLayer($this->layers[0]['id']);
            } else {
                $this->selectedLayerId = null;
                $this->opacity = 100;
                $this->isLayerLocked = false;
            }
        }
        
        Log::info('Layer removed from panel', ['featureId' => $featureId]);
    }
    
    /**
     * Clear all layers
     */
    public function clearLayers()
    {
        $this->layers = [];
        $this->selectedLayerId = null;
        $this->opacity = 100;
        $this->isLayerLocked = false;
        
        Log::info('All layers cleared from panel');
    }
    
    public function moveLayerUp($layerId)
    {
        $currentIndex = $this->findLayerIndex($layerId);
        
        if ($currentIndex > 0) {
            // Swap with the layer above
            $temp = $this->layers[$currentIndex - 1];
            $this->layers[$currentIndex - 1] = $this->layers[$currentIndex];
            $this->layers[$currentIndex] = $temp;
            
            // Dispatch event to update canvas
            // Note: Since the layer panel has the reverse order of the canvas,
            // moving a layer "up" in the panel means moving it "down" in the canvas
            // (i.e., lower z-index). The MainCanvas component will handle the reversal.
            $this->dispatch('layers-reordered', [
                'layers' => $this->layers
            ]);
            
            Log::info('Layer moved up', ['layerId' => $layerId]);
        }
    }
    
    public function moveLayerDown($layerId)
    {
        $currentIndex = $this->findLayerIndex($layerId);
        
        if ($currentIndex < count($this->layers) - 1) {
            // Swap with the layer below
            $temp = $this->layers[$currentIndex + 1];
            $this->layers[$currentIndex + 1] = $this->layers[$currentIndex];
            $this->layers[$currentIndex] = $temp;
            
            // Dispatch event to update canvas
            // Note: Since the layer panel has the reverse order of the canvas,
            // moving a layer "down" in the panel means moving it "up" in the canvas
            // (i.e., higher z-index). The MainCanvas component will handle the reversal.
            $this->dispatch('layers-reordered', [
                'layers' => $this->layers
            ]);
            
            Log::info('Layer moved down', ['layerId' => $layerId]);
        }
    }
    
    private function findLayerIndex($layerId)
    {
        foreach ($this->layers as $index => $layer) {
            if ($layer['id'] == $layerId) {
                return $index;
            }
        }
        return -1;
    }
    
    /**
     * Toggle a layer's lock state
     */
    public function toggleLock($layerId)
    {
        foreach ($this->layers as $key => $layer) {
            if ($layer['id'] == $layerId) {
                $this->layers[$key]['locked'] = !$this->layers[$key]['locked'];
                $isLocked = $this->layers[$key]['locked'];
                
                // If this is the currently selected layer, update the property panel state
                if ($this->selectedLayerId == $layerId) {
                    $this->isLayerLocked = $isLocked;
                }
                
                // Dispatch event to update canvas
                $this->dispatch('layer-lock-changed', [
                    'layerId' => $layerId,
                    'locked' => $isLocked
                ]);
                
                Log::info('Layer lock toggled', [
                    'layerId' => $layerId, 
                    'locked' => $isLocked
                ]);
                break;
            }
        }
    }
    
    /**
     * Request deletion of a layer by dispatching an event to the main canvas.
     */
    public function requestDeletion($layerId)
    {
        Log::info('Requesting layer deletion', ['layerId' => $layerId]);
        $this->dispatch('remove-feature-requested', $layerId);
    }
    
    /**
     * Handle object selection from Fabric.js
     */
    public function handleObjectSelected($data = null)
    {
        if (empty($data)) {
            return;
        }
        
        if (is_array($data)) {
            $obj = $data;
        } else {
            $obj = (array)$data;
        }
        
        // Get the feature ID safely without assuming data structure
        $featureId = null;
        if (isset($obj['id'])) {
            $featureId = $obj['id'];
        } elseif (isset($obj['data']) && isset($obj['data']['featureId'])) {
            $featureId = $obj['data']['featureId'];
        }
        
        // Only proceed if we have a valid feature ID
        if ($featureId) {
            // Only select the layer if it's not already selected
            if ($this->selectedLayerId != $featureId) {
                $this->selectLayer($featureId);
            }
            
            // Update position values
            if (isset($obj['left'])) {
                $this->positionX = round($obj['left']);
            }
            
            if (isset($obj['top'])) {
                $this->positionY = round($obj['top']);
            }
            
            // Update dimension values using width and height multiplied by scale factors
            if (isset($obj['width']) && isset($obj['scaleX'])) {
                $this->width = round($obj['width'] * $obj['scaleX']);
            }
            
            if (isset($obj['height']) && isset($obj['scaleY'])) {
                $this->height = round($obj['height'] * $obj['scaleY']);
            }
            
            Log::info('Object selected in layer panel', [
                'featureId' => $featureId,
                'position' => ['x' => $this->positionX, 'y' => $this->positionY],
                'dimensions' => ['width' => $this->width, 'height' => $this->height]
            ]);
        }
    }
    
    /**
     * Handle object modification from Fabric.js
     */
    public function handleObjectModified($data = null)
    {
        if (empty($data)) {
            return;
        }
        
        if (is_array($data)) {
            $obj = $data;
        } else {
            $obj = (array)$data;
        }
        
        // Get the feature ID safely without assuming data structure
        $featureId = null;
        if (isset($obj['id'])) {
            $featureId = $obj['id'];
        } elseif (isset($obj['data']) && isset($obj['data']['featureId'])) {
            $featureId = $obj['data']['featureId'];
        }
        
        // Only proceed if we have a valid feature ID and it matches the selected layer
        if ($featureId && $featureId == $this->selectedLayerId) {
            
            // Update position values
            if (isset($obj['left'])) {
                $this->positionX = round($obj['left']);
            }
            
            if (isset($obj['top'])) {
                $this->positionY = round($obj['top']);
            }
            
            // Update dimension values using width and height multiplied by scale factors
            if (isset($obj['width']) && isset($obj['scaleX'])) {
                $this->width = round($obj['width'] * $obj['scaleX']);
            }
            
            if (isset($obj['height']) && isset($obj['scaleY'])) {
                $this->height = round($obj['height'] * $obj['scaleY']);
            }
            
            Log::info('Object modified in layer panel', [
                'featureId' => $featureId,
                'position' => ['x' => $this->positionX, 'y' => $this->positionY],
                'dimensions' => ['width' => $this->width, 'height' => $this->height]
            ]);
        }
    }
    
    /**
     * Update the position of the selected layer
     */
    public function updatePosition()
    {
        if ($this->selectedLayerId) {
            // Validate and sanitize position values
            $x = is_numeric($this->positionX) ? (int) $this->positionX : 0;
            $y = is_numeric($this->positionY) ? (int) $this->positionY : 0;
            
            // Dispatch event to update canvas object position
            $this->dispatch('updateObjectPosition', [
                'layerId' => $this->selectedLayerId,
                'x' => $x,
                'y' => $y
            ]);
            
            Log::info('Position updated from panel', [
                'layerId' => $this->selectedLayerId,
                'x' => $x,
                'y' => $y
            ]);
        }
    }
    
    /**
     * Handle layer ordering when sorting in the UI
     */
    public function updateLayerOrder($orderedIds)
    {
        Log::info('Layer order update received from UI', ['orderedIds' => $orderedIds]);
        
        // Need to convert the layer sorting to the correct order for the canvas
        // The sorted order from UI is in reverse of what we need for canvas
        // (top layer in UI = last drawn on canvas)
        $newOrder = array_values($orderedIds);
        
        // Rearrange the layers array to match the new order
        $reorderedLayers = [];
        
        // Create a map of layer ID to layer data
        $layerMap = [];
            foreach ($this->layers as $layer) {
            $layerMap[$layer['id']] = $layer;
                }
        
        // Build the reordered layers array
        foreach ($newOrder as $layerId) {
            if (isset($layerMap[$layerId])) {
                $reorderedLayers[] = $layerMap[$layerId];
            }
        }
        
        // Update the layers array with the new order
        $this->layers = $reorderedLayers;
        
        // Dispatch the event to the MainCanvas component with both newOrder and full layers data
        $this->dispatch('layers-reordered', [
            'newOrder' => $newOrder,
            'layers' => $this->layers
        ]);
        
        Log::info('Layer order dispatch sent', [
            'newOrder' => $newOrder,
            'layerCount' => count($this->layers)
        ]);
    }
    
    public function render()
    {
        return view('livewire.editor.layer-panel');
    }
}
