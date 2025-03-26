<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;

class CaseCard extends Component
{
    public $case;
    public $isExpanded = false;
    
    public function mount($case)
    {
        $this->case = $case;
        
        // Auto-expand based on URL query params if we're in witness or composite view mode
        $contentTypeFilter = request()->query('contentTypeFilter', 'all');
        if ($contentTypeFilter === 'witnesses' || $contentTypeFilter === 'composites') {
            $this->isExpanded = true;
        }
    }
    
    // Get a unique identifier for this component instance
    private function getComponentId()
    {
        return 'case-' . $this->case->id;
    }
    
    public function toggleExpand()
    {
        $this->isExpanded = !$this->isExpanded;
    }
    
    public function viewCaseDetails()
    {
        $this->dispatch('case-selected', caseId: $this->case->id);
    }
    
    public function togglePin()
    {
        $this->case->is_pinned = !$this->case->is_pinned;
        $this->case->save();
        
        // Dispatch case-updated event to refresh the list and maintain sort order
        $this->dispatch('case-updated');
    }
    
    public function deleteCase()
    {
        // Instead of showing a local modal, dispatch an event to the global modal manager
        $this->dispatch('show-delete-modal', [
            'title' => 'Delete Case',
            'message' => 'Are you sure you want to delete this case? This action cannot be undone, and all associated data will be permanently removed.',
            'confirmText' => 'Delete Case',
            'id' => $this->getComponentId(),
            'targetId' => $this->case->id,
            'type' => 'case'
        ]);
    }
    
    #[On('confirm-delete')]
    public function handleDelete($data)
    {
        // Only process if this is the target component and it's a case
        if ($data['id'] === $this->getComponentId() && $data['type'] === 'case' && $data['targetId'] === $this->case->id) {
            $case = $this->case;
            $case->delete();
            $this->dispatch('case-deleted');
        }
    }
    
    public function createComposite()
    {
        $this->dispatch('create-composite', caseId: $this->case->id);
    }
    
    #[On('case-details-updated')]
    public function handleCaseUpdate($updatedCase)
    {
        // Only update this case card if it matches the updated case ID
        if ($this->case->id === $updatedCase['id']) {
            $this->case->title = $updatedCase['title'];
            $this->case->status = $updatedCase['status'];
            $this->case->incident_type = $updatedCase['incident_type'];
            $this->case->incident_date = $updatedCase['incident_date'];
            $this->case->description = $updatedCase['description'];
            $this->case->location = $updatedCase['location'];
            
            // This will trigger a selective refresh of just this component
            $this->case->updated_at = now();
        }
    }
    
    #[On('composite-deleted')]
    #[On('composite-updated')]
    #[On('composite-created')]
    #[On('witness-deleted')]
    #[On('witness-added')]
    #[On('witness-updated')]
    public function refreshData($eventData = null)
    {
        // For composite/witness deletion/updates, only refresh if it's for this case
        if ($eventData && isset($eventData['caseId']) && $eventData['caseId'] !== $this->case->id) {
            return;
        }
        
        // Refresh the case data from the database to reflect the updated composites/witnesses
        $this->case->refresh();
    }
    
    public function editCase()
    {
        $this->dispatch('edit-case', caseId: $this->case->id);
    }
    
    public function addWitness()
    {
        $this->dispatch('add-witness', caseId: $this->case->id);
    }
    
    public function addDocument()
    {
        $this->dispatch('add-document', caseId: $this->case->id);
    }
    
    public function render()
    {
        return view('livewire.dashboard.case-card');
    }
}
