<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;

class LeftPanel extends Component
{
    public $search = '';
    public $statusFilter = 'all';
    public $sortBy = 'recent';
    public $genderFilter = 'all';
    public $ethnicityFilter = 'all';
    public $ageRangeFilter = 'all';
    
    public function mount($search, $statusFilter, $sortBy, $genderFilter = 'all', $ethnicityFilter = 'all', $ageRangeFilter = 'all')
    {
        $this->search = $search;
        $this->statusFilter = $statusFilter;
        $this->sortBy = $sortBy;
        $this->genderFilter = $genderFilter;
        $this->ethnicityFilter = $ethnicityFilter;
        $this->ageRangeFilter = $ageRangeFilter;
    }
    
    #[On('filters-reset')]
    public function resetFilterStates()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->sortBy = 'recent';
        $this->genderFilter = 'all';
        $this->ethnicityFilter = 'all';
        $this->ageRangeFilter = 'all';
    }
    
    public function updatedSearch()
    {
        $this->dispatch('search-updated', search: $this->search);
    }
    
    public function updatedStatusFilter()
    {
        $this->dispatch('status-filter-updated', statusFilter: $this->statusFilter);
    }
    
    public function updatedSortBy()
    {
        $this->dispatch('sort-updated', sortBy: $this->sortBy);
    }
    
    public function updatedGenderFilter()
    {
        $this->dispatch('gender-filter-updated', genderFilter: $this->genderFilter);
    }
    
    public function updatedEthnicityFilter()
    {
        $this->dispatch('ethnicity-filter-updated', ethnicityFilter: $this->ethnicityFilter);
    }
    
    public function updatedAgeRangeFilter()
    {
        $this->dispatch('age-range-filter-updated', ageRangeFilter: $this->ageRangeFilter);
    }
    
    public function createNewCase()
    {
        $this->dispatch('create-new-case');
    }
    
    public function render()
    {
        return view('livewire.dashboard.left-panel');
    }
}
