<div x-data="{ isExpanded: @entangle('isExpanded') }" class="bg-white p-3 rounded-lg border border-gray-200 hover:border-[#2C3E50]/50 transition-all duration-200 shadow-sm overflow-hidden">
    <div class="flex items-start justify-between">
        <div class="flex items-start space-x-3">
            <div class="bg-[#2C3E50]/20 p-2 rounded-full">
                <x-icon name="user" class="h-5 w-5 text-[#2C3E50]" />
            </div>
            <div>
                <h5 class="font-medium text-[#2C3E50]">{{ $witness->name }}</h5>
                <div class="text-xs text-gray-700 flex items-center mt-1">
                    <span class="inline-flex items-center mr-2">
                        <x-icon name="identification" class="h-3 w-3 mr-1 text-gray-700" />
                        {{ $witness->age ?? 'N/A' }} yrs
                    </span>
                    <span class="inline-flex items-center">
                        <x-icon name="user-circle" class="h-3 w-3 mr-1 text-gray-700" />
                        {{ $witness->gender ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            <!-- Action Buttons -->
            <x-button 
                wire:click="editWitness" 
                icon="pencil-square" 
                size="sm"
                class="bg-[#6366F1] hover:bg-[#4F46E5] text-white transition-colors rounded-md"
            >
                Edit Witness
            </x-button>
            <x-button 
                wire:click="deleteWitness" 
                icon="trash" 
                size="sm"
                outline
                class="text-red-600 border-red-300 hover:bg-red-50 transition-colors rounded-md"
            >
                Delete Witness
            </x-button>
            
            <!-- Details Toggle Button -->
            <button 
                wire:click="toggleExpand" 
                class="flex items-center space-x-1 text-sm text-gray-600 hover:text-[#2C3E50] focus:outline-none transition-colors duration-200 px-2 py-1 rounded-md hover:bg-gray-200 focus:bg-gray-200"
            >
                <x-icon 
                    name="chevron-down" 
                    class="h-4 w-4 transform transition-transform duration-300"
                    x-bind:class="isExpanded ? 'rotate-180' : ''" 
                />
                 <span x-text="isExpanded ? 'Hide Details' : 'Show Details'"></span>
            </button>
        </div>
    </div>
    
    <!-- Expanded Details Section -->
    <div 
        x-show="isExpanded"
        x-transition:enter="transition ease-out duration-200" 
        x-transition:enter-start="opacity-0 transform -translate-y-2" 
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150" 
        x-transition:leave-start="opacity-100 transform translate-y-0" 
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="mt-3 pt-3 border-t border-gray-200 space-y-3"
    >
        @if($witness->contact_number)
            <div class="flex items-center text-sm bg-gray-50 p-2 rounded-md">
                <x-icon name="phone" class="h-4 w-4 mr-2 text-[#2C3E50]" />
                <span class="text-gray-800">{{ $witness->contact_number }}</span>
            </div>
        @endif
        
        @if($witness->address)
            <div class="flex items-start text-sm bg-gray-50 p-2 rounded-md">
                <x-icon name="map-pin" class="h-4 w-4 mr-2 text-[#2C3E50] mt-0.5" />
                <span class="text-gray-800">{{ $witness->address }}</span>
            </div>
        @endif
        
        @if($witness->relationship_to_case)
            <div class="flex items-center text-sm bg-gray-50 p-2 rounded-md">
                <x-icon name="link" class="h-4 w-4 mr-2 text-[#2C3E50]" />
                <span class="text-gray-800">Relation: {{ $witness->relationship_to_case }}</span>
            </div>
        @endif
        
        @if($witness->interview_date)
            <div class="flex items-center text-sm bg-gray-50 p-2 rounded-md">
                <x-icon name="calendar" class="h-4 w-4 mr-2 text-[#2C3E50]" />
                <span class="text-gray-800">Interviewed: {{ $witness->interview_date->format('M d, Y') }}</span>
            </div>
        @endif
        
        @if($witness->interview_notes)
            <div class="mt-2">
                <p class="text-xs font-medium text-gray-700 mb-1">Interview Notes:</p>
                <p class="text-sm text-gray-800 bg-gray-50 p-2 rounded-md">{{ $witness->interview_notes }}</p>
            </div>
        @endif
    </div>
</div>
