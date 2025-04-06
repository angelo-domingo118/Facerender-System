<div>
    <x-modal 
        wire:model.live="show" 
        max-width="16xl" 
        blur="md"
        align="center"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        x-on:hidden="$dispatch('hidden')"
    >
        <x-card :title="$isEditing ? 'Edit Composite' : 'Composite Details'" class="overflow-hidden bg-gray-50">
            @if($composite)
                <div wire:loading.delay class="absolute inset-0 bg-white/80 z-50 flex items-center justify-center">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#2C3E50]"></div>
                        <span class="mt-2 text-sm text-gray-600">Loading...</span>
                    </div>
                </div>
                
                <!-- Tabs Navigation -->
                <div x-data="{ activeTab: 'composite-image' }" class="mb-5">
                    <div class="flex border-b border-gray-200">
                        <button 
                            @click="activeTab = 'composite-image'" 
                            :class="{'text-[#2C3E50] border-[#2C3E50] border-b-2 -mb-px py-3': activeTab === 'composite-image', 'text-gray-500 hover:text-gray-700 py-3': activeTab !== 'composite-image'}"
                            class="flex items-center px-4 font-medium text-sm focus:outline-none transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Composite Image
                        </button>
                        
                        <button 
                            @click="activeTab = 'basic-info'" 
                            :class="{'text-[#2C3E50] border-[#2C3E50] border-b-2 -mb-px py-3': activeTab === 'basic-info', 'text-gray-500 hover:text-gray-700 py-3': activeTab !== 'basic-info'}"
                            class="flex items-center px-4 font-medium text-sm focus:outline-none transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Basic Information
                        </button>
                        
                        <button 
                            @click="activeTab = 'suspect-desc'" 
                            :class="{'text-[#2C3E50] border-[#2C3E50] border-b-2 -mb-px py-3': activeTab === 'suspect-desc', 'text-gray-500 hover:text-gray-700 py-3': activeTab !== 'suspect-desc'}"
                            class="flex items-center px-4 font-medium text-sm focus:outline-none transition"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Suspect Description
                        </button>
                    </div>
                
                    <!-- Tab Content -->
                    <div class="mt-4">
                        <!-- Panel 1: Image Tab -->
                        <div x-show="activeTab === 'composite-image'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-100">
                                <div class="bg-gray-100 rounded-lg overflow-hidden border h-64 md:h-96 w-full flex items-center justify-center transition duration-300 group relative">
                                    @if($composite->final_image_path)
                                        <img src="{{ Storage::url($composite->final_image_path) }}" alt="{{ $composite->title }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                                    @else
                                        <div class="flex flex-col items-center justify-center p-6 text-center">
                                            <x-icon name="photo" class="h-14 w-14 text-gray-400 mx-auto mb-2" />
                                            <p class="text-gray-500 text-sm">No image available</p>
                                        </div>
                                    @endif
                                </div>
                                
                                @if(!$isEditing)
                                    <div class="mt-4 flex justify-center">
                                        <x-button 
                                            icon="arrow-down-tray" 
                                            label="Download" 
                                            wire:click="downloadComposite" 
                                            class="bg-[#2C3E50] hover:bg-[#34495E] text-white transition-colors duration-150"
                                        />
                                    </div>
                                @endif
                            </div>
                        </div>
                    
                        <!-- Panel 2: Basic Information Tab -->
                        <div x-show="activeTab === 'basic-info'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-100">
                                @if(!$isEditing)
                                    <div class="space-y-4">
                                        <div class="bg-gray-50 p-3 rounded-lg border">
                                            <p class="text-xs font-medium text-gray-500 mb-1">Title</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $composite->title }}</p>
                                        </div>

                                        <div class="bg-gray-50 p-3 rounded-lg border">
                                            <p class="text-xs font-medium text-gray-500 mb-1">Witness</p>
                                            <p class="text-sm text-gray-800 flex items-center">
                                                <x-icon name="user" class="h-4 w-4 mr-1 text-[#2C3E50]/70" />
                                                {{ $composite->witness ? $composite->witness->name : 'N/A' }}
                                            </p>
                                        </div>

                                        <div class="bg-gray-50 p-3 rounded-lg border">
                                            <p class="text-xs font-medium text-gray-500 mb-1">Case</p>
                                            <p class="text-sm text-gray-800 flex items-center">
                                                <x-icon name="folder" class="h-4 w-4 mr-1 text-[#2C3E50]/70" />
                                                {{ $composite->caseRecord->title }}
                                            </p>
                                        </div>

                                        <div class="bg-gray-50 p-3 rounded-lg border">
                                            <p class="text-xs font-medium text-gray-500 mb-1">Created</p>
                                            <p class="text-sm text-gray-800 flex items-center">
                                                <x-icon name="calendar" class="h-4 w-4 mr-1 text-[#2C3E50]/70" />
                                                {{ $composite->created_at->format('M d, Y') }}
                                            </p>
                                        </div>
                                        
                                        <div class="bg-gray-50 p-3 rounded-lg border">
                                            <p class="text-xs font-medium text-gray-500 mb-2 flex items-center">
                                                <x-icon name="document-text" class="h-4 w-4 mr-1 text-[#2C3E50]/70" />
                                                Description
                                            </p>
                                            <p class="text-sm text-gray-800 leading-relaxed">
                                                {{ $composite->description ?: 'No description provided' }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <!-- Edit Form - Basic Info -->
                                    <div class="space-y-4">
                                        <x-input 
                                            label="Title" 
                                            wire:model="title" 
                                            placeholder="Enter composite title"
                                            icon="pencil"
                                            class="w-full"
                                        />
                                        
                                        <x-select 
                                            label="Witness" 
                                            wire:model="witness_id"
                                            placeholder="Select a witness"
                                            icon="user"
                                            class="w-full"
                                        >
                                            @foreach($available_witnesses as $witness)
                                                <x-select.option label="{{ $witness->name }}" value="{{ $witness->id }}" />
                                            @endforeach
                                        </x-select>
                                        
                                        <x-textarea 
                                            label="Description" 
                                            wire:model="description" 
                                            placeholder="Provide a general description of the composite"
                                            rows="5"
                                            class="mt-2" 
                                        />
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Panel 3: Suspect Description Tab -->
                        <div x-show="activeTab === 'suspect-desc'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="bg-white p-3 rounded-md shadow-sm border border-gray-100">
                                @if(!$isEditing)
                                    <div class="space-y-4">
                                        <div class="bg-gray-50 p-3 rounded-lg border">
                                            <p class="text-xs font-medium text-gray-500 mb-1">Gender</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $composite->suspect_gender ?: 'Not specified' }}</p>
                                        </div>
                                        
                                        <div class="bg-gray-50 p-3 rounded-lg border">
                                            <p class="text-xs font-medium text-gray-500 mb-1">Ethnicity</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $composite->suspect_ethnicity ?: 'Not specified' }}</p>
                                        </div>
                                        
                                        <div class="bg-gray-50 p-3 rounded-lg border">
                                            <p class="text-xs font-medium text-gray-500 mb-1">Age Range</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $composite->suspect_age_range ?: 'Not specified' }}</p>
                                        </div>
                                        
                                        <div class="bg-gray-50 p-3 rounded-lg border">
                                            <p class="text-xs font-medium text-gray-500 mb-1">Height</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $composite->suspect_height ?: 'Not specified' }}</p>
                                        </div>
                                        
                                        <div class="bg-gray-50 p-3 rounded-lg border">
                                            <p class="text-xs font-medium text-gray-500 mb-1">Body Build</p>
                                            <p class="text-sm text-gray-800 font-medium">{{ $composite->suspect_body_build ?: 'Not specified' }}</p>
                                        </div>
                                        
                                        <div class="bg-gray-50 p-3 rounded-lg border">
                                            <p class="text-xs font-medium text-gray-500 mb-2 flex items-center">
                                                <x-icon name="clipboard-document-list" class="h-4 w-4 mr-1 text-[#2C3E50]/70" />
                                                Additional Notes
                                            </p>
                                            <p class="text-sm text-gray-800 leading-relaxed">
                                                {{ $composite->suspect_additional_notes ?: 'No additional notes' }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <!-- Edit Form - Suspect Info -->
                                    <div class="space-y-4">
                                        <x-input label="Gender" wire:model="suspect_gender" placeholder="e.g. Male, Female, Other" icon="user" />
                                        <x-select
                                            label="Ethnicity"
                                            wire:model="suspect_ethnicity"
                                            placeholder="Select ethnicity"
                                            icon="globe-alt"
                                            :options="[
                                                ['name' => 'Filipino', 'value' => 'Filipino'],
                                                ['name' => 'Ilocano', 'value' => 'Ilocano'],
                                                ['name' => 'Cebuano', 'value' => 'Cebuano'],
                                                ['name' => 'Tagalog', 'value' => 'Tagalog'],
                                                ['name' => 'Bicolano', 'value' => 'Bicolano'],
                                                ['name' => 'Waray', 'value' => 'Waray'],
                                                ['name' => 'Kapampangan', 'value' => 'Kapampangan'],
                                                ['name' => 'Pangasinense', 'value' => 'Pangasinense'],
                                                ['name' => 'Chinese-Filipino', 'value' => 'Chinese-Filipino'],
                                                ['name' => 'Spanish-Filipino', 'value' => 'Spanish-Filipino'],
                                                ['name' => 'American-Filipino', 'value' => 'American-Filipino'],
                                                ['name' => 'Japanese-Filipino', 'value' => 'Japanese-Filipino'],
                                                ['name' => 'Korean-Filipino', 'value' => 'Korean-Filipino'],
                                                ['name' => 'Indian-Filipino', 'value' => 'Indian-Filipino'],
                                                ['name' => 'Middle Eastern-Filipino', 'value' => 'Middle Eastern-Filipino'],
                                                ['name' => 'Foreign', 'value' => 'Foreign'],
                                                ['name' => 'Other', 'value' => 'Other'],
                                            ]"
                                            option-label="name"
                                            option-value="value"
                                        />
                                        <x-input label="Age Range" wire:model="suspect_age_range" placeholder="e.g. 25-35" icon="calendar" />
                                        <x-input label="Height" wire:model="suspect_height" placeholder="e.g. 5'9\" to 6'" icon="arrow-up-down" />
                                        <x-input label="Body Build" wire:model="suspect_body_build" placeholder="e.g. Slim, Athletic, Muscular" icon="identification" />
                                        
                                        <x-textarea 
                                            label="Additional Notes" 
                                            wire:model="suspect_additional_notes" 
                                            placeholder="Any additional details about the suspect"
                                            rows="4" 
                                            class="mt-2"
                                        />
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="flex justify-end gap-x-3">
                        @if($isEditing)
                            <x-button 
                                flat 
                                label="Cancel" 
                                wire:click="close" 
                                icon="x-mark"
                                class="text-gray-600 hover:text-gray-800 transition-colors duration-150" 
                            />
                            <x-button 
                                secondary 
                                label="Reset" 
                                wire:click="resetForm" 
                                icon="arrow-path"
                                class="border-gray-300 text-gray-700 hover:bg-gray-100 transition-colors duration-150" 
                            />
                            <x-button 
                                primary 
                                label="Save Changes" 
                                wire:click="saveChanges" 
                                icon="check-circle"
                                class="bg-[#2C3E50] hover:bg-[#34495E] transition-colors duration-150" 
                            />
                        @else
                            <x-button 
                                flat 
                                label="Close" 
                                wire:click="close" 
                                icon="x-mark"
                                class="text-gray-600 hover:text-gray-800 transition-colors duration-150" 
                            />
                            <x-button 
                                primary 
                                label="Edit" 
                                wire:click="toggleEditMode" 
                                icon="pencil"
                                class="bg-[#2C3E50] hover:bg-[#34495E] transition-colors duration-150" 
                            />
                        @endif
                    </div>
                </x-slot>
            @else
                <div class="p-8 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="bg-gray-100 rounded-full p-4 mb-4">
                            <x-icon name="exclamation-circle" class="h-8 w-8 text-gray-400" />
                        </div>
                        <p class="text-gray-500 mb-4">Composite not found or no data available.</p>
                    </div>
                </div>
                
                <x-slot name="footer">
                    <div class="flex justify-end">
                        <x-button flat label="Close" wire:click="close" icon="x-mark" class="text-gray-600 hover:text-gray-800 transition-colors duration-150" />
                    </div>
                </x-slot>
            @endif
        </x-card>
    </x-modal>
</div>
