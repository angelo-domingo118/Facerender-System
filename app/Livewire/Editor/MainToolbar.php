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

    /**
     * Get composite details for printing.
     * This method can be called from JavaScript when the Print button is clicked.
     *
     * @return array
     */
    public function getCompositeDetailsForPrint()
    {
        // Forward to the parent component if possible
        if ($this->compositeId) {
            // Find the composite
            $composite = \App\Models\Composite::find($this->compositeId);
            
            if (!$composite) {
                return []; // Return empty if composite not found
            }
            
            // Eager load relations if necessary
            $composite->loadMissing(['witness', 'caseRecord']);
            
            return [
                'title' => $composite->title,
                'witness_name' => $composite->witness?->name,
                'case_title' => $composite->caseRecord?->title,
                'created_at' => $composite->created_at?->format('M d, Y H:i'),
                'description' => $composite->description,
                'suspect_gender' => $composite->suspect_gender,
                'suspect_ethnicity' => $composite->suspect_ethnicity,
                'suspect_age_range' => $composite->suspect_age_range,
                'suspect_height' => $composite->suspect_height,
                'suspect_body_build' => $composite->suspect_body_build,
                'suspect_additional_notes' => $composite->suspect_additional_notes,
            ];
        }
        
        return [];
    }
}
