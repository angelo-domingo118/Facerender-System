<?php

namespace App\Livewire\Dashboard;

use App\Models\CaseRecord;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Main extends Component
{
    use WithPagination;
    
    public $search = '';
    public $statusFilter = 'all';
    public $sortBy = 'recent';
    public $selectedCase = null;
    public $showCaseDetailsModal = false;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'recent'],
    ];
    
    #[On('case-created')]
    #[On('case-updated')]
    #[On('case-deleted')]
    public function refreshCases()
    {
        // Clear the cached computed property
        unset($this->cases);
        
        $this->resetPage();
    }
    
    #[On('case-selected')]
    public function selectCase($caseId)
    {
        $this->selectedCase = $caseId;
        $this->showCaseDetailsModal = true;
    }
    
    public function createNewCase()
    {
        $this->dispatch('create-new-case');
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }
    
    public function getCasesProperty()
    {
        $query = CaseRecord::query()
            ->with(['composites', 'witnesses'])
            ->when($this->search, function ($query) {
                return $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('reference_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter != 'all', function ($query) {
                return $query->where('status', $this->statusFilter);
            });
            
        // Always order by pinned status first (pinned cases at the top)
        $query->orderByDesc('is_pinned');
            
        switch ($this->sortBy) {
            case 'alphabetical':
                $query->orderBy('title');
                break;
            case 'status':
                $query->orderBy('status');
                break;
            case 'recent':
            default:
                $query->latest('updated_at');
                break;
        }
        
        return $query->get();
    }
    
    public function render()
    {
        return view('livewire.dashboard.main', [
            'cases' => $this->cases
        ]);
    }
}
