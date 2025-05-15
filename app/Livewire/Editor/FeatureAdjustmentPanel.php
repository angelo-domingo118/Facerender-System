<?php

namespace App\Livewire\Editor;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

class FeatureAdjustmentPanel extends Component
{
    // Layer properties
    public $selectedLayer = null;
    public $selectedLayerId = null;
    public $layers = [];
    
    // Adjustment properties - default values represent no effect (0)
    public $contrast = 0;
    public $saturation = 0;
    public $sharpness = 0;
    public $feathering = 0;
    public $featheringCurve = 3; // Default middle value for curve amount (1-5 range)
    public $skinTone = 50; // This is a special case, 50 is the middle/neutral
    public $skinToneLabel = 'Natural';
    
    // Listen for events
    protected $listeners = [
        'layer-selected' => 'handleLayerSelected',
        'layers-updated' => 'updateLayers',
    ];
    
    /**
     * When component is initialized
     */
    public function mount()
    {
        Log::info('FeatureAdjustmentPanel mounted');
        // The component will wait for layers-updated event from MainCanvas
    }
    
    /**
     * Update the layers list when received from MainCanvas
     */
    public function updateLayers($layers)
    {
        $this->layers = $layers;
        Log::info('Layers updated in adjustment panel', [
            'layerCount' => count($layers),
            'layerIds' => collect($layers)->pluck('id')->toArray()
        ]);
        
        // DEBUG: Log complete layer data structure to see exactly what we're receiving
        Log::info('DEBUG - Complete layers data received:', [
            'layers' => json_encode($layers)
        ]);
        
        // Debug: Log the first layer's adjustments to see if they're coming through
        if (!empty($layers) && isset($layers[0]['adjustments'])) {
            Log::info('First layer adjustments', [
                'layerId' => $layers[0]['id'],
                'adjustments' => $layers[0]['adjustments']
            ]);
            
            // Auto-select the first layer if none is currently selected
            if (!$this->selectedLayerId && !empty($layers)) {
                $this->selectedLayerId = $layers[0]['id'];
                $this->selectedLayer = $layers[0];
                $this->loadAdjustmentsFromLayer($layers[0]);
                
                Log::info('Auto-selected first layer', [
                    'layerId' => $this->selectedLayerId
                ]);
            }
        } else {
            Log::warning('First layer has no adjustments data', [
                'firstLayer' => !empty($layers) ? $layers[0] : 'No layers',
                'hasAdjustments' => !empty($layers) && isset($layers[0]['adjustments'])
            ]);
        }
        
        // If we have a selected layer ID, verify it still exists in the layers
        if ($this->selectedLayerId) {
            $found = false;
            foreach ($this->layers as $layer) {
                if ($layer['id'] == $this->selectedLayerId) {
                    $found = true;
                    $this->selectedLayer = $layer;
                    $this->loadAdjustmentsFromLayer($layer);
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
     * When a layer is selected
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
        Log::info('Layer selected in adjustment panel', ['layerId' => $layerId]);
        
        // Load adjustment values from the selected layer
        $this->loadAdjustmentsFromLayer($this->selectedLayer);
    }
    
    /**
     * Load adjustments from layer data
     */
    private function loadAdjustmentsFromLayer($layer)
    {
        // Default values in case they're not set in the adjustments
        $defaultValues = [
            'contrast' => 0,
            'saturation' => 0,
            'sharpness' => 0,
            'feathering' => 0,
            'featheringCurve' => 3,
            'skinTone' => 50
        ];
        
        // Load adjustment values if they exist, otherwise use defaults
        if ($layer && isset($layer['adjustments'])) {
            $adjustments = $layer['adjustments'];
            
            // Log the adjustments we're loading for debugging
            Log::info('Loading adjustments from layer', [
                'layerId' => $layer['id'],
                'adjustments' => $adjustments,
                'adjustments_type' => gettype($adjustments)
            ]);
            
            // Handle the case where adjustments might be a JSON string
            if (is_string($adjustments)) {
                try {
                    $adjustments = json_decode($adjustments, true);
                    Log::info('Decoded adjustments from JSON string', [
                        'decoded' => $adjustments
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to decode adjustments JSON', [
                        'error' => $e->getMessage(),
                        'raw' => $adjustments
                    ]);
                    $adjustments = [];
                }
            }
            
            // Force debug output of the adjustments to make sure we're seeing what we expect
            $debugOutput = '';
            if (is_array($adjustments)) {
                foreach ($adjustments as $key => $value) {
                    $debugOutput .= "{$key}: {$value}, ";
                }
            } else {
                $debugOutput = "Not an array: " . gettype($adjustments);
            }
            Log::info('ADJUSTMENT DEBUG: ' . $debugOutput);
            
            // Log specific adjustments we're looking for
            Log::info('Looking for specific adjustment values', [
                'has_contrast' => isset($adjustments['contrast']),
                'contrast_value' => $adjustments['contrast'] ?? 'not set',
                'contrast_type' => isset($adjustments['contrast']) ? gettype($adjustments['contrast']) : 'N/A'
            ]);
            
            // Handle each adjustment value, checking for presence and correct type
            foreach ($defaultValues as $key => $defaultValue) {
                if (isset($adjustments[$key])) {
                    // Force convert to integer to avoid type issues
                    $value = is_numeric($adjustments[$key]) ? (int)$adjustments[$key] : $defaultValue;
                    $this->{$key} = $value;
                    
                    Log::info("Set {$key} to {$value}");
                } else {
                    $this->{$key} = $defaultValue;
                    Log::info("Using default for {$key}: {$defaultValue}");
                }
            }
            
            // Update the skinTone label based on the loaded value
            $this->updateSkinToneLabel();
            
            // Add client-side debugging
            $this->js("console.log('Loaded adjustments from layer:', {
                layerId: " . $layer['id'] . ", 
                contrast: " . $this->contrast . ", 
                saturation: " . $this->saturation . ",
                skinTone: " . $this->skinTone . "
            })");
        } else {
            Log::info('No adjustments found for layer', [
                'layerId' => $layer['id'] ?? 'unknown'
            ]);
            $this->resetAdjustments();
            
            // Add client-side debugging
            $this->js("console.log('No adjustments found, using defaults for layer:', {
                layerId: '" . ($layer['id'] ?? 'unknown') . "'
            })");
        }
    }
    
    /**
     * Set skin tone by preset
     */
    public function setSkinTone($preset)
    {
        switch ($preset) {
            case 'light':
                $this->skinTone = 15;
                $this->skinToneLabel = 'Light';
                break;
            case 'natural':
                $this->skinTone = 50;
                $this->skinToneLabel = 'Natural';
                break;
            case 'medium':
                $this->skinTone = 70;
                $this->skinToneLabel = 'Medium';
                break;
            case 'dark':
                $this->skinTone = 90;
                $this->skinToneLabel = 'Dark';
                break;
        }
        
        $this->updateAdjustments();
    }
    
    /**
     * Set skin tone to an exact value when clicking a color grid button
     */
    public function setSkinToneExact($value)
    {
        $this->skinTone = $value;
        $this->updateSkinToneLabel();
        $this->updateAdjustments();
    }
    
    /**
     * Update skin tone label based on current value
     */
    private function updateSkinToneLabel()
    {
        if ($this->skinTone <= 20) {
            $this->skinToneLabel = 'Light';
        } elseif ($this->skinTone <= 60) {
            $this->skinToneLabel = 'Natural';
        } elseif ($this->skinTone <= 80) {
            $this->skinToneLabel = 'Medium';
        } else {
            $this->skinToneLabel = 'Dark';
        }
    }
    
    /**
     * Lifecycle hook for when a public property is updated.
     */
    public function updated($propertyName)
    {
        // Check if the updated property is one of the adjustment sliders
        if (in_array($propertyName, ['contrast', 'saturation', 'sharpness', 'feathering', 'featheringCurve', 'skinTone'])) {
            $this->updateAdjustments();
        }
    }
    
    /**
     * Update adjustment values and dispatch change event
     */
    public function updateAdjustments()
    {
        if (!$this->selectedLayer) {
            return;
        }
        
        $this->updateSkinToneLabel();
        
        // Ensure all values are integers
        $contrast = (int)$this->contrast;
        $saturation = (int)$this->saturation;
        $sharpness = (int)$this->sharpness;
        $feathering = (int)$this->feathering;
        $featheringCurve = (int)$this->featheringCurve;
        $skinTone = (int)$this->skinTone;
        
        // Create adjustments data structure
        $adjustmentData = [
            'layerId' => $this->selectedLayerId,
            'adjustments' => [
                'contrast' => $contrast,
                'saturation' => $saturation,
                'sharpness' => $sharpness,
                'feathering' => $feathering,
                'featheringCurve' => $featheringCurve,
                'skinTone' => $skinTone,
                'skinToneLabel' => $this->skinToneLabel
            ]
        ];
        
        // Log the adjustments we're sending for debugging
        Log::info('Sending layer adjustments update', $adjustmentData);
        
        // Dispatch event to update canvas - using named parameters for Livewire 3
        $this->dispatch('layer-adjustments-updated', $adjustmentData);
        
        // Add client-side debugging
        $this->js("console.log('Dispatched layer-adjustments-updated:', " . json_encode($adjustmentData) . ")");
        
        // Also update our local layer data to keep it in sync
        if (isset($this->selectedLayer['adjustments'])) {
            $this->selectedLayer['adjustments'] = $adjustmentData['adjustments'];
        } else {
            $this->selectedLayer['adjustments'] = $adjustmentData['adjustments'];
        }
        
        // Comment out the toast notification that appears on every adjustment
        /*
        // Show a small toast notification that adjustments are being applied
        $this->js('$wireui.notify({
            title: "Adjustments Applied",
            description: "Changes will be saved when you click Save",
            icon: "info",
            timeout: 1500,
            position: "bottom-right"
        })');
        */
    }
    
    /**
     * Reset all adjustments to default values
     */
    public function resetAllAdjustments()
    {
        if (!$this->selectedLayer) {
            return;
        }
        
        $this->resetAdjustments();
        
        // Dispatch reset event
        $this->dispatch('reset-layer-adjustments', [
            'layerId' => $this->selectedLayerId,
            'action' => 'reset',
            'adjustments' => [
                'contrast' => 0,
                'saturation' => 0,
                'sharpness' => 0,
                'feathering' => 0,
                'featheringCurve' => 3,
                'skinTone' => 50,
                'skinToneLabel' => 'Natural'
            ]
        ]);
        
        // Comment out the reset notification
        /*
        // Show notification
        $this->js('$wireui.notify({
            title: "Adjustments Reset",
            description: "All adjustments have been reset to default values",
            icon: "information",
            timeout: 1500,
            position: "bottom-right"
        })');
        */
    }
    
    /**
     * Reset adjustment values to defaults
     */
    private function resetAdjustments()
    {
        $this->contrast = 0;
        $this->saturation = 0;
        $this->sharpness = 0;
        $this->feathering = 0;
        $this->featheringCurve = 3; // Mid-range value
        $this->skinTone = 50; // Neutral skin tone
        $this->skinToneLabel = 'Natural';
    }
    
    public function render()
    {
        // Add debugging to track render and current adjustments
        $this->js("console.log('FeatureAdjustmentPanel rendering:', {
            selectedLayerId: '" . ($this->selectedLayerId ?? 'none') . "',
            contrast: " . $this->contrast . ",
            saturation: " . $this->saturation . ",
            sharpness: " . $this->sharpness . ",
            feathering: " . $this->feathering . ",
            skinTone: " . $this->skinTone . "
        })");
        
        return view('livewire.editor.feature-adjustment-panel');
    }
}
