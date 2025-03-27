<?php

namespace App\Livewire\Editor;

use Livewire\Component;
use App\Models\Composite;
use App\Models\Witness;

class CompositeDetailsPanel extends Component
{
    public $compositeId;
    public $composite;
    
    // Form fields
    public $title;
    public $description;
    public $witnessId;
    public $suspectGender;
    public $suspectEthnicity;
    public $suspectAgeRange;
    public $suspectHeight;
    public $suspectBodyBuild;
    public $suspectAdditionalNotes;
    public $canvasWidth = 800;
    public $canvasHeight = 600;
    
    public function mount($compositeId)
    {
        $this->compositeId = $compositeId;
        $this->loadCompositeData();
    }
    
    public function loadCompositeData()
    {
        $this->composite = Composite::find($this->compositeId);
        
        if ($this->composite) {
            $this->title = $this->composite->title;
            $this->description = $this->composite->description;
            $this->witnessId = $this->composite->witness_id;
            $this->suspectGender = $this->composite->suspect_gender;
            $this->suspectEthnicity = $this->composite->suspect_ethnicity;
            $this->suspectAgeRange = $this->composite->suspect_age_range;
            $this->suspectHeight = $this->composite->suspect_height;
            $this->suspectBodyBuild = $this->composite->suspect_body_build;
            $this->suspectAdditionalNotes = $this->composite->suspect_additional_notes;
            $this->canvasWidth = $this->composite->canvas_width;
            $this->canvasHeight = $this->composite->canvas_height;
        }
    }
    
    public function resetForm()
    {
        $this->loadCompositeData();
    }
    
    public function saveComposite()
    {
        // Validation would go here in a real implementation
        
        $this->composite->update([
            'title' => $this->title,
            'description' => $this->description,
            'witness_id' => $this->witnessId,
            'suspect_gender' => $this->suspectGender,
            'suspect_ethnicity' => $this->suspectEthnicity,
            'suspect_age_range' => $this->suspectAgeRange,
            'suspect_height' => $this->suspectHeight,
            'suspect_body_build' => $this->suspectBodyBuild,
            'suspect_additional_notes' => $this->suspectAdditionalNotes,
            'canvas_width' => $this->canvasWidth,
            'canvas_height' => $this->canvasHeight,
        ]);
        
        // In a real implementation, you might dispatch an event or show a notification
    }
    
    public function render()
    {
        $witnesses = [];
        
        if ($this->composite) {
            // Get witnesses related to the composite's case
            $witnesses = Witness::where('case_id', $this->composite->case_id)->get();
        }
        
        return view('livewire.editor.composite-details-panel', [
            'witnesses' => $witnesses,
        ]);
    }
}
