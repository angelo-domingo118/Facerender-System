<div>
    <!-- Global Delete Modal -->
    <x-modal 
        wire:model="showModal" 
        max-width="md"
        align="center"
        blur="md"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
    >
        <x-card :title="$title">
            <div class="flex flex-col items-center justify-center text-center">
                <div class="rounded-full bg-red-100 p-3 mb-4">
                    <x-icon name="exclamation-triangle" class="w-8 h-8 text-red-500" solid />
                </div>
                <p class="text-gray-600 mb-6">
                    {{ $message }}
                </p>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button 
                        wire:click="cancel" 
                        flat
                        label="Cancel"
                    />
                    <x-button 
                        wire:click="confirm" 
                        red
                        spinner="confirm" 
                        label="{{ $confirmText }}"
                    />
                </div>
            </x-slot>
        </x-card>
    </x-modal>
</div>
