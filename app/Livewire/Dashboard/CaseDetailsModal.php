<?php

namespace App\Livewire\Dashboard;

use App\Models\CaseRecord;
use Livewire\Component;
use Livewire\Attributes\On;

class CaseDetailsModal extends Component
{
    public $caseId = null;
    public $show = false;
    
    #[On('case-selected')]
    public function showCase($caseId)
    {
        $this->caseId = $caseId;
        $this->show = true;
    }
    
    #[On('composite-deleted')]
    #[On('composite-updated')]
    public function refreshData($eventData = null)
    {
        // If the modal is open, refresh the data
        if ($this->show && $this->caseId) {
            // For composite deletion, only refresh if it's for the currently displayed case
            if ($eventData && isset($eventData['caseId']) && $eventData['caseId'] !== $this->caseId) {
                return;
            }
            
            // This forces the computed property to be recalculated
            unset($this->case);
        }
    }
    
    public function close()
    {
        $this->show = false;
    }
    
    public function getCaseProperty()
    {
        if (!$this->caseId) {
            return null;
        }
        
        return CaseRecord::with(['composites', 'witnesses', 'notes', 'documents'])
            ->findOrFail($this->caseId);
    }
    
    public function render()
    {
        return view('livewire.dashboard.case-details-modal', [
            'case' => $this->caseId ? $this->case : null
        ]);
    }
}
