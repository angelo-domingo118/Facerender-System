<?php

namespace App\Livewire\Editor;

use Livewire\Component;
use App\Models\Composite;

class MainToolbar extends Component
{
    public $compositeId;
    public $composite;
    
    public function mount($compositeId)
    {
        $this->compositeId = $compositeId;
        $this->composite = Composite::find($compositeId);
    }
    
    public function render()
    {
        return view('livewire.editor.main-toolbar');
    }
}
