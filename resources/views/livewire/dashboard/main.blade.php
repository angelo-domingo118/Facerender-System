<div x-data="{ isCollapsed: false }" class="flex flex-col md:flex-row gap-6 min-h-screen w-full">
    <!-- Left Panel with collapse button -->
    <div class="relative w-full md:w-auto">
        <!-- Left Panel Container with sticky wrapper -->
        <div class="md:sticky md:top-4">
            <div class="relative">
                <!-- Collapse toggle button -->
                <button 
                    @click="isCollapsed = !isCollapsed" 
                    class="absolute right-0 top-4 transform translate-x-1/2 z-20 bg-[#3498DB] text-white rounded-full p-1.5 shadow-lg hover:bg-[#2980B9] transition-all duration-200 hover:scale-110"
                    :class="isCollapsed ? 'rotate-180' : ''"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                
                <!-- Panel content container -->
                <div 
                    class="transition-all duration-300 ease-in-out overflow-hidden bg-white rounded-lg shadow-lg"
                    :class="isCollapsed ? 'w-0 opacity-0 md:invisible' : 'w-full md:w-80 lg:w-96 opacity-100 md:visible'"
                    style="transition-property: width, opacity, visibility;"
                >
                    @livewire('dashboard.left-panel', [
                        'search' => $search,
                        'statusFilter' => $statusFilter,
                        'sortBy' => $sortBy
                    ])
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="flex-1 space-y-6 pb-6 px-2">
        <!-- Dashboard indicator -->
        <div class="bg-white rounded-lg shadow-md p-4 border border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-[#2C3E50]">Dashboard</h2>
                <x-button 
                    primary 
                    label="New Case" 
                    icon="plus" 
                    class="bg-[#3498DB] hover:bg-[#2980B9] transition-colors text-white" 
                    wire:click="createNewCase"
                />
            </div>
        </div>
        
        @if($cases->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 bg-white rounded-lg shadow-md border border-gray-200">
                <div class="bg-gray-50 rounded-full p-4 mb-4">
                    <x-icon name="document-magnifying-glass" class="w-12 h-12 text-gray-400"/>
                </div>
                <h3 class="text-lg font-medium text-[#2C3E50]">No cases found</h3>
                <p class="text-gray-500 mt-2 text-center max-w-md">
                    @if($search)
                        No cases match your search criteria. Try adjusting your filters.
                    @else
                        You haven't created any cases yet. Click the "New Case" button to get started.
                    @endif
                </p>
                <x-button 
                    primary 
                    label="Create New Case" 
                    icon="plus" 
                    class="mt-6 bg-[#3498DB] hover:bg-[#2980B9] transition-colors text-white transform hover:scale-105" 
                    wire:click="createNewCase"
                />
            </div>
        @else
            <div class="grid grid-cols-1 gap-5">
                @foreach($cases as $case)
                    @livewire('dashboard.case-card', ['case' => $case], key('case-'.$case->id))
                @endforeach
            </div>
        @endif
    </div>
    
    <!-- Case Details Modal -->
    @livewire('dashboard.case-details-modal')
    
    <!-- Create Case Form -->
    @livewire('forms.create-case-form')
    
    <!-- Edit Case Form -->
    @livewire('forms.edit-case-form')
</div>
