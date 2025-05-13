<?php

namespace App\Livewire\Dashboard;

use App\Models\Composite;
use App\Models\Witness;
use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;
use WireUi\Traits\WireUiActions;

class CompositeDetailsModal extends Component
{
    use WireUiActions;
    
    public $show = false;
    public $compositeId = null;
    public $isEditing = false;
    
    // Form properties
    public $title;
    public $description;
    public $witness_id;
    public $suspect_gender;
    public $suspect_ethnicity;
    public $suspect_age_range;
    public $suspect_height;
    public $suspect_body_build;
    public $suspect_additional_notes;
    public $created_at;
    public $available_witnesses = [];
    
    protected $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'nullable|max:1000',
        'witness_id' => 'required|exists:witnesses,id',
        'suspect_gender' => 'nullable|max:50',
        'suspect_ethnicity' => 'nullable|max:100',
        'suspect_age_range' => 'nullable|max:50',
        'suspect_height' => 'nullable|max:50',
        'suspect_body_build' => 'nullable|max:50',
        'suspect_additional_notes' => 'nullable|max:1000',
        'created_at' => 'required|date',
    ];
    
    protected $messages = [
        'title.required' => 'Composite title is required',
        'title.min' => 'Composite title must be at least 3 characters',
        'witness_id.required' => 'Please select a witness',
        'witness_id.exists' => 'The selected witness is invalid',
        'created_at.required' => 'Creation date is required',
    ];
    
    #[On('view-composite')]
    public function showComposite($compositeId)
    {
        $this->resetFormData();
        $this->compositeId = $compositeId;
        $this->loadCompositeData();
        $this->isEditing = false;
        $this->show = true;
    }
    
    #[On('edit-composite')]
    public function editComposite($compositeId)
    {
        $this->resetFormData();
        $this->compositeId = $compositeId;
        $this->loadCompositeData();
        $this->loadAvailableWitnesses();
        $this->isEditing = true;
        $this->show = true;
    }
    
    public function loadCompositeData()
    {
        $composite = $this->getCompositeProperty();
        
        if ($composite) {
            $this->title = $composite->title;
            $this->description = $composite->description;
            $this->witness_id = $composite->witness_id;
            $this->suspect_gender = $composite->suspect_gender;
            $this->suspect_ethnicity = $composite->suspect_ethnicity;
            $this->suspect_age_range = $composite->suspect_age_range;
            $this->suspect_height = $composite->suspect_height;
            $this->suspect_body_build = $composite->suspect_body_build;
            $this->suspect_additional_notes = $composite->suspect_additional_notes;
            $this->created_at = $composite->created_at;
        }
    }
    
    public function loadAvailableWitnesses()
    {
        $composite = $this->getCompositeProperty();
        
        if ($composite) {
            $this->available_witnesses = Witness::where('case_id', $composite->case_id)->get();
        }
    }
    
    public function getCompositeProperty()
    {
        if (!$this->compositeId) {
            return null;
        }
        
        return Composite::with(['witness', 'caseRecord'])
            ->findOrFail($this->compositeId);
    }
    
    public function toggleEditMode()
    {
        $this->isEditing = !$this->isEditing;
        
        if ($this->isEditing) {
            $this->loadAvailableWitnesses();
        }
    }
    
    public function saveChanges()
    {
        $this->validate();
        
        $composite = Composite::findOrFail($this->compositeId);
        $composite->update([
            'title' => $this->title,
            'description' => $this->description,
            'witness_id' => $this->witness_id,
            'suspect_gender' => $this->suspect_gender,
            'suspect_ethnicity' => $this->suspect_ethnicity,
            'suspect_age_range' => $this->suspect_age_range,
            'suspect_height' => $this->suspect_height,
            'suspect_body_build' => $this->suspect_body_build,
            'suspect_additional_notes' => $this->suspect_additional_notes,
        ]);
        
        $this->dispatch('composite-updated', ['compositeId' => $composite->id, 'caseId' => $composite->case_id]);
        $this->isEditing = false;
        
        // Show success notification
        $this->notification()->success(
            title: 'Composite Updated',
            description: 'Composite details have been successfully updated.'
        );
    }
    
    public function resetFormData()
    {
        $this->reset([
            'title', 'description', 'witness_id', 'suspect_gender',
            'suspect_ethnicity', 'suspect_age_range', 'suspect_height',
            'suspect_body_build', 'suspect_additional_notes', 'available_witnesses',
            'created_at'
        ]);
        $this->resetValidation();
    }
    
    public function closeModal()
    {
        $this->show = false;
        $this->resetFormData();
    }
    
    public function resetForm()
    {
        $this->loadCompositeData();
        $this->dispatch('notify', [
            'message' => 'Form has been reset',
            'type' => 'info'
        ]);
    }
    
    public function render()
    {
        return view('livewire.dashboard.composite-details-modal', [
            'composite' => $this->compositeId ? $this->getCompositeProperty() : null
        ]);
    }
}
