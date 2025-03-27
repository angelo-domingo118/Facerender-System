<?php

namespace App\Livewire\Editor;

use Livewire\Component;
use App\Models\Composite;

class MainCanvas extends Component
{
    public $compositeId;
    public $composite;
    
    // Properties for canvas state
    public $activeTool = 'move'; // move, scale, rotate
    public $zoomLevel = 100;
    
    public function mount($compositeId)
    {
        $this->compositeId = $compositeId;
        $this->composite = Composite::find($compositeId);
    }
    
    public function setTool($tool)
    {
        $this->activeTool = $tool;
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
