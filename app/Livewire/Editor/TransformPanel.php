<?php

namespace App\Livewire\Editor;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

class TransformPanel extends Component
{
    // Transform properties
    public $positionX = 0;
    public $positionY = 0;
    public $width = 100;
    public $height = 100;
    public float $rotation = 0.0;
    public $selectedLayer = null;
    public $selectedLayerId = null;
    public $moveIncrement = 5;
    public $resizeStep = 10;
    public $layers = [];
    public $size_control_used = false;
    public $preserveAspectRatio = true;
    public $originalWidth = 0;
    public $originalHeight = 0;
    public float $rotationIncrement = 0.5; // Default increment
    
    // Listen for events from LayerPanel and MainCanvas
    protected $listeners = [
        'layer-selected' => 'handleLayerSelected',
        'layers-updated' => 'updateLayers',
    ];
    
    /**
     * Update the layers list when received from MainCanvas
     */
    public function updateLayers($layers)
    {
        $this->layers = $layers;
        
        // If we have a selected layer ID, verify it still exists in the layers
        if ($this->selectedLayerId) {
            $found = false;
            foreach ($this->layers as $layer) {
                if ($layer['id'] == $this->selectedLayerId) {
                    $found = true;
                    break;
                }
            }
            
            // If the layer no longer exists, clear the selection
            if (!$found) {
                $this->selectedLayerId = null;
                $this->selectedLayer = null;
            }
        }
    }
    
    /**
     * When user selects a layer in the dropdown
     */
    public function handleLayerChange()
    {
        if ($this->selectedLayerId) {
            // Find the layer in our layers array
            foreach ($this->layers as $layer) {
                if ($layer['id'] == $this->selectedLayerId) {
                    $this->handleLayerSelected($layer);
                    
                    // Also notify the canvas to select this feature
                    $this->dispatch('select-feature-on-canvas', [
                        'featureId' => $this->selectedLayerId
                    ]);
                    break;
                }
            }
        } else {
            // Clear selection if no layer ID is selected
            $this->selectedLayer = null;
        }
    }
    
    /**
     * When a layer is selected in the LayerPanel or via the dropdown
     */
    public function handleLayerSelected($layerData)
    {
        // Fix: Handle different data formats that might be passed to this method
        $layerId = null;
        
        // If $layerData is an array with 'id' key
        if (is_array($layerData) && isset($layerData['id'])) {
            $layerId = $layerData['id'];
            $this->selectedLayer = $layerData;
        }
        // If $layerData is an array with 'layerId' key (from event payload)
        elseif (is_array($layerData) && isset($layerData['layerId'])) {
            $layerId = $layerData['layerId'];
            
            // Find the complete layer data from our layers array
            foreach ($this->layers as $layer) {
                if ($layer['id'] == $layerId) {
                    $this->selectedLayer = $layer;
                    break;
                }
            }
        }
        // If $layerData is a direct ID value
        else {
            $layerId = $layerData;
            
            // Find the complete layer data from our layers array
            foreach ($this->layers as $layer) {
                if ($layer['id'] == $layerId) {
                    $this->selectedLayer = $layer;
                    break;
                }
            }
        }
        
        if (!$layerId) {
            Log::error('Invalid layer data passed to handleLayerSelected', ['layerData' => $layerData]);
            return;
        }
        
        $this->selectedLayerId = $layerId;
        Log::info('Layer selected in transform panel', ['layerId' => $layerId]);
        
        // Initialize transform values from the layer data if available
        if ($this->selectedLayer && isset($this->selectedLayer['position'])) {
            $this->positionX = $this->selectedLayer['position']['x'] ?? 0;
            $this->positionY = $this->selectedLayer['position']['y'] ?? 0;
            $this->rotation = $this->selectedLayer['position']['rotation'] ?? 0.0;
            
            // Width and height might be directly on the feature or in the position
            $this->width = $this->selectedLayer['width'] ?? ($this->selectedLayer['position']['width'] ?? 100);
            $this->height = $this->selectedLayer['height'] ?? ($this->selectedLayer['position']['height'] ?? 100);
            
            // Store the original dimensions for aspect ratio calculations
            $this->originalWidth = $this->width;
            $this->originalHeight = $this->height;
        } else {
            // Default values if no position data exists
            $this->resetTransform();
        }
    }
    
    /**
     * Reset transform to default values
     */
    private function resetTransform()
    {
        $this->positionX = 0;
        $this->positionY = 0;
        $this->width = 100;
        $this->height = 100;
        $this->rotation = 0.0;
        $this->originalWidth = 100;
        $this->originalHeight = 100;
    }
    
    /**
     * Update transform values and dispatch change event
     */
    public function updateTransform()
    {
        if (!$this->selectedLayer) {
            return;
        }
        
        // Create transform data structure
        $transformData = [
            'layerId' => $this->selectedLayerId,
            'transform' => [
                'x' => $this->positionX,
                'y' => $this->positionY,
                'rotation' => $this->rotation
            ]
        ];
        
        // Always include width and height when size_control_used is true
        // This tells Fabric.js to update dimensions without scaling proportionally
        if ($this->size_control_used) {
            $transformData['transform']['width'] = $this->width;
            $transformData['transform']['height'] = $this->height;
            $transformData['transform']['scaleToWidth'] = false; // Tell Fabric not to use scaleX/Y
            
            // Reset the flag after use
            $this->size_control_used = false;
        }
        
        // Dispatch event to update canvas
        $this->dispatch('layer-transform-updated', $transformData);
        
        Log::info('Layer transform updated', ['layerId' => $this->selectedLayerId]);
    }
    
    /**
     * Move layer in a relative direction
     */
    public function moveRelative($direction)
    {
        if (!$this->selectedLayer) {
            return;
        }
        
        $amount = $this->moveIncrement;
        
        switch ($direction) {
            case 'north':
            case 'up':
                $this->positionY -= $amount;
                break;
            case 'south':
            case 'down':
                $this->positionY += $amount;
                break;
            case 'west':
            case 'left':
                $this->positionX -= $amount;
                break;
            case 'east':
            case 'right':
                $this->positionX += $amount;
                break;
            case 'northwest':
                $this->positionX -= $amount;
                $this->positionY -= $amount;
                break;
            case 'northeast':
                $this->positionX += $amount;
                $this->positionY -= $amount;
                break;
            case 'southwest':
                $this->positionX -= $amount;
                $this->positionY += $amount;
                break;
            case 'southeast':
                $this->positionX += $amount;
                $this->positionY += $amount;
                break;
        }
        
        $this->updateTransform();
    }
    
    /**
     * Reset position to 0,0
     */
    public function resetPosition()
    {
        if (!$this->selectedLayer) {
            return;
        }
        
        $this->positionX = 0;
        $this->positionY = 0;
        $this->updateTransform();
    }
    
    /**
     * Set the resize step size for dimension adjustments
     */
    public function setResizeStep($size)
    {
        $this->resizeStep = $size;
    }
    
    /**
     * Update when preserveAspectRatio is toggled
     */
    public function updatedPreserveAspectRatio($value)
    {
        Log::info('Preserve aspect ratio updated', ['value' => $value]);
        
        // Dispatch an event to notify the frontend about the aspect ratio change
        $this->dispatch('preserveAspectRatioChanged', $value);
    }

    /**
     * Saves the original dimensions before any adjustments
     * This is called from JavaScript when selecting an object
     */
    public function saveOriginalDimensions($width, $height)
    {
        $this->originalWidth = $width;
        $this->originalHeight = $height;
        Log::info('Saved original dimensions', ['width' => $width, 'height' => $height]);
    }
    
    /**
     * Adjust size in different ways (increase/decrease width/height)
     */
    public function adjustSize($action)
    {
        if (!$this->selectedLayer) {
            return;
        }
        
        // Mark that size is being updated
        $this->size_control_used = true;
        
        // Store current dimensions before adjustment if original dimensions aren't set
        if ($this->originalWidth == 0 || $this->originalHeight == 0) {
            $this->originalWidth = $this->width;
            $this->originalHeight = $this->height;
        }
        
        // Amount to adjust based on resize step setting
        $amount = $this->resizeStep;
        $aspectRatio = $this->originalHeight / $this->originalWidth;
        
        switch ($action) {
            case 'increase-width':
                $this->width += $amount;
                // Only adjust height if preserve aspect ratio is enabled
                if ($this->preserveAspectRatio === true) {
                    $this->height = round($this->width * $aspectRatio);
                }
                break;
                
            case 'decrease-width':
                $this->width = max(10, $this->width - $amount);
                // Only adjust height if preserve aspect ratio is enabled
                if ($this->preserveAspectRatio === true) {
                    $this->height = round($this->width * $aspectRatio);
                }
                break;
                
            case 'increase-height':
                $this->height += $amount;
                // Only adjust width if preserve aspect ratio is enabled
                if ($this->preserveAspectRatio === true) {
                    $this->width = round($this->height / $aspectRatio);
                }
                break;
                
            case 'decrease-height':
                $this->height = max(10, $this->height - $amount);
                // Only adjust width if preserve aspect ratio is enabled
                if ($this->preserveAspectRatio === true) {
                    $this->width = round($this->height / $aspectRatio);
                }
                break;
        }
        
        // Ensure dimensions are at least 10px
        $this->width = max(10, $this->width);
        $this->height = max(10, $this->height);
        
        $this->updateTransform();
    }
    
    /**
     * Reset size to default (100x100)
     */
    public function resetSize()
    {
        if (!$this->selectedLayer) {
            return;
        }
        
        $this->width = 100;
        $this->height = 100;
        $this->originalWidth = 100;
        $this->originalHeight = 100;
        
        // Mark that size was updated
        $this->size_control_used = true;
        
        $this->updateTransform();
    }
    
    /**
     * Reset rotation to 0
     */
    public function resetRotation()
    {
        if (!$this->selectedLayer) {
            return;
        }
        
        $this->rotation = 0.0;
        $this->updateTransform();
    }
    
    /**
     * Set the movement increment size for position adjustments
     */
    public function setMoveIncrement($size)
    {
        $this->moveIncrement = $size;
    }
    
    /**
     * Rotate the layer by a specific angle or the current increment
     */
    public function rotateBy($direction) // Accepts 'increase' or 'decrease' now
    {
        if (!$this->selectedLayer) {
            return;
        }
        
        // Determine the angle based on direction and increment
        $angle = ($direction === 'increase') ? (float)$this->rotationIncrement : -(float)$this->rotationIncrement;
        
        // Ensure rotation is treated as a float
        $newRotation = (float)$this->rotation + $angle;
        
        // Normalize the rotation angle to be within -180 to 180 degrees
        while ($newRotation > 180) {
            $newRotation -= 360;
        }
        while ($newRotation <= -180) { // Use <= to include -180
            $newRotation += 360;
        }
        
        // Round to a reasonable precision (e.g., 1 decimal place)
        $this->rotation = round($newRotation, 1);
        
        $this->updateTransform();
    }
    
    /**
     * Validation hook for rotationIncrement
     */
    public function updatedRotationIncrement($value)
    {
        // Ensure the value is within bounds and numeric
        $this->rotationIncrement = max(0.1, min(45, (float)$value));
        Log::info('Rotation increment updated', ['new_value' => $this->rotationIncrement]);
    }
    
    public function render()
    {
        return view('livewire.editor.transform-panel');
    }
} 