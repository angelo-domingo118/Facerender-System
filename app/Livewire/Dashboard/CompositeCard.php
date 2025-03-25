<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;

class CompositeCard extends Component
{
    public $composite;
    
    public function mount($composite)
    {
        $this->composite = $composite;
    }
    
    // Get a unique identifier for this component instance
    private function getComponentId()
    {
        return 'composite-' . $this->composite->id;
    }
    
    public function viewComposite()
    {
        $this->dispatch('view-composite', compositeId: $this->composite->id);
    }
    
    public function editComposite()
    {
        $this->dispatch('edit-composite', compositeId: $this->composite->id);
    }
    
    public function downloadComposite()
    {
        $this->dispatch('download-composite', compositeId: $this->composite->id);
    }
    
    public function deleteComposite()
    {
        // Instead of showing a local modal, dispatch an event to the global modal manager
        $this->dispatch('show-delete-modal', [
            'title' => 'Delete Composite',
            'message' => 'Are you sure you want to delete this composite? This action cannot be undone.',
            'confirmText' => 'Delete Composite',
            'id' => $this->getComponentId(),
            'targetId' => $this->composite->id,
            'type' => 'composite'
        ]);
    }
    
    #[On('confirm-delete')]
    public function handleDelete($data)
    {
        // Only process if this is the target component and it's a composite
        if ($data['id'] === $this->getComponentId() && $data['type'] === 'composite' && $data['targetId'] === $this->composite->id) {
            // Store the ID before deleting
            $compositeId = $this->composite->id;
            $caseId = $this->composite->case_id;
            
            // Delete the composite
            $this->composite->delete();
            
            // Dispatch event with the IDs for better tracking
            $this->dispatch('composite-deleted', [
                'compositeId' => $compositeId,
                'caseId' => $caseId
            ]);
        }
    }
    
    public function render()
    {
        return view('livewire.dashboard.composite-card');
    }
}
