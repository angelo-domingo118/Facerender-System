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
    public $genderFilter = 'all';
    public $ethnicityFilter = 'all';
    public $ageRangeFilter = 'all';
    public $selectedCase = null;
    public $showCaseDetailsModal = false;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'recent'],
        'genderFilter' => ['except' => 'all'],
        'ethnicityFilter' => ['except' => 'all'],
        'ageRangeFilter' => ['except' => 'all'],
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
    
    #[On('gender-filter-updated')]
    public function updatedGenderFilter($value)
    {
        $this->genderFilter = $value;
        $this->resetPage();
    }
    
    #[On('ethnicity-filter-updated')]
    public function updatedEthnicityFilter($value)
    {
        $this->ethnicityFilter = $value;
        $this->resetPage();
    }
    
    #[On('age-range-filter-updated')]
    public function updatedAgeRangeFilter($value)
    {
        $this->ageRangeFilter = $value;
        $this->resetPage();
    }
    
    #[On('quick-filter')]
    public function handleQuickFilter($type)
    {
        if ($type === 'pinned') {
            // Show only pinned cases
            $this->statusFilter = 'all';
            // Add logic to filter by pinned status only
            // This is handled in the query by default, we just reset the other filters
        } elseif ($type === 'recent') {
            // Show recent cases and set the sort order
            $this->sortBy = 'recent';
            $this->statusFilter = 'all';
        } elseif ($type === 'reset') {
            // Reset all filters
            $this->search = '';
            $this->statusFilter = 'all';
            $this->sortBy = 'recent';
            
            // Notify the left panel about the reset
            $this->dispatch('filters-reset');
        }
        
        $this->resetPage();
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
    
    #[On('search-updated')]
    public function updatedSearchFromPanel($search)
    {
        $this->search = $search;
        $this->resetPage();
    }
    
    #[On('status-filter-updated')]
    public function updatedStatusFilterFromPanel($statusFilter)
    {
        $this->statusFilter = $statusFilter;
        $this->resetPage();
    }
    
    #[On('sort-updated')]
    public function updatedSortByFromPanel($sortBy)
    {
        $this->sortBy = $sortBy;
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
            
        // Apply the selected sort first
        switch ($this->sortBy) {
            case 'alphabetical':
                $query->orderBy('title')->orderByDesc('is_pinned');
                break;
            case 'status':
                $query->orderBy('status')->orderByDesc('is_pinned');
                break;
            case 'recent':
            default:
                // For recent sorting, we still want pinned items at the top
                $query->orderByDesc('is_pinned')->latest('updated_at');
                break;
        }
        
        return $query->get();
    }
    
    public function getTotalCasesCountProperty()
    {
        return CaseRecord::count();
    }
    
    public function render()
    {
        return view('livewire.dashboard.main', [
            'cases' => $this->cases,
            'totalCasesCount' => $this->totalCasesCount
        ]);
    }
}
