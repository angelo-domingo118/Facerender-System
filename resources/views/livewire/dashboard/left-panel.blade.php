<div class="bg-[#2C3E50] p-4 h-full text-gray-200 flex flex-col">
    <!-- Search Bar with Clear Button -->
    <div class="mb-3">
        <x-input 
            placeholder="{{ $contentTypeFilter === 'witnesses' ? 'Search witnesses...' : ($contentTypeFilter === 'composites' ? 'Search composites...' : 'Search cases...') }}" 
            wire:model.live.debounce.300ms="search"
            icon="magnifying-glass"
            class="w-full bg-[#2C3E50] text-gray-200 border-[#3498DB]/20 focus:border-[#3498DB] focus:ring-[#3498DB]/50 placeholder-gray-400"
        >
            <x-slot name="append">
                <div class="absolute inset-y-0 right-0 flex items-center p-0.5">
                    <button 
                        x-data="{ search: @entangle('search') }" 
                        x-show="search.length > 0" 
                        @click="$wire.set('search', '')" 
                        type="button"
                        class="flex items-center justify-center h-full w-8 text-gray-400 hover:text-white focus:outline-none rounded-full hover:bg-[#34495E]"
                        aria-label="Clear search"
                    >
                        <x-icon name="x-mark" class="w-4 h-4" />
                    </button>
                </div>
            </x-slot>
        </x-input>
    </div>
    
    <!-- New Case Button -->
    <div class="mb-3">
        <x-button 
            primary 
            label="New Case" 
            icon="plus" 
            class="w-full justify-center bg-[#10B981] hover:bg-[#059669] transition-colors text-white shadow-sm rounded-md" 
            wire:click="$dispatch('create-new-case')"
        />
    </div>
    
    <!-- Divider with label -->
    <div class="relative my-4">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-[#3498DB]/20"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-[#2C3E50] text-[#3498DB] uppercase tracking-wider font-medium">Filters</span>
        </div>
    </div>
    
    <!-- Filters Section -->
    <div class="space-y-3">
        <!-- Content Type Filter -->
        <div class="space-y-1">
            <label class="text-sm font-medium text-gray-300">View Mode</label>
            <x-select
                placeholder="Select view mode"
                wire:model.live="contentTypeFilter"
                class="w-full bg-[#2C3E50] text-gray-200 border-[#3498DB]/20"
                :clearable="false"
            >
                <x-select.option value="all">
                    <div class="flex items-center">
                        <x-icon name="document-text" class="w-4 h-4 mr-2 text-[#3498DB]" />
                        Cases (Default)
                    </div>
                </x-select.option>
                <x-select.option value="witnesses">
                    <div class="flex items-center">
                        <x-icon name="users" class="w-4 h-4 mr-2 text-[#3498DB]" />
                        Witnesses Only
                    </div>
                </x-select.option>
                <x-select.option value="composites">
                    <div class="flex items-center">
                        <x-icon name="photo" class="w-4 h-4 mr-2 text-[#3498DB]" />
                        Composites Only
                    </div>
                </x-select.option>
            </x-select>
        </div>
        
        <!-- Status Filter -->
        <div class="space-y-1">
            <label class="text-sm font-medium text-gray-300">Status</label>
            <x-select
                placeholder="Filter by status"
                wire:model.live="statusFilter"
                class="w-full bg-[#2C3E50] text-gray-200 border-[#3498DB]/20"
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
        <div class="space-y-1">
            <label class="text-sm font-medium text-gray-300">Sort By</label>
            <x-select
                wire:model.live="sortBy"
                class="w-full bg-[#2C3E50] text-gray-200 border-[#3498DB]/20"
                :clearable="false"
            >
                <x-select.option value="recent">
                    <div class="flex items-center">
                        <x-icon name="clock" class="w-4 h-4 mr-2 text-[#3498DB]" />
                        Most Recent
                    </div>
                </x-select.option>
                <x-select.option value="alphabetical">
                    <div class="flex items-center">
                        <x-icon name="arrows-up-down" class="w-4 h-4 mr-2 text-[#3498DB]" />
                        Alphabetical
                    </div>
                </x-select.option>
                <x-select.option value="status">
                    <div class="flex items-center">
                        <x-icon name="adjustments-horizontal" class="w-4 h-4 mr-2 text-[#3498DB]" />
                        Status
                    </div>
                </x-select.option>
            </x-select>
        </div>
        
        <!-- Reset Filters Button -->
        <div class="pt-2">
            <x-button 
                wire:click="$dispatch('quick-filter', { type: 'reset' })"
                label="Reset Filters"
                icon="x-mark"
                class="w-full justify-center bg-[#E74C3C] hover:bg-[#C0392B] text-white border-0"
            />
        </div>
    </div>
    
    <!-- Collapse Panel Button -->
    <div class="mt-auto pt-4">
        <button 
            x-data
            @click="$dispatch('toggle-sidebar')" 
            class="w-full flex items-center justify-center space-x-2 text-sm py-2 rounded bg-[#34495E] hover:bg-[#3498DB] transition-colors duration-200 text-white"
        >
            <span>Collapse Panel</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
    </div>
</div>
