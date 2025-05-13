<x-action-section>
    <x-slot name="title">
        {{ __('Delete Account') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Permanently delete your account.') }}
    </x-slot>

    <x-slot name="content">
        <div class="max-w-xl text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </div>

        <div class="mt-5">
            <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                {{ __('Delete Account') }}
            </x-danger-button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-modal 
            wire:model.live="confirmingUserDeletion" 
            max-width="md"
            align="center"
        >
            <x-card title="{{ __('Delete Account') }}">
                <div class="p-4">
                    <div class="flex items-center justify-center mb-4 text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    
                    <p class="text-sm text-gray-600 text-center mb-4">
                        {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>

                    <div class="mt-4" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                        <div class="relative">
                            <x-input 
                                type="password" 
                                class="mt-1 block w-full"
                                autocomplete="current-password"
                                placeholder="{{ __('Password') }}"
                                x-ref="password"
                                wire:model="password"
                                id="delete_user_password"
                                wire:keydown.enter="deleteUser" />
                                
                            @error('password')
                                <div class="flex items-center text-red-500 text-sm mt-2 bg-red-50 p-2 rounded-md border border-red-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span>{{ __('Please enter your current account password to verify your identity.') }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <x-slot name="footer">
                    <div class="flex justify-end gap-x-4">
                        <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </x-secondary-button>

                        <x-danger-button wire:click="deleteUser" wire:loading.attr="disabled" wire:loading.class="opacity-75">
                            <span wire:loading.remove wire:target="deleteUser">{{ __('Delete Account') }}</span>
                            <span wire:loading wire:target="deleteUser">{{ __('Deleting...') }}</span>
                        </x-danger-button>
                    </div>
                </x-slot>
            </x-card>
        </x-modal>
    </x-slot>
</x-action-section>
