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
    
    /**
     * Get composite details for printing.
     *
     * @return array
     */
    public function getCompositeDetailsForPrint()
    {
        if (!$this->composite) {
            $this->composite = Composite::find($this->compositeId);
        }
        
        if (!$this->composite) {
            return []; // Return empty if composite not found
        }
        
        // Eager load relations if necessary
        $this->composite->loadMissing(['witness', 'caseRecord']);
        
        return [
            'title' => $this->composite->title,
            'witness_name' => $this->composite->witness?->name,
            'case_title' => $this->composite->caseRecord?->title,
            'created_at' => $this->composite->created_at?->format('M d, Y H:i'),
            'description' => $this->composite->description,
            'suspect_gender' => $this->composite->suspect_gender,
            'suspect_ethnicity' => $this->composite->suspect_ethnicity,
            'suspect_age_range' => $this->composite->suspect_age_range,
            'suspect_height' => $this->composite->suspect_height,
            'suspect_body_build' => $this->composite->suspect_body_build,
            'suspect_additional_notes' => $this->composite->suspect_additional_notes,
        ];
    }
    
    public function render()
    {
        return view('livewire.editor.composite-editor');
    }
}
