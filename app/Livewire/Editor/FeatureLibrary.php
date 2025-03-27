<?php

namespace App\Livewire\Editor;

use Livewire\Component;

class FeatureLibrary extends Component
{
    public $selectedCategory = '';
    public $search = '';
    
    public function render()
    {
        return view('livewire.editor.feature-library');
    }
}
