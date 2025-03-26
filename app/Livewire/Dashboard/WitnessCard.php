<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Witness;
use Livewire\Attributes\On;

class WitnessCard extends Component
{
    public Witness $witness;
    public $showDetails = false;

    public function mount(Witness $witness)
    {
        $this->witness = $witness;
    }

    private function getComponentId()
    {
        return 'witness-' . $this->witness->id;
    }

    public function toggleDetails()
    {
        $this->showDetails = !$this->showDetails;
    }

    public function editWitness()
    {
        $this->dispatch('edit-witness', witnessId: $this->witness->id);
    }

    public function deleteWitness()
    {
        // Dispatch to the global delete modal manager
        $this->dispatch('show-delete-modal', [
            'title' => 'Delete Witness',
            'message' => 'Are you sure you want to delete this witness? This action cannot be undone.',
            'confirmText' => 'Delete Witness',
            'id' => $this->getComponentId(),
            'targetId' => $this->witness->id,
            'type' => 'witness'
        ]);
    }

    #[On('confirm-delete')]
    public function handleDelete($data)
    {
        // Only process if this is the target component and it's a witness
        if ($data['id'] === $this->getComponentId() && $data['type'] === 'witness' && $data['targetId'] === $this->witness->id) {
            $witness = $this->witness;
            $witness->delete();
            $this->dispatch('witness-deleted', caseId: $witness->case_id);
        }
    }

    public function render()
    {
        return view('livewire.dashboard.witness-card');
    }
}
