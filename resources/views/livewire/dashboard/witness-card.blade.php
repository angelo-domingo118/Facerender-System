<div class="bg-white p-3 rounded-lg border border-gray-200 hover:border-[#2C3E50]/50 transition-all duration-200 shadow-sm">
    <div class="flex items-start justify-between">
        <div class="flex items-start space-x-3">
            <div class="bg-[#2C3E50]/20 p-2 rounded-full">
                <x-icon name="user" class="h-5 w-5 text-[#2C3E50]" />
            </div>
            <div>
                <h5 class="font-medium text-[#2C3E50]">{{ $witness->name }}</h5>
                <div class="text-xs text-gray-500 flex items-center mt-1">
                    <span class="inline-flex items-center mr-2">
                        <x-icon name="identification" class="h-3 w-3 mr-1 text-gray-500" />
                        {{ $witness->age ?? 'N/A' }} yrs
                    </span>
                    <span class="inline-flex items-center">
                        <x-icon name="user-circle" class="h-3 w-3 mr-1 text-gray-500" />
                        {{ $witness->gender ?? 'N/A' }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="flex space-x-1">
            <button 
                wire:click="toggleDetails" 
                class="p-1 text-gray-500 hover:text-[#2C3E50] hover:bg-gray-100 rounded-full transition-colors duration-200"
                x-tooltip="'{{ $showDetails ? 'Hide Details' : 'Show Details' }}'"
            >
                <x-icon 
                    name="chevron-down" 
                    class="h-4 w-4 transform transition-transform duration-300"
                    :class="$showDetails ? 'rotate-180' : ''" 
                />
            </button>
            
            <x-dropdown>
                <x-slot name="trigger">
                    <button class="p-1 text-gray-500 hover:text-[#2C3E50] hover:bg-gray-100 rounded-full transition-colors duration-200">
                        <x-icon name="ellipsis-vertical" class="h-4 w-4" />
                    </button>
                </x-slot>
                
                <x-dropdown.item icon="pencil" label="Edit" wire:click="editWitness" />
                <x-dropdown.item icon="trash" label="Delete" wire:click="deleteWitness" />
            </x-dropdown>
        </div>
    </div>
    
    <!-- Expanded Details Section -->
    @if($showDetails)
        <div class="mt-3 pt-3 border-t border-gray-200 space-y-3">
            @if($witness->contact_number)
                <div class="flex items-center text-sm">
                    <x-icon name="phone" class="h-4 w-4 mr-2 text-gray-500" />
                    <span>{{ $witness->contact_number }}</span>
                </div>
            @endif
            
            @if($witness->address)
                <div class="flex items-start text-sm">
                    <x-icon name="map-pin" class="h-4 w-4 mr-2 text-gray-500 mt-0.5" />
                    <span>{{ $witness->address }}</span>
                </div>
            @endif
            
            @if($witness->relationship_to_case)
                <div class="flex items-center text-sm">
                    <x-icon name="link" class="h-4 w-4 mr-2 text-gray-500" />
                    <span>Relation: {{ $witness->relationship_to_case }}</span>
                </div>
            @endif
            
            @if($witness->interview_date)
                <div class="flex items-center text-sm">
                    <x-icon name="calendar" class="h-4 w-4 mr-2 text-gray-500" />
                    <span>Interviewed: {{ $witness->interview_date->format('M d, Y') }}</span>
                </div>
            @endif
            
            @if($witness->interview_notes)
                <div class="mt-2">
                    <p class="text-xs font-medium text-gray-500 mb-1">Interview Notes:</p>
                    <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded-md">{{ $witness->interview_notes }}</p>
                </div>
            @endif
        </div>
    @endif
</div>
