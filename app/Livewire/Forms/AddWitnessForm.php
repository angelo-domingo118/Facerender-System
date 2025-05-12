<?php

namespace App\Livewire\Forms;

use App\Models\Witness;
use App\Models\CaseRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;
use Carbon\Carbon;

class AddWitnessForm extends Component
{
    public $show = false;
    public $caseId = null;
    public $case = null;
    public $name = '';
    public $age = null;
    public $gender = '';
    public $contact_number = '';
    public $address = '';
    public $relationship_to_case = '';
    public $notes = '';
    public $interview_date = null;

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'age' => 'nullable|numeric|min:1|max:120',
        'gender' => 'required|in:Male,Female,Other',
        'contact_number' => 'nullable|max:20',
        'address' => 'nullable|max:255',
        'relationship_to_case' => 'nullable|max:100',
        'notes' => 'nullable|max:1000',
        'interview_date' => 'required|date',
    ];

    protected $messages = [
        'name.required' => 'Witness name is required',
        'name.min' => 'Witness name must be at least 3 characters',
        'gender.required' => 'Please select a gender',
        'interview_date.required' => 'Interview date is required',
    ];

    #[On('add-witness')]
    public function openModal($caseId)
    {
        $this->resetForm();
        $this->caseId = $caseId;
        $this->loadCase();
        $this->show = true;
    }

    public function loadCase()
    {
        $this->case = CaseRecord::findOrFail($this->caseId);
    }

    public function resetForm()
    {
        $this->reset([
            'name', 'age', 'contact_number',
            'address', 'relationship_to_case', 'notes',
        ]);
        $this->gender = 'Male'; // Default value
        $this->interview_date = Carbon::now()->format('Y-m-d'); // Set current date as default
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $witness = Witness::create([
            'case_id' => $this->caseId,
            'name' => $this->name,
            'age' => $this->age,
            'gender' => $this->gender,
            'contact_number' => $this->contact_number,
            'address' => $this->address,
            'relationship_to_case' => $this->relationship_to_case,
            'interview_notes' => $this->notes,
            'interview_date' => $this->interview_date,
        ]);

        $this->dispatch('witness-added', witnessId: $witness->id);
        $this->show = false;
        $this->resetForm();
    }

    public function cancel()
    {
        $this->show = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.forms.add-witness-form');
    }
}
