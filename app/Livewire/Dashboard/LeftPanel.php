<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class LeftPanel extends Component
{
    public $search = '';
    public $statusFilter = 'all';
    public $sortBy = 'recent';
    
    public function mount($search, $statusFilter, $sortBy)
    {
        $this->search = $search;
        $this->statusFilter = $statusFilter;
        $this->sortBy = $sortBy;
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
    
    public function createNewCase()
    {
        $this->dispatch('create-new-case');
    }
    
    public function render()
    {
        return view('livewire.dashboard.left-panel');
    }
}
