<div class="w-full">
    <div x-data="{ isExpanded: @entangle('isExpanded') }" class="w-full transition-all duration-300">
        {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}
        <x-card class="w-full border-l-4 border-l-[#2C3E50] transition-all duration-200 shadow-md overflow-hidden">
            <!-- Case Header -->
            <div class="flex flex-col space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <!-- Pin/Favorite Button -->
                        <button 
                            wire:click="togglePin" 
                            class="text-gray-500 hover:text-[#2C3E50] focus:outline-none transition-colors duration-200 p-1.5 rounded-full hover:bg-gray-100"
                            x-tooltip="'{{ $case->is_pinned ? 'Unpin Case' : 'Pin Case' }}'"
                        >
                            <x-icon 
                                name="{{ $case->is_pinned ? 'map-pin' : 'map-pin' }}" 
                                class="h-5 w-5 {{ $case->is_pinned ? 'text-[#2C3E50]' : '' }}" 
                                :solid="$case->is_pinned"
                            />
                        </button>
                        
                        <!-- Title and Reference Number -->
                        <div>
                            <div class="flex items-center">
                                <h3 class="text-lg font-semibold text-[#2C3E50] group-hover:text-[#2C3E50]">{{ $case->title }}</h3>
                                @if($case->is_pinned)
                                    <span class="ml-2 text-xs bg-[#2C3E50]/20 text-[#2C3E50] px-2 py-0.5 rounded-full font-medium">Pinned</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 flex items-center">
                                <x-icon name="hashtag" class="h-3 w-3 mr-1 text-[#2C3E50]" />
                                {{ $case->reference_number }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <!-- Status Badge -->
                        <x-badge 
                            :label="ucfirst($case->status)" 
                            :color="
                                $case->status === 'open' ? 'positive' : 
                                ($case->status === 'pending' ? 'warning' : 
                                ($case->status === 'closed' ? 'negative' : 
                                ($case->status === 'archived' ? 'slate' : 'neutral')))
                            "
                            class="font-medium"
                            rounded="full"
                        />
                        
                        <!-- Expand/Collapse Button -->
                        <button 
                            wire:click="toggleExpand" 
                            class="text-gray-500 hover:text-[#2C3E50] focus:outline-none transition-colors duration-200 p-1.5 rounded-full hover:bg-gray-100"
                            x-tooltip="isExpanded ? 'Collapse Details' : 'Expand Details'"
                        >
                            <x-icon 
                                name="chevron-down" 
                                class="h-5 w-5 transform transition-transform duration-300"
                                x-bind:class="isExpanded ? 'rotate-180' : ''" 
                            />
                        </button>
                        
                        <!-- Actions Dropdown -->
                        <x-dropdown>
                            <x-slot name="trigger">
                                <button class="p-1.5 rounded-full hover:bg-gray-100 focus:outline-none">
                                    <x-icon name="ellipsis-vertical" class="h-5 w-5 text-gray-500 cursor-pointer hover:text-[#2C3E50]" />
                                </button>
                            </x-slot>
                            
                            <x-dropdown.item icon="pencil" label="Edit Case" wire:click="editCase" />
                            <x-dropdown.item icon="trash" label="Delete Case" wire:click="deleteCase" class="text-gray-700" />
                        </x-dropdown>
                    </div>
                </div>

                <!-- Metadata and Last Updated -->
                <div class="flex justify-between items-center text-xs text-gray-600 border-t border-gray-200 pt-3">
                    <div class="flex items-center bg-gray-100 px-2 py-1 rounded-md">
                        <x-icon name="clock" class="h-3 w-3 mr-1 text-[#2C3E50]" />
                        Last updated: {{ $case->updated_at->diffForHumans() }}
                    </div>
                    <div class="flex items-center bg-gray-100 px-2 py-1 rounded-md">
                        <x-icon name="calendar" class="h-3 w-3 mr-1 text-[#2C3E50]" />
                        Created: {{ $case->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
            
            <!-- Collapsible Content -->
            <div 
                x-show="isExpanded"
                x-transition:enter="transition ease-out duration-200" 
                x-transition:enter-start="opacity-0 transform -translate-y-2" 
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-150" 
                x-transition:leave-start="opacity-100 transform translate-y-0" 
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="mt-4"
            >
                <div class="pt-2 border-t border-gray-200">
                    <!-- Case Info Summary -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
                        <div class="flex flex-col p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <span class="text-xs text-gray-600 mb-1 font-medium">Incident Type</span>
                            <div class="flex items-center">
                                <div class="bg-[#2C3E50]/20 p-1.5 rounded-md mr-2">
                                    <x-icon name="identification" class="h-4 w-4 text-[#2C3E50]" />
                                </div>
                                <span class="text-sm font-medium text-[#2C3E50]">{{ $case->incident_type }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <span class="text-xs text-gray-600 mb-1 font-medium">Incident Date</span>
                            <div class="flex items-center">
                                <div class="bg-[#2C3E50]/20 p-1.5 rounded-md mr-2">
                                    <x-icon name="calendar" class="h-4 w-4 text-[#2C3E50]" />
                                </div>
                                <span class="text-sm font-medium text-[#2C3E50]">{{ $case->incident_date->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <span class="text-xs text-gray-600 mb-1 font-medium">Witnesses</span>
                            <div class="flex items-center">
                                <div class="bg-[#2C3E50]/20 p-1.5 rounded-md mr-2">
                                    <x-icon name="users" class="h-4 w-4 text-[#2C3E50]" />
                                </div>
                                <span class="text-sm font-medium text-[#2C3E50]">{{ $case->witnesses->count() }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <span class="text-xs text-gray-600 mb-1 font-medium">Location</span>
                            <div class="flex items-center">
                                <div class="bg-[#2C3E50]/20 p-1.5 rounded-md mr-2">
                                    <x-icon name="map-pin" class="h-4 w-4 text-[#2C3E50]" />
                                </div>
                                <span class="text-sm font-medium text-[#2C3E50]">{{ $case->location ?? 'Not specified' }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description Section (if available) -->
                    @if($case->description)
                    <div class="mb-4 hover:bg-gray-100 p-2 rounded-lg transition-colors duration-200">
                        <h4 class="text-sm font-medium text-[#2C3E50] mb-2 flex items-center">
                            <x-icon name="document-text" class="h-4 w-4 mr-2 text-[#2C3E50]" />
                            Description
                        </h4>
                        <div class="bg-white p-3 rounded-lg border border-gray-200">
                            <p class="text-sm text-gray-700">{{ $case->description }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Composite Section -->
                    <div class="mt-6 hover:bg-gray-100 p-2 rounded-lg transition-colors duration-200 {{ request()->query('contentTypeFilter') === 'composites' ? 'bg-blue-50 shadow-sm border border-blue-100' : '' }}">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-[#2C3E50] flex items-center">
                                <x-icon name="photo" class="h-4 w-4 mr-2 text-[#2C3E50]" />
                                Composites <span class="ml-1 bg-white text-gray-700 text-xs py-0.5 px-2 rounded-full border border-gray-300">{{ $case->composites->count() }}</span>
                            </h4>
                            <x-button 
                                wire:click="createComposite" 
                                xs
                                icon="plus" 
                                label="Add Composite" 
                                class="bg-[#2C3E50] hover:bg-[#34495E] text-white shadow-sm"
                                rounded
                            />
                        </div>
                        
                        @if($case->composites->isEmpty())
                            <div class="text-center py-6 bg-white rounded-lg border border-gray-300 transition-all duration-200 hover:border-[#2C3E50]/50">
                                <div class="bg-[#2C3E50]/20 p-3 rounded-full inline-flex mb-2">
                                    <x-icon name="photo" class="h-6 w-6 text-[#2C3E50]" />
                                </div>
                                <p class="text-gray-600 text-sm">No composites yet</p>
                                <x-button 
                                    wire:click="createComposite" 
                                    flat 
                                    xs
                                    icon="plus" 
                                    label="Create First Composite" 
                                    class="mt-2 text-[#2C3E50] hover:bg-[#2C3E50]/20" 
                                    rounded
                                />
                            </div>
                        @else
                            <div class="space-y-3" wire:key="composites-{{ $case->updated_at->timestamp }}">
                                @foreach($case->composites as $composite)
                                    @livewire('dashboard.composite-card', ['composite' => $composite], key('composite-'.$composite->id))
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- Witnesses Section -->
                    <div class="mt-6 hover:bg-gray-100 p-2 rounded-lg transition-colors duration-200 {{ request()->query('contentTypeFilter') === 'witnesses' ? 'bg-blue-50 shadow-sm border border-blue-100' : '' }}">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-[#2C3E50] flex items-center">
                                <x-icon name="users" class="h-4 w-4 mr-2 text-[#2C3E50]" />
                                Witnesses <span class="ml-1 bg-white text-gray-700 text-xs py-0.5 px-2 rounded-full border border-gray-300">{{ $case->witnesses->count() }}</span>
                            </h4>
                            <x-button 
                                wire:click="addWitness" 
                                xs
                                icon="plus" 
                                label="Add Witness" 
                                class="bg-[#2C3E50] hover:bg-[#34495E] text-white shadow-sm"
                                rounded
                            />
                        </div>
                        
                        @if($case->witnesses->isEmpty())
                            <div class="text-center py-6 bg-white rounded-lg border border-gray-300 transition-all duration-200 hover:border-[#2C3E50]/50">
                                <div class="bg-[#2C3E50]/20 p-3 rounded-full inline-flex mb-2">
                                    <x-icon name="users" class="h-6 w-6 text-[#2C3E50]" />
                                </div>
                                <p class="text-gray-600 text-sm">No witnesses yet</p>
                                <x-button 
                                    wire:click="addWitness" 
                                    flat 
                                    xs
                                    icon="plus" 
                                    label="Add First Witness" 
                                    class="mt-2 text-[#2C3E50] hover:bg-[#2C3E50]/20" 
                                    rounded
                                />
                            </div>
                        @else
                            <div class="space-y-3" wire:key="witnesses-{{ $case->updated_at->timestamp }}">
                                @foreach($case->witnesses as $witness)
                                    @livewire('dashboard.witness-card', ['witness' => $witness], key('witness-'.$witness->id))
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</div>

