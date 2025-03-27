<div class="h-full flex flex-col bg-white">
    <div class="p-3 border-b border-gray-200">
        <h3 class="font-medium text-[#2C3E50]">Composite Details</h3>
    </div>
    
    <!-- Details Form -->
    <div class="flex-1 overflow-y-auto p-3">
        <div class="space-y-4">
            <!-- Basic Info -->
            <div>
                <h4 class="text-xs font-medium text-gray-500 uppercase mb-3">Basic Information</h4>
                
                <div class="space-y-3">
                    <div>
                        <x-input label="Title" wire:model.live="title" placeholder="Enter composite title" />
                    </div>
                    
                    <div>
                        <x-textarea label="Description" wire:model.live="description" placeholder="Enter description" rows="3" />
                    </div>
                </div>
            </div>
            
            <!-- Witness Information -->
            <div>
                <h4 class="text-xs font-medium text-gray-500 uppercase mb-3">Witness Information</h4>
                
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
            
            <!-- Suspect Details -->
            <div>
                <h4 class="text-xs font-medium text-gray-500 uppercase mb-3">Suspect Details</h4>
                
                <div class="space-y-3">
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
            
            <!-- Canvas Settings -->
            <div>
                <h4 class="text-xs font-medium text-gray-500 uppercase mb-3">Canvas Settings</h4>
                
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <x-input label="Width" type="number" wire:model.live="canvasWidth" value="800" />
                        </div>
                        <div>
                            <x-input label="Height" type="number" wire:model.live="canvasHeight" value="600" />
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
