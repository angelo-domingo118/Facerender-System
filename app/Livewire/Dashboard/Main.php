<?php

namespace App\Livewire\Dashboard;

use App\Models\CaseRecord;
use Illuminate\Support\Facades\Auth;
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
    public $contentTypeFilter = 'all';
    public $selectedCase = null;
    public $showCaseDetailsModal = false;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'recent'],
        'genderFilter' => ['except' => 'all'],
        'ethnicityFilter' => ['except' => 'all'],
        'ageRangeFilter' => ['except' => 'all'],
        'contentTypeFilter' => ['except' => 'all'],
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
    
    #[On('content-type-filter-updated')]
    public function updatedContentTypeFilter($contentTypeFilter)
    {
        $this->contentTypeFilter = $contentTypeFilter;
        $this->resetPage();
    }
    
    #[On('quick-filter')]
    public function handleQuickFilter($type)
    {
        if ($type === 'pinned') {
            // Remove pinned filter functionality
            // Reset to showing all cases instead
            $this->statusFilter = 'all';
            $this->sortBy = 'recent';
        } elseif ($type === 'recent') {
            // Show recent cases and set the sort order
            $this->sortBy = 'recent';
            $this->statusFilter = 'all';
        } elseif ($type === 'reset') {
            // Reset all filters
            $this->search = '';
            $this->statusFilter = 'all';
            $this->sortBy = 'recent';
            $this->contentTypeFilter = 'all';
            
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
        // Start with base query
        $query = CaseRecord::query();
            
        // Filter cases by the currently authenticated user
        $query->where('user_id', Auth::id());
            
        // Prepare relationship loading with potential filtering
        $searchTerm = $this->search;
        $loadWitnesses = function ($q) use ($searchTerm) {
            if ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('relationship_to_case', 'like', '%' . $searchTerm . '%');
            }
        };
        $loadComposites = function ($q) use ($searchTerm) {
            if ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            }
        };

        // Handle content type filtering and eager loading
        if ($this->contentTypeFilter === 'witnesses') {
            // Only get cases that HAVE matching witnesses
            $query->whereHas('witnesses', $loadWitnesses);
            // Eager load ONLY the matching witnesses
            $query->with(['witnesses' => $loadWitnesses]);
        } elseif ($this->contentTypeFilter === 'composites') {
            // Only get cases that HAVE matching composites
            $query->whereHas('composites', $loadComposites);
            // Eager load ONLY the matching composites
            $query->with(['composites' => $loadComposites]);
        } else {
            // Default view: Load both, unfiltered, for general search
            $query->with(['composites', 'witnesses']);
            
            // Default search behavior (search across cases, witnesses, composites)
            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', '%' . $searchTerm . '%')
                      ->orWhere('reference_number', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%')
                      ->orWhere('location', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('witnesses', function ($wq) use ($searchTerm) {
                          $wq->where('name', 'like', '%' . $searchTerm . '%')
                             ->orWhere('relationship_to_case', 'like', '%' . $searchTerm . '%');
                      })
                      ->orWhereHas('composites', function ($cq) use ($searchTerm) {
                          $cq->where('title', 'like', '%' . $searchTerm . '%')
                             ->orWhere('description', 'like', '%' . $searchTerm . '%');
                      });
                });
            }
        }
        
        // Apply status filter (applies to cases regardless of view mode)
        if ($this->statusFilter != 'all') {
            $query->where('status', $this->statusFilter);
        }
            
        // Apply the selected sort
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
