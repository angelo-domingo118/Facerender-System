<div>
    <x-modal 
        wire:model.live="show" 
        max-width="4xl" 
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
                <div x-data="{ activeTab: 'basic-info' }" class="mb-5">
                    <div class="flex border-b border-gray-200 bg-white rounded-t-lg overflow-hidden">
                        <button 
                            @click="activeTab = 'basic-info'" 
                            :class="{'text-[#2C3E50] border-[#2C3E50] border-b-2 -mb-px py-3 bg-gray-50': activeTab === 'basic-info', 'text-gray-500 hover:text-gray-700 py-3 hover:bg-gray-50': activeTab !== 'basic-info'}"
                            class="flex items-center px-6 font-medium text-sm focus:outline-none transition flex-1 justify-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Basic Information
                        </button>
                        
                        <button 
                            @click="activeTab = 'suspect-desc'" 
                            :class="{'text-[#2C3E50] border-[#2C3E50] border-b-2 -mb-px py-3 bg-gray-50': activeTab === 'suspect-desc', 'text-gray-500 hover:text-gray-700 py-3 hover:bg-gray-50': activeTab !== 'suspect-desc'}"
                            class="flex items-center px-6 font-medium text-sm focus:outline-none transition flex-1 justify-center"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Suspect Description
                        </button>
                    </div>
                
                    <!-- Tab Content -->
                    <div class="mt-4">
                        <!-- Panel: Basic Information Tab -->
                        <div x-show="activeTab === 'basic-info'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                                @if(!$isEditing)
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:border-[#2C3E50]/30 transition-all duration-200">
                                                <div class="flex items-center mb-2">
                                                    <div class="bg-[#2C3E50]/10 p-2 rounded-full">
                                                        <x-icon name="document-text" class="h-4 w-4 text-[#2C3E50]" />
                                                    </div>
                                                    <p class="text-xs font-semibold text-gray-500 ml-2 uppercase tracking-wider">Title</p>
                                                </div>
                                                <p class="text-sm font-medium text-gray-800">{{ $composite->title }}</p>
                                            </div>

                                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:border-[#2C3E50]/30 transition-all duration-200">
                                                <div class="flex items-center mb-2">
                                                    <div class="bg-[#2C3E50]/10 p-2 rounded-full">
                                                        <x-icon name="user" class="h-4 w-4 text-[#2C3E50]" />
                                                    </div>
                                                    <p class="text-xs font-semibold text-gray-500 ml-2 uppercase tracking-wider">Witness</p>
                                                </div>
                                                <p class="text-sm font-medium text-gray-800">{{ $composite->witness ? $composite->witness->name : 'N/A' }}</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:border-[#2C3E50]/30 transition-all duration-200">
                                                <div class="flex items-center mb-2">
                                                    <div class="bg-[#2C3E50]/10 p-2 rounded-full">
                                                        <x-icon name="folder" class="h-4 w-4 text-[#2C3E50]" />
                                                    </div>
                                                    <p class="text-xs font-semibold text-gray-500 ml-2 uppercase tracking-wider">Case</p>
                                                </div>
                                                <p class="text-sm font-medium text-gray-800">{{ $composite->caseRecord->title }}</p>
                                            </div>

                                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:border-[#2C3E50]/30 transition-all duration-200">
                                                <div class="flex items-center mb-2">
                                                    <div class="bg-[#2C3E50]/10 p-2 rounded-full">
                                                        <x-icon name="calendar" class="h-4 w-4 text-[#2C3E50]" />
                                                    </div>
                                                    <p class="text-xs font-semibold text-gray-500 ml-2 uppercase tracking-wider">Created</p>
                                                </div>
                                                <p class="text-sm font-medium text-gray-800">{{ $composite->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:border-[#2C3E50]/30 transition-all duration-200">
                                            <div class="flex items-center mb-2">
                                                <div class="bg-[#2C3E50]/10 p-2 rounded-full">
                                                    <x-icon name="document-text" class="h-4 w-4 text-[#2C3E50]" />
                                                </div>
                                                <p class="text-xs font-semibold text-gray-500 ml-2 uppercase tracking-wider">Description</p>
                                            </div>
                                            <p class="text-sm text-gray-700 leading-relaxed">
                                                {{ $composite->description ?: 'No description provided' }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <!-- Edit Form - Basic Info -->
                                    <div class="space-y-4">
                                        <x-input 
                                            label="Title *" 
                                            wire:model="title" 
                                            placeholder="Enter composite title"
                                            icon="pencil"
                                            class="w-full"
                                            required
                                            error="{{ $errors->first('title') }}"
                                        />
                                        
                                        <x-select 
                                            label="Witness *" 
                                            wire:model="witness_id"
                                            placeholder="Select a witness"
                                            icon="user"
                                            class="w-full"
                                            required
                                            error="{{ $errors->first('witness_id') }}"
                                        >
                                            @foreach($available_witnesses as $witness)
                                                <x-select.option label="{{ $witness->name }}" value="{{ $witness->id }}" />
                                            @endforeach
                                        </x-select>
                                        
                                        <x-datetime-picker
                                            wire:model="created_at"
                                            label="Creation Date *"
                                            placeholder="Creation date"
                                            without-time
                                            readonly
                                            hint="Automatically set when the composite was created"
                                            error="{{ $errors->first('created_at') }}"
                                        />
                                        
                                        <x-textarea 
                                            label="Description" 
                                            wire:model="description" 
                                            placeholder="Provide a general description of the composite"
                                            rows="5"
                                            class="mt-2" 
                                            error="{{ $errors->first('description') }}"
                                        />
                                        
                                        <div class="mt-2 text-sm text-gray-500">
                                            <span class="font-medium">Note:</span> Fields marked with * are required.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Panel: Suspect Description Tab -->
                        <div x-show="activeTab === 'suspect-desc'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                                @if(!$isEditing)
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:border-[#2C3E50]/30 transition-all duration-200">
                                                <div class="flex items-center mb-2">
                                                    <div class="bg-[#2C3E50]/10 p-2 rounded-full">
                                                        <x-icon name="user" class="h-4 w-4 text-[#2C3E50]" />
                                                    </div>
                                                    <p class="text-xs font-semibold text-gray-500 ml-2 uppercase tracking-wider">Gender</p>
                                                </div>
                                                <p class="text-sm font-medium text-gray-800">{{ $composite->suspect_gender ?: 'Not specified' }}</p>
                                            </div>
                                            
                                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:border-[#2C3E50]/30 transition-all duration-200">
                                                <div class="flex items-center mb-2">
                                                    <div class="bg-[#2C3E50]/10 p-2 rounded-full">
                                                        <x-icon name="user-group" class="h-4 w-4 text-[#2C3E50]" />
                                                    </div>
                                                    <p class="text-xs font-semibold text-gray-500 ml-2 uppercase tracking-wider">Ethnicity</p>
                                                </div>
                                                <p class="text-sm font-medium text-gray-800">{{ $composite->suspect_ethnicity ?: 'Not specified' }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:border-[#2C3E50]/30 transition-all duration-200">
                                                <div class="flex items-center mb-2">
                                                    <div class="bg-[#2C3E50]/10 p-2 rounded-full">
                                                        <x-icon name="calendar" class="h-4 w-4 text-[#2C3E50]" />
                                                    </div>
                                                    <p class="text-xs font-semibold text-gray-500 ml-2 uppercase tracking-wider">Age Range</p>
                                                </div>
                                                <p class="text-sm font-medium text-gray-800">{{ $composite->suspect_age_range ?: 'Not specified' }}</p>
                                            </div>
                                            
                                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:border-[#2C3E50]/30 transition-all duration-200">
                                                <div class="flex items-center mb-2">
                                                    <div class="bg-[#2C3E50]/10 p-2 rounded-full">
                                                        <x-icon name="identification" class="h-4 w-4 text-[#2C3E50]" />
                                                    </div>
                                                    <p class="text-xs font-semibold text-gray-500 ml-2 uppercase tracking-wider">Height</p>
                                                </div>
                                                <p class="text-sm font-medium text-gray-800">{{ $composite->suspect_height ?: 'Not specified' }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-4">
                                            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:border-[#2C3E50]/30 transition-all duration-200">
                                                <div class="flex items-center mb-2">
                                                    <div class="bg-[#2C3E50]/10 p-2 rounded-full">
                                                        <x-icon name="user" class="h-4 w-4 text-[#2C3E50]" />
                                                    </div>
                                                    <p class="text-xs font-semibold text-gray-500 ml-2 uppercase tracking-wider">Body Build</p>
                                                </div>
                                                <p class="text-sm font-medium text-gray-800">{{ $composite->suspect_body_build ?: 'Not specified' }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm hover:border-[#2C3E50]/30 transition-all duration-200">
                                            <div class="flex items-center mb-2">
                                                <div class="bg-[#2C3E50]/10 p-2 rounded-full">
                                                    <x-icon name="document-text" class="h-4 w-4 text-[#2C3E50]" />
                                                </div>
                                                <p class="text-xs font-semibold text-gray-500 ml-2 uppercase tracking-wider">Additional Notes</p>
                                            </div>
                                            <p class="text-sm text-gray-700 leading-relaxed">
                                                {{ $composite->suspect_additional_notes ?: 'No additional notes' }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <!-- Edit Form - Suspect Info -->
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <x-select
                                                label="Gender"
                                                wire:model="suspect_gender"
                                                placeholder="Select gender"
                                                :options="[
                                                    ['name' => 'Male', 'value' => 'Male'],
                                                    ['name' => 'Female', 'value' => 'Female'],
                                                    ['name' => 'Other', 'value' => 'Other'],
                                                ]"
                                                option-label="name"
                                                option-value="value"
                                                error="{{ $errors->first('suspect_gender') }}"
                                            />
                                            
                                            <x-select
                                                label="Ethnicity"
                                                wire:model="suspect_ethnicity"
                                                placeholder="Select ethnicity"
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
                                                error="{{ $errors->first('suspect_ethnicity') }}"
                                            />
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <x-select
                                                label="Age Range"
                                                wire:model="suspect_age_range"
                                                placeholder="Select age range"
                                                :options="[
                                                    ['name' => '18-25', 'value' => '18-25'],
                                                    ['name' => '26-35', 'value' => '26-35'],
                                                    ['name' => '36-45', 'value' => '36-45'],
                                                    ['name' => '46-55', 'value' => '46-55'],
                                                    ['name' => '56-65', 'value' => '56-65'],
                                                    ['name' => '65+', 'value' => '65+'],
                                                ]"
                                                option-label="name"
                                                option-value="value"
                                                error="{{ $errors->first('suspect_age_range') }}"
                                            />
                                            
                                            <x-select
                                                label="Height"
                                                wire:model="suspect_height"
                                                placeholder="Select height"
                                                :options="[
                                                    ['name' => 'Under 5 feet', 'value' => 'Under 5 feet'],
                                                    ['name' => '5\' to 5\'4\"', 'value' => '5\' to 5\'4\"'],
                                                    ['name' => '5\'5\" to 5\'8\"', 'value' => '5\'5\" to 5\'8\"'],
                                                    ['name' => '5\'9\" to 6\'', 'value' => '5\'9\" to 6\''],
                                                    ['name' => '6\'1\" to 6\'4\"', 'value' => '6\'1\" to 6\'4\"'],
                                                    ['name' => 'Over 6\'4\"', 'value' => 'Over 6\'4\"'],
                                                ]"
                                                option-label="name"
                                                option-value="value"
                                                error="{{ $errors->first('suspect_height') }}"
                                            />
                                        </div>
                                        
                                        <x-select
                                            label="Body Build"
                                            wire:model="suspect_body_build"
                                            placeholder="Select body build"
                                            :options="[
                                                ['name' => 'Slim', 'value' => 'Slim'],
                                                ['name' => 'Average', 'value' => 'Average'],
                                                ['name' => 'Athletic', 'value' => 'Athletic'],
                                                ['name' => 'Stocky', 'value' => 'Stocky'],
                                                ['name' => 'Overweight', 'value' => 'Overweight'],
                                                ['name' => 'Muscular', 'value' => 'Muscular'],
                                            ]"
                                            option-label="name"
                                            option-value="value"
                                            error="{{ $errors->first('suspect_body_build') }}"
                                        />
                                        
                                        <x-textarea
                                            label="Additional Notes"
                                            wire:model="suspect_additional_notes"
                                            placeholder="Add any other details about the suspect's appearance"
                                            rows="4"
                                            error="{{ $errors->first('suspect_additional_notes') }}"
                                        />
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <x-slot name="footer">
                    <div class="flex justify-between items-center">
                        @if(!$isEditing)
                            <div>
                                <x-button 
                                    icon="pencil-square" 
                                    wire:click="toggleEditMode" 
                                    class="bg-[#6366F1] hover:bg-[#4F46E5] text-white mr-2"
                                >
                                    Edit
                                </x-button>
                                <x-button
                                    href="{{ route('composite.editor', $compositeId) }}"
                                    icon="pencil-square"
                                    class="bg-[#2C3E50] hover:bg-[#34495E] text-white"
                                >
                                    Edit in Designer
                                </x-button>
                            </div>
                            <div>
                                <x-button 
                                    icon="x-mark" 
                                    wire:click="$set('show', false)"
                                    flat
                                >
                                    Close
                                </x-button>
                            </div>
                        @else
                            <div>
                                <x-button 
                                    icon="arrow-path" 
                                    wire:click="resetForm" 
                                    flat
                                    class="mr-2"
                                >
                                    Reset
                                </x-button>
                                <x-button 
                                    icon="x-mark" 
                                    wire:click="toggleEditMode"
                                    flat
                                >
                                    Cancel
                                </x-button>
                            </div>
                            <div>
                                <x-button 
                                    icon="check" 
                                    wire:click="saveChanges"
                                    spinner="saveChanges" 
                                    class="bg-[#2C3E50] hover:bg-[#34495E] text-white"
                                >
                                    Save Changes
                                </x-button>
                            </div>
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
