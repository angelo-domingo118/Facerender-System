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
                id="add-witness-name"
            />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    wire:model="age"
                    label="Age"
                    placeholder="Enter age"
                    type="number"
                    id="add-witness-age"
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
                    id="add-witness-gender"
                />
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    wire:model="contact_number"
                    label="Contact Number"
                    placeholder="Enter contact number"
                    id="add-witness-contact-number"
                />
            </div>
            
            <x-input
                wire:model="address"
                label="Address"
                placeholder="Enter address"
                id="add-witness-address"
            />
            
            <x-input
                wire:model="relationship_to_case"
                label="Relationship to Case"
                placeholder="E.g., Victim, Bystander, Reporting Officer"
                id="add-witness-relationship-to-case"
            />
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-datetime-picker
                    wire:model="interview_date"
                    label="Interview Date"
                    placeholder="Select interview date"
                    without-time
                    id="add-witness-interview-date"
                />
            </div>
            
            <x-textarea
                wire:model="notes"
                label="Interview Notes"
                placeholder="Add any additional information about this witness"
                rows="3"
                id="add-witness-notes"
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
