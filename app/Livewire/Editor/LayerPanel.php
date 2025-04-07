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
    
    // Listen for feature updates from MainCanvas
    protected $listeners = [
        'layers-updated' => 'updateLayers',
        'feature-removed' => 'handleFeatureRemoved',
        'features-cleared' => 'clearLayers',
        'fabricjs:object-selected' => 'handleObjectSelected'
    ];
    
    public function mount($compositeId)
    {
        $this->compositeId = $compositeId;
        Log::info('Layer panel mounted', ['compositeId' => $compositeId]);
    }
    
    /**
     * Update the layers information when received from MainCanvas
     */
    public function updateLayers($features)
    {
        Log::info('Updating layers in panel', ['features_count' => count($features)]);
        
        // Convert features to layers format with additional properties
        $updatedLayers = [];
        
        // In fabric.js, the last item in the array is the topmost one visually
        // We want the topmost item to appear at the top of our panel
        // Process features from last to first to make top items appear first in the panel
        $reversedFeatures = array_reverse($features);
        
        foreach ($reversedFeatures as $feature) {
            $featureId = $feature['id'];
            
            // Check if this layer already exists to preserve its settings
            $existingLayer = $this->findLayer($featureId);
            
            $updatedLayers[] = [
                'id' => $featureId,
                'name' => $feature['name'] ?? ('Feature ' . $featureId),
                'feature_type' => $feature['feature_type'] ?? null,
                'visible' => $existingLayer ? $existingLayer['visible'] : true,
                'locked' => $existingLayer ? $existingLayer['locked'] : false,
                'opacity' => $existingLayer ? $existingLayer['opacity'] : 100,
                'position' => $feature['position'] ?? null,
                'image_path' => $feature['image_path'] ?? null
            ];
        }
        
        $this->layers = $updatedLayers;
        
        // If no layer is selected and we have layers, select the first one
        if ($this->selectedLayerId === null && !empty($this->layers)) {
            $this->selectLayer($this->layers[0]['id']);
        } 
        // If the selected layer doesn't exist anymore, select the first layer
        elseif (!empty($this->layers) && !$this->findLayer($this->selectedLayerId)) {
            $this->selectLayer($this->layers[0]['id']);
        }
        // If no layers exist, clear selection
        elseif (empty($this->layers)) {
            $this->selectedLayerId = null;
            $this->opacity = 100;
            $this->isLayerLocked = false;
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
        $this->dispatch('remove-feature-requested', [
            'featureId' => $layerId
        ]);
    }
    
    /**
     * Handle object selection from Fabric.js canvas
     */
    public function handleObjectSelected($data)
    {
        if (isset($data['left']) && isset($data['top'])) {
            $this->positionX = round($data['left']);
            $this->positionY = round($data['top']);
            Log::info('Object position updated from canvas', [
                'x' => $this->positionX,
                'y' => $this->positionY
            ]);
        }
    }
    
    /**
     * Update the position of the selected layer
     */
    public function updatePosition()
    {
        if ($this->selectedLayerId) {
            // Dispatch event to update canvas object position
            $this->dispatch('updateObjectPosition', [
                'layerId' => $this->selectedLayerId,
                'x' => (int) $this->positionX,
                'y' => (int) $this->positionY
            ]);
            
            Log::info('Position updated from panel', [
                'layerId' => $this->selectedLayerId,
                'x' => $this->positionX,
                'y' => $this->positionY
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.editor.layer-panel');
    }
}
