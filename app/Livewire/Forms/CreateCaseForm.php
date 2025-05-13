<?php

namespace App\Livewire\Forms;

use App\Models\CaseRecord;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class CreateCaseForm extends Component
{
    public $show = false;
    public $title = '';
    public $incident_type = '';
    public $incident_date = '';
    public $description = '';
    public $status = 'open';
    public $location = '';

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'incident_type' => 'required|max:100',
        'incident_date' => 'required|date',
        'description' => 'nullable|max:1000',
        'status' => 'required|in:open,pending,closed,archived',
        'location' => 'nullable|max:255',
    ];

    protected $messages = [
        'title.required' => 'Case title is required',
        'title.min' => 'Case title must be at least 3 characters',
        'incident_type.required' => 'Incident type is required',
        'incident_date.required' => 'Incident date is required',
        'incident_date.date' => 'Please enter a valid date',
        'status.required' => 'Status is required',
    ];

    #[On('create-new-case')]
    public function openModal()
    {
        $this->resetForm();
        $this->show = true;
    }

    public function resetForm()
    {
        $this->reset(['title', 'incident_type', 'incident_date', 'description', 'status', 'location']);
        $this->status = 'open';
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate();

        $case = CaseRecord::create([
            'title' => $this->title,
            'reference_number' => 'CASE-' . strtoupper(Str::random(8)),
            'incident_type' => $this->incident_type,
            'incident_date' => $this->incident_date,
            'description' => $this->description,
            'status' => $this->status,
            'location' => $this->location ?: 'Unknown',
            'user_id' => Auth::id(),
        ]);

        $this->dispatch('case-created', caseId: $case->id);
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
        return view('livewire.forms.create-case-form');
    }
}
