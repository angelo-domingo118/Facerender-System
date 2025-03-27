<?php

namespace App\Livewire\Editor;

use Livewire\Component;

class LayerPanel extends Component
{
    public $blendMode = 'Normal';
    public $opacity = 100;
    public $selectedLayer = null;
    
    public function render()
    {
        return view('livewire.editor.layer-panel');
    }
}
