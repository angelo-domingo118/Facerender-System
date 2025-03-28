@props([
    'title' => 'Delete Item',
    'confirmText' => 'Delete',
    'cancelText' => 'Cancel',
    'confirmMethod' => 'confirmDelete',
    'cancelMethod' => 'cancelDelete',
    'model' => null,
    'message' => 'Are you sure you want to delete this item? This action cannot be undone.'
])

<div>
    <x-modal wire:model="{{ $model }}" x-teleport="body">
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
                        wire:click="{{ $cancelMethod }}" 
                        flat
                        label="{{ $cancelText }}"
                    />
                    <x-button 
                        wire:click="{{ $confirmMethod }}" 
                        red
                        spinner="{{ $confirmMethod }}" 
                        label="{{ $confirmText }}"
                    />
                </div>
            </x-slot>
        </x-card>
    </x-modal>
</div>