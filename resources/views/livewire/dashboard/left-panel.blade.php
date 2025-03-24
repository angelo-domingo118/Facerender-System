<div class="bg-white p-6 h-full">
    <!-- Search Bar -->
    <div class="mb-6">
        <x-input 
            placeholder="Search cases..." 
            wire:model.live="search"
            icon="magnifying-glass"
            class="w-full bg-gray-50 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500"
        />
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
        <!-- Status Filter -->
        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700">Status</label>
            <x-select
                placeholder="Filter by status"
                wire:model.live="statusFilter"
                class="w-full bg-gray-50 border-gray-200"
            >
                <x-select.option value="all" label="All Statuses" />
                <x-select.option value="active">
                    <div class="flex items-center">
                        <span class="h-2 w-2 rounded-full bg-green-500 mr-2"></span>
                        Active
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
            </x-select>
        </div>
        
        <!-- Sort Options -->
        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-700">Sort By</label>
            <x-select
                wire:model.live="sortBy"
                class="w-full bg-gray-50 border-gray-200"
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
    </div>
    
    <!-- Quick Filters -->
    <div class="mt-8">
        <h3 class="text-sm font-medium text-gray-700 mb-3">Quick Filters</h3>
        <div class="flex flex-wrap gap-2">
            <button class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
                <x-icon name="bookmark" class="w-3 h-3 mr-1" />
                Pinned
            </button>
            <button class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                <x-icon name="clock" class="w-3 h-3 mr-1" />
                Recent
            </button>
            <button class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 hover:bg-purple-100 transition-colors">
                <x-icon name="user" class="w-3 h-3 mr-1" />
                My Cases
            </button>
        </div>
    </div>
</div>
