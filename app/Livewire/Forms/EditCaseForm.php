<?php

namespace App\Livewire\Forms;

use App\Models\CaseRecord;
use Livewire\Component;
use Livewire\Attributes\On;

class EditCaseForm extends Component
{
    public $show = false;
    public $case = null;
    public $caseId;
    public $title;
    public $incident_type;
    public $incident_date;
    public $description;
    public $status;
    public $location;

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

    #[On('edit-case')]
    public function openModal($caseId)
    {
        $this->caseId = $caseId;
        $this->loadCase();
        $this->show = true;
    }

    public function loadCase()
    {
        $this->case = CaseRecord::findOrFail($this->caseId);
        $this->title = $this->case->title;
        $this->incident_type = $this->case->incident_type;
        $this->incident_date = $this->case->incident_date ? $this->case->incident_date->format('Y-m-d') : null;
        $this->description = $this->case->description;
        $this->status = $this->case->status;
        $this->location = $this->case->location;
    }

    public function save()
    {
        $this->validate();

        $this->case->update([
            'title' => $this->title,
            'incident_type' => $this->incident_type,
            'incident_date' => $this->incident_date,
            'description' => $this->description,
            'status' => $this->status,
            'location' => $this->location ?: 'Unknown',
        ]);

        $this->dispatch('case-updated', caseId: $this->case->id);
        $this->dispatch('case-details-updated', [
            'id' => $this->case->id,
            'title' => $this->title,
            'status' => $this->status,
            'incident_type' => $this->incident_type,
            'incident_date' => $this->incident_date,
            'description' => $this->description,
            'location' => $this->location ?: 'Unknown',
        ]);
        
        $this->show = false;
        $this->reset(['case', 'caseId', 'title', 'incident_type', 'incident_date', 'description', 'status', 'location']);
    }

    public function cancel()
    {
        $this->show = false;
        $this->reset(['case', 'caseId', 'title', 'incident_type', 'incident_date', 'description', 'status', 'location']);
    }

    public function render()
    {
        return view('livewire.forms.edit-case-form');
    }
}
