<div class="bg-white p-6 h-full">
    <!-- Search Bar with Clear Button -->
    <div class="mb-6">
        <x-input 
            placeholder="{{ $contentTypeFilter === 'witnesses' ? 'Search witnesses...' : ($contentTypeFilter === 'composites' ? 'Search composites...' : 'Search cases...') }}" 
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            class="w-full bg-gray-50 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
        >
            <x-slot name="append">
                <div class="absolute inset-y-0 right-0 flex items-center p-0.5">
                    <button 
                        x-data="{ search: @entangle('search') }" 
                        x-show="search.length > 0" 
                        @click="$wire.set('search', '')" 
                        type="button"
                        class="flex items-center justify-center h-full w-8 text-gray-400 hover:text-gray-600 focus:outline-none rounded-full hover:bg-gray-100"
                        aria-label="Clear search"
                    >
                        <x-icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>
            </x-slot>
        </x-input>
    </div>
    
    <!-- Divider with label -->
    <div class="relative my-8">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500 uppercase tracking-wider font-medium">Filters</span>
        </div>
    </div>
    
    <!-- Filters Section -->
    <div class="space-y-6">
        <!-- Content Type Filter -->
        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700">View Mode</label>
            <x-select
                placeholder="Select view mode"
                wire:model.live="contentTypeFilter"
                class="w-full bg-gray-50 border-gray-200"
                :clearable="false"
            >
                <x-select.option value="all" label="Cases (Default)" />
                <x-select.option value="witnesses">
                    <div class="flex items-center">
                        <x-icon name="users" class="w-4 h-4 mr-2 text-gray-500" />
                        Witnesses Only
                    </div>
                </x-select.option>
                <x-select.option value="composites">
                    <div class="flex items-center">
                        <x-icon name="photo" class="w-4 h-4 mr-2 text-gray-500" />
                        Composites Only
                    </div>
                </x-select.option>
            </x-select>
            <p class="text-xs text-gray-500 italic">
                Choose "Witnesses Only" or "Composites Only" to view specific items across all cases.
            </p>
        </div>
        
        <!-- Status Filter -->
        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700">Status</label>
            <x-select
                placeholder="Filter by status"
                wire:model.live="statusFilter"
                class="w-full bg-gray-50 border-gray-200"
                :clearable="false"
            >
                <x-select.option value="all" label="All Statuses" />
                <x-select.option value="open">
                    <div class="flex items-center">
                        <span class="h-2 w-2 rounded-full bg-green-500 mr-2"></span>
                        Open
                    </div>
                </x-select.option>
                <x-select.option value="pending">
                    <div class="flex items-center">
                        <span class="h-2 w-2 rounded-full bg-yellow-500 mr-2"></span>
                        Pending
                    </div>
                </x-select.option>
                <x-select.option value="closed">
                    <div class="flex items-center">
                        <span class="h-2 w-2 rounded-full bg-gray-500 mr-2"></span>
                        Closed
                    </div>
                </x-select.option>
                <x-select.option value="archived">
                    <div class="flex items-center">
                        <span class="h-2 w-2 rounded-full bg-purple-500 mr-2"></span>
                        Archived
                    </div>
                </x-select.option>
            </x-select>
        </div>
        
        <!-- Sort Options -->
        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700">Sort By</label>
            <x-select
                wire:model.live="sortBy"
                class="w-full bg-gray-50 border-gray-200"
                :clearable="false"
            >
                <x-select.option value="recent">
                    <div class="flex items-center">
                        <x-icon name="clock" class="w-4 h-4 mr-2 text-gray-500" />
                        Most Recent
                    </div>
                </x-select.option>
                <x-select.option value="alphabetical">
                    <div class="flex items-center">
                        <x-icon name="arrows-up-down" class="w-4 h-4 mr-2 text-gray-500" />
                        Alphabetical
                    </div>
                </x-select.option>
                <x-select.option value="status">
                    <div class="flex items-center">
                        <x-icon name="adjustments-horizontal" class="w-4 h-4 mr-2 text-gray-500" />
                        Status
                    </div>
                </x-select.option>
            </x-select>
        </div>
        
        <!-- Reset Filters Button -->
        <div class="pt-4">
            <x-button 
                wire:click="$dispatch('quick-filter', { type: 'reset' })"
                label="Reset Filters"
                icon="x-mark"
                negative
                class="w-full justify-center"
            />
        </div>
    </div>
</div>
