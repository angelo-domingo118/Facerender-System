<div>
    <x-modal-card wire:model.defer="show" max-width="lg" blur>
        <x-slot name="title">
            <div class="flex items-center">
                <x-icon name="photo" class="h-5 w-5 mr-2 text-[#2C3E50]" />
                <span>Create New Composite</span>
            </div>
        </x-slot>
        
        <div class="space-y-4">
            <x-input
                wire:model="title"
                label="Composite Title *"
                placeholder="Enter a title for this composite"
                required
                id="create-composite-title"
                error="{{ $errors->first('title') }}"
            />
            
            <x-textarea
                wire:model="description"
                label="Description (Optional)"
                placeholder="Add details about this composite"
                rows="2"
                error="{{ $errors->first('description') }}"
            />
            
            <x-datetime-picker
                wire:model="created_at"
                label="Creation Date *"
                placeholder="Creation date"
                without-time
                readonly
                hint="Automatically set to today's date"
                error="{{ $errors->first('created_at') }}"
            />
            
            @if(empty($available_witnesses))
                <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-yellow-700">
                        <x-icon name="exclamation-triangle" class="h-4 w-4 inline mr-1" />
                        This case doesn't have any witnesses yet. You need to add a witness before creating a composite.
                    </p>
                    <x-button 
                        flat 
                        icon="plus" 
                        label="Add Witness First" 
                        wire:click="handleAddWitnessFirstClick" 
                        class="mt-2"
                    />
                </div>
            @else
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Witness *</label>
                    <select 
                        wire:model="witness_id"
                        class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                        required
                    >
                        <option value="">Choose a witness...</option>
                        @foreach($available_witnesses as $witness)
                            <option value="{{ $witness['value'] }}">{{ $witness['label'] }}</option>
                        @endforeach
                    </select>
                    @error('witness_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            @endif
            
            <div class="border-t border-gray-200 pt-4 mt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Suspect Description (Optional)</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-select
                        wire:model="suspect_gender"
                        label="Gender"
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
                        wire:model="suspect_ethnicity"
                        label="Ethnicity"
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
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                    <x-select
                        wire:model="suspect_age_range"
                        label="Age Range"
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
                        wire:model="suspect_height"
                        label="Height"
                        placeholder="Select height range"
                        :options="$heightOptions"
                        option-label="name"
                        option-value="value"
                        error="{{ $errors->first('suspect_height') }}"
                    />
                </div>
                
                <div class="mt-3">
                    <x-select
                        wire:model="suspect_body_build"
                        label="Body Build"
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
                </div>
                
                <div class="mt-3">
                    <x-textarea
                        wire:model="suspect_additional_notes"
                        label="Additional Notes"
                        placeholder="Add any other details about the suspect's appearance"
                        rows="2"
                        error="{{ $errors->first('suspect_additional_notes') }}"
                    />
                </div>
            </div>
            
            <div class="mt-2 text-sm text-gray-500">
                <span class="font-medium">Note:</span> Fields marked with * are required.
            </div>
        </div>
        
        <x-slot name="footer">
            <div class="flex justify-end gap-x-2">
                <x-button
                    flat
                    label="Cancel"
                    wire:click="cancel"
                />
                
                <x-button
                    primary
                    label="Create Composite"
                    wire:click="save"
                    spinner="save"
                    class="bg-[#2C3E50] hover:bg-[#34495E]"
                />
            </div>
        </x-slot>
    </x-modal-card>
</div>
