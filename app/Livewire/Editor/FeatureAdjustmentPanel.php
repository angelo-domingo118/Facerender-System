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
    
    // Adjustment properties
    public $brightness = 50;
    public $contrast = 50;
    public $saturation = 50;
    public $sharpness = 50;
    public $feathering = 20;
    public $skinTone = 50;
    public $skinToneLabel = 'Natural';
    public $showAdvanced = false;
    
    // Listen for events
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
     * When a layer is selected
     */
    public function handleLayerSelected($layerData)
    {
        Log::info('Layer selected in adjustment panel', ['layerId' => $layerData['id']]);
        $this->selectedLayer = $layerData;
        $this->selectedLayerId = $layerData['id'];
        
        // Load adjustment values if they exist, otherwise use defaults
        if (isset($layerData['adjustments'])) {
            $adjustments = $layerData['adjustments'];
            $this->brightness = $adjustments['brightness'] ?? 50;
            $this->contrast = $adjustments['contrast'] ?? 50;
            $this->saturation = $adjustments['saturation'] ?? 50;
            $this->sharpness = $adjustments['sharpness'] ?? 50;
            $this->feathering = $adjustments['feathering'] ?? 20;
            $this->skinTone = $adjustments['skinTone'] ?? 50;
            $this->updateSkinToneLabel();
        } else {
            $this->resetAdjustments();
        }
    }
    
    /**
     * Toggle the advanced panel visibility
     */
    public function toggleAdvancedPanel()
    {
        $this->showAdvanced = !$this->showAdvanced;
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
                $this->skinTone = 40;
                $this->skinToneLabel = 'Natural';
                break;
            case 'medium':
                $this->skinTone = 65;
                $this->skinToneLabel = 'Medium';
                break;
            case 'dark':
                $this->skinTone = 85;
                $this->skinToneLabel = 'Dark';
                break;
        }
        
        $this->updateAdjustments();
    }
    
    /**
     * Update skin tone label based on current value
     */
    private function updateSkinToneLabel()
    {
        if ($this->skinTone <= 25) {
            $this->skinToneLabel = 'Light';
        } elseif ($this->skinTone <= 50) {
            $this->skinToneLabel = 'Natural';
        } elseif ($this->skinTone <= 75) {
            $this->skinToneLabel = 'Medium';
        } else {
            $this->skinToneLabel = 'Dark';
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
        
        // Create adjustments data structure
        $adjustmentData = [
            'layerId' => $this->selectedLayerId,
            'adjustments' => [
                'brightness' => $this->brightness,
                'contrast' => $this->contrast,
                'saturation' => $this->saturation,
                'sharpness' => $this->sharpness,
                'feathering' => $this->feathering,
                'skinTone' => $this->skinTone,
                'skinToneLabel' => $this->skinToneLabel
            ]
        ];
        
        // Dispatch event to update canvas
        $this->dispatch('layer-adjustments-updated', $adjustmentData);
        
        Log::info('Layer adjustments updated', ['layerId' => $this->selectedLayerId]);
    }
    
    /**
     * Reset all adjustment values to defaults
     */
    public function resetAllAdjustments()
    {
        $this->resetAdjustments();
        $this->updateAdjustments();
    }
    
    /**
     * Reset adjustment values to defaults
     */
    private function resetAdjustments()
    {
        $this->brightness = 50;
        $this->contrast = 50;
        $this->saturation = 50;
        $this->sharpness = 50;
        $this->feathering = 20;
        $this->skinTone = 50;
        $this->skinToneLabel = 'Natural';
    }
    
    public function render()
    {
        return view('livewire.editor.feature-adjustment-panel');
    }
}
