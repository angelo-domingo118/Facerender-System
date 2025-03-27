<?php

namespace App\Livewire\Editor;

use Livewire\Component;

class FeatureAdjustmentPanel extends Component
{
    // Selected feature
    public $selectedFeature = null;
    
    // Transform properties
    public $positionX = 350;
    public $positionY = 100;
    public $scaleX = 100;
    public $scaleY = 100;
    public $rotation = 0;
    
    // Image adjustment properties
    public $brightness = 0;
    public $contrast = 0;
    public $saturation = 0;
    
    public function resetToDefaults()
    {
        $this->positionX = 350;
        $this->positionY = 100;
        $this->scaleX = 100;
        $this->scaleY = 100;
        $this->rotation = 0;
        $this->brightness = 0;
        $this->contrast = 0;
        $this->saturation = 0;
    }
    
    public function render()
    {
        return view('livewire.editor.feature-adjustment-panel');
    }
}
