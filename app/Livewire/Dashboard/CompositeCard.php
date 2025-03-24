<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class CompositeCard extends Component
{
    public $composite;
    public $confirmingDelete = false;
    
    public function mount($composite)
    {
        $this->composite = $composite;
    }
    
    public function togglePin()
    {
        $this->composite->is_pinned = !$this->composite->is_pinned;
        $this->composite->save();
        $this->dispatch('composite-updated');
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
        $this->confirmingDelete = true;
    }
    
    public function confirmDelete()
    {
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
        
        $this->confirmingDelete = false;
    }
    
    public function cancelDelete()
    {
        $this->confirmingDelete = false;
    }
    
    public function render()
    {
        return view('livewire.dashboard.composite-card');
    }
}
