<?php

namespace App\Livewire\Forms;

use App\Models\Witness;
use Livewire\Component;
use Livewire\Attributes\On;

class EditWitnessForm extends Component
{
    public $show = false;
    public $witnessId = null;
    public $witness = null;
    
    public $name = '';
    public $age = null;
    public $gender = '';
    public $contact_number = '';
    public $address = '';
    public $relationship_to_case = '';
    public $notes = '';
    public $reliability_rating = null;
    public $interview_date = null;
    
    protected $rules = [
        'name' => 'required|min:3|max:255',
        'age' => 'nullable|numeric|min:1|max:120',
        'gender' => 'required|in:Male,Female,Other',
        'contact_number' => 'nullable|max:20',
        'address' => 'nullable|max:255',
        'relationship_to_case' => 'nullable|max:100',
        'notes' => 'nullable|max:1000',
        'reliability_rating' => 'nullable|numeric|min:1|max:10',
        'interview_date' => 'nullable|date',
    ];
    
    #[On('edit-witness')]
    public function openModal($witnessId)
    {
        $this->resetForm();
        $this->witnessId = $witnessId;
        $this->loadWitness();
        $this->show = true;
    }
    
    public function loadWitness()
    {
        $this->witness = Witness::findOrFail($this->witnessId);
        
        $this->name = $this->witness->name;
        $this->age = $this->witness->age;
        $this->gender = $this->witness->gender;
        $this->contact_number = $this->witness->contact_number;
        $this->address = $this->witness->address;
        $this->relationship_to_case = $this->witness->relationship_to_case;
        $this->notes = $this->witness->interview_notes;
        $this->reliability_rating = $this->witness->reliability_rating;
        $this->interview_date = $this->witness->interview_date;
    }
    
    public function resetForm()
    {
        $this->reset([
            'name', 'age', 'gender', 'contact_number',
            'address', 'relationship_to_case', 'notes',
            'reliability_rating', 'interview_date'
        ]);
        $this->resetValidation();
    }
    
    public function save()
    {
        $this->validate();
        
        $this->witness->update([
            'name' => $this->name,
            'age' => $this->age,
            'gender' => $this->gender,
            'contact_number' => $this->contact_number,
            'address' => $this->address,
            'relationship_to_case' => $this->relationship_to_case,
            'interview_notes' => $this->notes,
            'reliability_rating' => $this->reliability_rating,
            'interview_date' => $this->interview_date,
        ]);
        
        $this->dispatch('witness-updated', caseId: $this->witness->case_id);
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
        return view('livewire.forms.edit-witness-form');
    }
}
