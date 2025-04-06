<div class="h-full flex flex-col bg-white">
    <div class="flex-1 overflow-y-auto">
        <!-- Details Form -->
        <div class="p-3 space-y-5">
            <!-- Basic Info Section -->
            <div class="mb-5">
                <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#2C3E50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Basic Information
                </h4>
                
                <div class="bg-white p-3 rounded-md shadow-sm border border-gray-100">
                    <div class="space-y-3">
                        <div>
                            <x-input label="Title" wire:model.live="title" placeholder="Enter composite title" />
                        </div>
                        
                        <div>
                            <x-textarea label="Description" wire:model.live="description" placeholder="Enter description" rows="3" />
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Witness Information Section -->
            <div class="mb-5">
                <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#2C3E50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Witness Information
                </h4>
                
                <div class="bg-white p-3 rounded-md shadow-sm border border-gray-100">
                    <div class="space-y-3">
                        <div>
                            <x-native-select
                                label="Witness"
                                :options="$witnesses->map(function($witness) {
                                    return ['name' => $witness->name, 'value' => $witness->id];
                                })->toArray()"
                                option-label="name"
                                option-value="value"
                                placeholder="Select witness"
                                wire:model.live="witnessId"
                            />
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Suspect Details Section -->
            <div class="mb-5">
                <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#2C3E50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Suspect Details
                </h4>
                
                <div class="bg-white p-3 rounded-md shadow-sm border border-gray-100">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-native-select
                                    label="Gender"
                                    :options="[
                                        ['name' => 'Male', 'value' => 'Male'],
                                        ['name' => 'Female', 'value' => 'Female'],
                                        ['name' => 'Unknown', 'value' => 'Unknown'],
                                    ]"
                                    option-label="name"
                                    option-value="value"
                                    placeholder="Select gender"
                                    wire:model.live="suspectGender"
                                />
                            </div>
                            <div>
                                <x-input label="Ethnicity" wire:model.live="suspectEthnicity" placeholder="Ethnicity" />
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <x-input label="Age Range" wire:model.live="suspectAgeRange" placeholder="25-35" />
                            </div>
                            <div>
                                <x-input label="Height" wire:model.live="suspectHeight" placeholder="5'10\"" />
                            </div>
                        </div>
                        
                        <div>
                            <x-native-select
                                label="Body Build"
                                :options="[
                                    ['name' => 'Slim', 'value' => 'Slim'],
                                    ['name' => 'Average', 'value' => 'Average'],
                                    ['name' => 'Athletic', 'value' => 'Athletic'],
                                    ['name' => 'Large', 'value' => 'Large'],
                                    ['name' => 'Muscular', 'value' => 'Muscular'],
                                ]"
                                option-label="name"
                                option-value="value"
                                placeholder="Select body build"
                                wire:model.live="suspectBodyBuild"
                            />
                        </div>
                        
                        <div>
                            <x-textarea label="Additional Notes" wire:model.live="suspectAdditionalNotes" placeholder="Enter any additional details about the suspect" rows="3" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="p-3 border-t border-gray-200">
        <div class="flex justify-end space-x-2">
            <x-button flat label="Reset" wire:click="resetForm" />
            <x-button primary label="Save" wire:click="saveComposite" icon="check" class="bg-[#2C3E50] hover:bg-[#34495E]" />
        </div>
    </div>
</div>
