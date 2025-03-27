<?php

namespace App\Livewire\Editor;

use Livewire\Component;
use App\Models\Composite;
use App\Models\Witness;
use Illuminate\Support\Facades\Log;

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
    
    protected $listeners = ['refreshDetails' => 'loadCompositeData'];
    
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
            $this->canvasWidth = $this->composite->canvas_width ?? 800;
            $this->canvasHeight = $this->composite->canvas_height ?? 600;
        }
    }
    
    public function resetForm()
    {
        $this->loadCompositeData();
        $this->dispatch('notify', [
            'message' => 'Form has been reset',
            'type' => 'info'
        ]);
    }
    
    public function saveComposite()
    {
        try {
            // Basic validation
            $validated = $this->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'witnessId' => 'nullable|exists:witnesses,id',
                'suspectGender' => 'nullable|string|max:50',
                'suspectEthnicity' => 'nullable|string|max:50',
                'suspectAgeRange' => 'nullable|string|max:50',
                'suspectHeight' => 'nullable|string|max:50',
                'suspectBodyBuild' => 'nullable|string|max:50',
                'suspectAdditionalNotes' => 'nullable|string',
                'canvasWidth' => 'required|integer|min:100|max:2000',
                'canvasHeight' => 'required|integer|min:100|max:2000',
            ]);
            
            if (!$this->composite) {
                $this->composite = Composite::find($this->compositeId);
                if (!$this->composite) {
                    throw new \Exception("Composite not found");
                }
            }
            
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
            
            $this->dispatch('notify', [
                'message' => 'Composite details saved successfully!',
                'type' => 'success'
            ]);
            
            // Emit an event to notify other components
            $this->dispatch('compositeUpdated', $this->compositeId);
            
        } catch (\Exception $e) {
            Log::error('Error saving composite: ' . $e->getMessage());
            $this->dispatch('notify', [
                'message' => 'Error saving composite: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }
    
    public function render()
    {
        // Get all witnesses for the dropdown
        $witnesses = Witness::all();
        
        return view('livewire.editor.composite-details-panel', [
            'witnesses' => $witnesses,
        ]);
    }
}
