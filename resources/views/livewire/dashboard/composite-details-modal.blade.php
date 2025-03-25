<div>
    <x-modal 
        wire:model.live="show" 
        max-width="6xl" 
        blur="md"
        align="center"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
    >
        <x-card :title="$isEditing ? 'Edit Composite' : 'Composite Details'" class="overflow-hidden">
            @if($composite)
                <div wire:loading.delay class="absolute inset-0 bg-white/80 z-50 flex items-center justify-center">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#2C3E50]"></div>
                        <span class="mt-2 text-sm text-gray-600">Loading...</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Panel 1: Image -->
                    <div class="md:col-span-1">
                        <div class="bg-white rounded-lg border shadow-sm p-4">
                            <h3 class="text-lg font-medium text-[#2C3E50] mb-3 border-b pb-2">Composite Image</h3>
                            
                            <div class="bg-gray-100 rounded-lg overflow-hidden border h-64 md:h-80 w-full flex items-center justify-center transition duration-300 group relative">
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
                                        class="bg-[#2C3E50] hover:bg-[#34495E] text-white"
                                    />
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Panel 2: Basic Information -->
                    <div class="md:col-span-1">
                        <div class="bg-white rounded-lg border shadow-sm p-4 h-full">
                            <h3 class="text-lg font-medium text-[#2C3E50] mb-3 border-b pb-2">Basic Information</h3>
                            
                            @if(!$isEditing)
                                <div class="space-y-4">
                                    <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                        <p class="text-sm font-medium text-gray-500">Title</p>
                                        <p class="text-gray-800 font-medium">{{ $composite->title }}</p>
                                    </div>

                                    <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                        <p class="text-sm font-medium text-gray-500">Witness</p>
                                        <p class="text-gray-800 flex items-center">
                                            <x-icon name="user" class="h-4 w-4 mr-1 text-[#2C3E50]/70" />
                                            {{ $composite->witness ? $composite->witness->name : 'N/A' }}
                                        </p>
                                    </div>

                                    <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                        <p class="text-sm font-medium text-gray-500">Case</p>
                                        <p class="text-gray-800 flex items-center">
                                            <x-icon name="folder" class="h-4 w-4 mr-1 text-[#2C3E50]/70" />
                                            {{ $composite->caseRecord->title }}
                                        </p>
                                    </div>

                                    <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                        <p class="text-sm font-medium text-gray-500">Created</p>
                                        <p class="text-gray-800 flex items-center">
                                            <x-icon name="calendar" class="h-4 w-4 mr-1 text-[#2C3E50]/70" />
                                            {{ $composite->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <p class="text-sm font-medium text-gray-500 mb-2 flex items-center">
                                            <x-icon name="document-text" class="h-4 w-4 mr-1 text-[#2C3E50]/70" />
                                            Description
                                        </p>
                                        <div class="mt-1 p-4 bg-gray-50 rounded-md border border-gray-100 text-gray-800 leading-relaxed">
                                            {{ $composite->description ?: 'No description provided' }}
                                        </div>
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

                    <!-- Panel 3: Suspect Information -->
                    <div class="md:col-span-1">
                        <div class="bg-white rounded-lg border shadow-sm p-4 h-full">
                            <h3 class="text-lg font-medium text-[#2C3E50] mb-3 border-b pb-2">Suspect Description</h3>
                            
                            @if(!$isEditing)
                                <div class="space-y-4">
                                    <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                        <p class="text-sm font-medium text-gray-500">Gender</p>
                                        <p class="text-gray-800 font-medium">{{ $composite->suspect_gender ?: 'Not specified' }}</p>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                        <p class="text-sm font-medium text-gray-500">Ethnicity</p>
                                        <p class="text-gray-800 font-medium">{{ $composite->suspect_ethnicity ?: 'Not specified' }}</p>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                        <p class="text-sm font-medium text-gray-500">Age Range</p>
                                        <p class="text-gray-800 font-medium">{{ $composite->suspect_age_range ?: 'Not specified' }}</p>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                        <p class="text-sm font-medium text-gray-500">Height</p>
                                        <p class="text-gray-800 font-medium">{{ $composite->suspect_height ?: 'Not specified' }}</p>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                        <p class="text-sm font-medium text-gray-500">Body Build</p>
                                        <p class="text-gray-800 font-medium">{{ $composite->suspect_body_build ?: 'Not specified' }}</p>
                                    </div>
                                    
                                    <div class="mt-4">
                                        <p class="text-sm font-medium text-gray-500 mb-2 flex items-center">
                                            <x-icon name="clipboard-document-list" class="h-4 w-4 mr-1 text-[#2C3E50]/70" />
                                            Additional Notes
                                        </p>
                                        <div class="mt-1 p-4 bg-gray-50 rounded-md border border-gray-100 text-gray-800 leading-relaxed">
                                            {{ $composite->suspect_additional_notes ?: 'No additional notes' }}
                                        </div>
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

                <x-slot name="footer">
                    <div class="flex justify-end gap-x-3">
                        @if($isEditing)
                            <x-button 
                                flat 
                                label="Cancel" 
                                wire:click="toggleEditMode" 
                                icon="x-mark"
                                class="text-gray-600 hover:text-gray-800" 
                            />
                            <x-button 
                                primary 
                                label="Save Changes" 
                                wire:click="saveChanges" 
                                icon="check"
                                wire:loading.attr="disabled"
                                class="bg-[#2C3E50] hover:bg-[#34495E]" 
                            />
                        @else
                            <x-button 
                                flat 
                                label="Close" 
                                wire:click="close" 
                                icon="x-mark"
                                class="text-gray-600 hover:text-gray-800" 
                            />
                            <x-button 
                                primary 
                                label="Edit" 
                                wire:click="toggleEditMode" 
                                icon="pencil"
                                class="bg-[#2C3E50] hover:bg-[#34495E]" 
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
                        <x-button flat label="Close" wire:click="close" icon="x-mark" />
                    </div>
                </x-slot>
            @endif
        </x-card>
    </x-modal>
</div>
