<?php

namespace App\Livewire\Forms;

use App\Models\Composite;
use App\Models\Witness;
use App\Models\CaseRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;

class CreateCompositeForm extends Component
{
    public $show = false;
    public $caseId = null;
    public $case = null;
    public $title = '';
    public $description = '';
    public $witness_id = null;
    public $available_witnesses = [];
    public $suspect_gender = '';
    public $suspect_ethnicity = '';
    public $suspect_age_range = '';
    public $suspect_height = '';
    public $suspect_body_build = '';
    public $suspect_additional_notes = '';
    public $created_at = null;
    
    public $heightOptions = [
        ['name' => 'Under 5 feet', 'value' => 'Under 5 feet'],
        ['name' => '5\' to 5\'4"', 'value' => '5\' to 5\'4"'],
        ['name' => '5\'5" to 5\'8"', 'value' => '5\'5" to 5\'8"'],
        ['name' => '5\'9" to 6\'', 'value' => '5\'9" to 6\''],
        ['name' => '6\'1" to 6\'4"', 'value' => '6\'1" to 6\'4"'],
        ['name' => 'Over 6\'4"', 'value' => 'Over 6\'4"']
    ];

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

    #[On('create-composite')]
    public function openModal($caseId)
    {
        $this->resetForm();
        $this->caseId = $caseId;
        $this->loadCase();
        $this->loadAvailableWitnesses();
        $this->created_at = Carbon::now()->format('Y-m-d');
        $this->show = true;
    }

    public function loadCase()
    {
        $this->case = CaseRecord::findOrFail($this->caseId);
    }

    public function loadAvailableWitnesses()
    {
        try {
            // Directly fetch and format witnesses data
            $witnesses = Witness::where('case_id', $this->caseId)->get();
            
            if ($witnesses->isNotEmpty()) {
                // Simplify to basic array format for standard HTML select
                $this->available_witnesses = $witnesses->map(function($witness) {
                    return [
                        'value' => $witness->id,
                        'label' => $witness->name
                    ];
                })->toArray();
                
                // Set first witness as default if available
                if (!empty($this->available_witnesses)) {
                    $this->witness_id = $this->available_witnesses[0]['value'];
                }
            } else {
                $this->available_witnesses = [];
            }
        } catch (\Exception $e) {
            // Log error and set empty array as fallback
            \Illuminate\Support\Facades\Log::error('Error loading witnesses: ' . $e->getMessage());
            $this->available_witnesses = [];
        }
    }

    public function resetForm()
    {
        $this->reset([
            'title', 'description', 'witness_id', 'suspect_gender',
            'suspect_ethnicity', 'suspect_age_range', 'suspect_height',
            'suspect_body_build', 'suspect_additional_notes'
        ]);
        $this->created_at = Carbon::now()->format('Y-m-d');
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        if (empty($this->available_witnesses)) {
            $this->addError('witness_id', 'You need to add at least one witness to the case before creating a composite.');
            return;
        }

        $composite = Composite::create([
            'case_id' => $this->caseId,
            'user_id' => Auth::id(),
            'witness_id' => $this->witness_id,
            'title' => $this->title,
            'description' => $this->description,
            'canvas_width' => 800, // Default canvas width
            'canvas_height' => 600, // Default canvas height
            'suspect_gender' => $this->suspect_gender,
            'suspect_ethnicity' => $this->suspect_ethnicity,
            'suspect_age_range' => $this->suspect_age_range,
            'suspect_height' => $this->suspect_height,
            'suspect_body_build' => $this->suspect_body_build,
            'suspect_additional_notes' => $this->suspect_additional_notes,
            'created_at' => $this->created_at,
        ]);

        // Dispatch composite-created event for editor navigation
        $this->dispatch('composite-created', compositeId: $composite->id);
        
        // Dispatch composite-updated event to refresh the parent case card
        $this->dispatch('composite-updated', ['caseId' => $this->caseId]);
        
        $this->show = false;
        $this->resetForm();
    }

    public function cancel()
    {
        $this->show = false;
        $this->resetForm();
    }

    #[On('witness-added')]
    public function refreshWitnesses($witnessId = null)
    {
        if ($this->show && $this->caseId) {
            $this->loadAvailableWitnesses();
            
            // If a new witness was just added, select it
            if ($witnessId) {
                $this->witness_id = $witnessId;
            }
        }
    }

    public function render()
    {
        return view('livewire.forms.create-composite-form');
    }
}
