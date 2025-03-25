<div>
    <x-modal-card wire:model.defer="show" max-width="lg" blur>
        <x-slot name="title">
            <div class="flex items-center">
                <x-icon name="user-plus" class="h-5 w-5 mr-2 text-[#3498DB]" />
                <span>Add Witness</span>
            </div>
        </x-slot>
        
        <div class="space-y-4">
            <x-input
                wire:model="name"
                label="Witness Name"
                placeholder="Enter full name"
                required
            />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    wire:model="age"
                    label="Age"
                    placeholder="Enter age"
                    type="number"
                />
                
                <x-select
                    wire:model="gender"
                    label="Gender"
                    placeholder="Select gender"
                    :options="[
                        ['name' => 'Male', 'value' => 'Male'],
                        ['name' => 'Female', 'value' => 'Female'],
                        ['name' => 'Other', 'value' => 'Other'],
                    ]"
                    option-label="name"
                    option-value="value"
                />
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    wire:model="contact_number"
                    label="Contact Number"
                    placeholder="Enter contact number"
                />
                
                <x-input
                    wire:model="email"
                    label="Email"
                    placeholder="Enter email address"
                    type="email"
                />
            </div>
            
            <x-input
                wire:model="address"
                label="Address"
                placeholder="Enter address"
            />
            
            <x-input
                wire:model="relationship_to_case"
                label="Relationship to Case"
                placeholder="E.g., Victim, Bystander, Reporting Officer"
            />
            
            <x-textarea
                wire:model="notes"
                label="Additional Notes"
                placeholder="Add any additional information about this witness"
                rows="3"
            />
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
                    label="Add Witness"
                    wire:click="save"
                    spinner="save"
                    class="bg-[#2C3E50] hover:bg-[#34495E]"
                />
            </div>
        </x-slot>
    </x-modal-card>
</div>
