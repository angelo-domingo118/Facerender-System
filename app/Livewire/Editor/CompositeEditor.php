<?php

namespace App\Livewire\Editor;

use Livewire\Component;
use App\Models\Composite;

class CompositeEditor extends Component
{
    public $compositeId;
    public $composite;
    
    // State for UI elements
    public $leftSidebarExpanded = true;
    public $rightSidebarExpanded = true;
    public $activeRightTab = 'layers'; // 'layers', 'adjustments', 'details'
    
    public function mount($compositeId)
    {
        $this->compositeId = $compositeId;
        
        // In a real app, you would load the composite data from the database
        // For now, we're just setting up the UI structure
        $this->composite = Composite::find($compositeId);
    }
    
    public function toggleLeftSidebar()
    {
        $this->leftSidebarExpanded = !$this->leftSidebarExpanded;
    }
    
    public function toggleRightSidebar()
    {
        $this->rightSidebarExpanded = !$this->rightSidebarExpanded;
    }
    
    public function setActiveRightTab($tab)
    {
        $this->activeRightTab = $tab;
    }
    
    public function render()
    {
        return view('livewire.editor.composite-editor');
    }
}
