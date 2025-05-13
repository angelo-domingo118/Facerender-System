<div x-data="{ isCollapsed: false }" 
    @toggle-sidebar.window="isCollapsed = !isCollapsed" 
    class="flex flex-col md:flex-row h-auto w-full">
    <!-- Left Panel with collapse button -->
    <div class="relative md:w-auto">
        <!-- Left Panel Container with sticky wrapper -->
        <div class="md:sticky md:top-4">
            <div class="relative">
                <!-- Expand button (only visible when sidebar is collapsed) -->
                <button 
                    x-show="isCollapsed"
                    @click="isCollapsed = false" 
                    x-data="{ isScrolled: false }"
                    @scroll.window="isScrolled = window.scrollY > 50"
                    :class="isScrolled ? 'top-4' : 'top-24'"
                    class="fixed left-4 z-50 bg-[#3498DB] hover:bg-[#2980B9] text-white rounded-lg p-2 transition-all duration-200 flex items-center justify-center shadow-md"
                    x-transition
                    aria-label="Expand sidebar"
                >
                    <span class="text-sm mr-2">Filters</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                
                <!-- Panel content container -->
                <div 
                    class="transition-all duration-300 ease-in-out overflow-hidden rounded-lg"
                    :class="isCollapsed ? 'w-0 opacity-0 md:invisible' : 'w-full md:w-[280px] lg:w-[320px] opacity-100 md:visible'"
                    style="transition-property: width, opacity, visibility;"
                >
                    @livewire('dashboard.left-panel', [
                        'search' => $search,
                        'statusFilter' => $statusFilter,
                        'sortBy' => $sortBy,
                        'genderFilter' => $genderFilter,
                        'ethnicityFilter' => $ethnicityFilter,
                        'ageRangeFilter' => $ageRangeFilter,
                        'contentTypeFilter' => $contentTypeFilter
                    ])
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Area - Full Width with left margin -->
    <div class="flex-1 space-y-6 pb-6 px-0 w-full md:ml-4 lg:ml-6">
        <!-- Dashboard indicator - REMOVED -->
        
        @if($cases->isEmpty() && ($contentTypeFilter === 'witnesses' || $contentTypeFilter === 'composites') && $search)
            <div class="flex flex-col items-center justify-center py-12 bg-white rounded-lg shadow-md border border-gray-200">
                <div class="bg-gray-50 rounded-full p-4 mb-4">
                    <x-icon name="funnel" class="w-12 h-12 text-gray-400"/>
                </div>
                <h3 class="text-lg font-medium text-[#2C3E50]">
                    @if($contentTypeFilter === 'witnesses')
                        No matching witnesses found
                    @else
                        No matching composites found
                    @endif
                </h3>
                <p class="text-gray-500 mt-2 text-center max-w-md">
                    No {{ $contentTypeFilter === 'witnesses' ? 'witnesses' : 'composites' }} match your current search criteria.
                    Try adjusting your search or 
                    <button wire:click="$dispatch('quick-filter', { type: 'reset' })" class="text-[#3498DB] hover:underline">
                        resetting the filters
                    </button>.
                </p>
                <x-button 
                    secondary
                    label="Reset Filters"
                    icon="arrow-path"
                    class="mt-6"
                    wire:click="$dispatch('quick-filter', { type: 'reset' })"
                />
            </div>
        @elseif($cases->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 bg-white rounded-lg shadow-md border border-gray-200">
                <div class="bg-gray-50 rounded-full p-4 mb-4">
                    <x-icon name="{{ $this->totalCasesCount > 0 ? 'funnel' : 'document-magnifying-glass' }}" class="w-12 h-12 text-gray-400"/>
                </div>
                <h3 class="text-lg font-medium text-[#2C3E50]">
                    {{ $this->totalCasesCount > 0 ? 'No matching cases' : 'No cases found' }}
                </h3>
                <p class="text-gray-500 mt-2 text-center max-w-md">
                    @if($this->totalCasesCount > 0)
                        No cases match your current filter or search criteria. 
                        <button wire:click="$dispatch('quick-filter', { type: 'reset' })" class="text-[#3498DB] hover:underline">
                            Reset filters
                        </button> to see all cases.
                    @else
                        You haven't created any cases yet. Click the "New Case" button to get started.
                    @endif
                </p>
                @if($this->totalCasesCount > 0)
                    <x-button 
                        secondary
                        label="Reset Filters"
                        icon="arrow-path"
                        class="mt-6"
                        wire:click="$dispatch('quick-filter', { type: 'reset' })"
                    />
                @else
                     <x-button 
                        primary 
                        label="Create New Case" 
                        icon="plus" 
                        class="mt-6 bg-[#10B981] hover:bg-[#059669] transition-colors text-white shadow-sm rounded-md transform hover:scale-105" 
                        wire:click="createNewCase"
                    />
                @endif
            </div>
        @else
            <!-- Witness View -->
            @if($contentTypeFilter === 'witnesses')
                <div class="grid grid-cols-1 gap-5 w-full">
                    @foreach($cases as $case)
                        @foreach($case->witnesses as $witness)
                            <div class="bg-white p-3 rounded-lg border border-gray-200 hover:border-[#2C3E50]/50 transition-all duration-200 shadow-sm">
                                <div class="flex justify-between mb-2">
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded-md text-gray-600">
                                        {{ $case->reference_number }} · {{ ucfirst($case->status) }}
                                    </span>
                                    <x-badge 
                                        :label="$case->title" 
                                        class="text-xs"
                                        rounded="full"
                                        color="slate"
                                    />
                                </div>
                                <div class="mt-2">
                                    @livewire('dashboard.witness-card', ['witness' => $witness], key('standalone-witness-'.$witness->id))
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            <!-- Composite View -->
            @elseif($contentTypeFilter === 'composites')
                <div class="grid grid-cols-1 gap-5 w-full">
                    @foreach($cases as $case)
                        @foreach($case->composites as $composite)
                            <div class="bg-white p-3 rounded-lg border border-gray-200 hover:border-[#2C3E50]/50 transition-all duration-200 shadow-sm">
                                <div class="flex justify-between mb-2">
                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded-md text-gray-600">
                                        {{ $case->reference_number }} · {{ ucfirst($case->status) }}
                                    </span>
                                    <x-badge 
                                        :label="$case->title" 
                                        class="text-xs"
                                        rounded="full"
                                        color="slate"
                                    />
                                </div>
                                <div class="mt-2">
                                    @livewire('dashboard.composite-card', ['composite' => $composite], key('standalone-composite-'.$composite->id))
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            <!-- Default Case View -->
            @else
                <div class="grid grid-cols-1 gap-5 w-full">
                    @foreach($cases as $case)
                        @livewire('dashboard.case-card', ['case' => $case], key('case-'.$case->id))
                    @endforeach
                </div>
            @endif
        @endif
    </div>
    
    <!-- Case Details Modal -->
    @livewire('dashboard.case-details-modal')
    
    <!-- Composite Details Modal -->
    @livewire('dashboard.composite-details-modal')
    
    <!-- Create Case Form -->
    @livewire('forms.create-case-form')
</div>
