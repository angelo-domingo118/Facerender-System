<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}
    <x-modal-card wire:model.defer="show" max-width="lg" blur>
        <x-slot name="title">
            <div class="flex items-center">
                <x-icon name="document-plus" class="h-5 w-5 mr-2 text-[#3498DB]" />
                <span>Create New Case</span>
            </div>
        </x-slot>

        <div class="space-y-4">
            <x-input
                wire:model="title"
                label="Case Title"
                placeholder="Enter a title for this case"
                required
            />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    wire:model="incident_type"
                    label="Incident Type"
                    placeholder="Type of incident"
                    required
                />

                <x-datetime-picker
                    wire:model="incident_date"
                    label="Incident Date"
                    placeholder="Select date"
                    required
                />
            </div>

            <x-input
                wire:model="location"
                label="Location"
                placeholder="Enter incident location"
            />

            <x-textarea
                wire:model="description"
                label="Description (Optional)"
                placeholder="Add any details about this case"
                rows="3"
            />

            <x-select
                wire:model="status"
                label="Status"
                placeholder="Select status"
                :options="[
                    ['name' => 'Open', 'value' => 'open'],
                    ['name' => 'Pending', 'value' => 'pending'],
                    ['name' => 'Closed', 'value' => 'closed'],
                    ['name' => 'Archived', 'value' => 'archived']
                ]"
                option-label="name"
                option-value="value"
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
                    label="Create Case"
                    wire:click="save"
                    spinner="save"
                    class="bg-[#10B981] hover:bg-[#059669] text-white shadow-sm transition-colors rounded-md"
                />
            </div>
        </x-slot>
    </x-modal-card>
</div>
