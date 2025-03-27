<?php

namespace App\Livewire\Editor;

use Livewire\Component;
use App\Models\Composite;

class MainToolbar extends Component
{
    public $compositeId;
    public $composite;
    
    // Listen for the compositeUpdated event from CompositeDetailsPanel
    protected $listeners = ['compositeUpdated' => 'refreshComposite'];
    
    public function mount($compositeId)
    {
        $this->compositeId = $compositeId;
        $this->loadComposite();
    }
    
    public function loadComposite()
    {
        $this->composite = Composite::find($this->compositeId);
    }
    
    public function refreshComposite($compositeId = null)
    {
        // Only refresh if the event is for this composite
        // If no ID is provided, or it matches our current ID
        if (!$compositeId || $compositeId == $this->compositeId) {
            $this->loadComposite();
        }
    }
    
    public function render()
    {
        return view('livewire.editor.main-toolbar');
    }
}
