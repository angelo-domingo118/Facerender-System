<div>
    <form wire:submit="updateProfileInformation">
        <div class="space-y-6">
            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                <!-- Profile Photo -->
                <div x-data="{photoName: null, photoPreview: null}" class="space-y-2">
                    <x-label for="photo" value="{{ __('Photo') }}" class="text-[#34495E] font-medium" />
                    
                    <!-- Current Profile Photo -->
                    <div class="mt-2 flex items-center">
                        <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="h-16 w-16 rounded-full object-cover border-2 border-[#95A5A6]">
                        
                        <div class="ml-4 flex">
                            <x-secondary-button type="button" class="ml-2 bg-[#F5F7FA] text-[#34495E] border-[#95A5A6] transform transition hover:scale-[1.05] duration-300" x-on:click.prevent="$refs.photo.click()">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ __('Select New Photo') }}
                                </div>
                            </x-secondary-button>

                            @if ($this->user->profile_photo_path)
                                <x-secondary-button type="button" class="ml-2 bg-red-50 text-red-500 border-red-300 hover:bg-red-100 transform transition hover:scale-[1.05] duration-300" wire:click="deleteProfilePhoto">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        {{ __('Remove Photo') }}
                                    </div>
                                </x-secondary-button>
                            @endif
                        </div>
                    </div>
                    
                    <!-- New Profile Photo Preview -->
                    <div class="mt-2" x-show="photoPreview" style="display: none;">
                        <div class="relative">
                            <span class="block rounded-full h-20 w-20 bg-cover bg-no-repeat bg-center border-2 border-[#95A5A6]"
                                x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                            </span>
                        </div>
                    </div>
                    
                    <input type="file" id="photo" class="hidden"
                                wire:model.live="photo"
                                x-ref="photo"
                                x-on:change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            photoPreview = e.target.result;
                                        };
                                        reader.readAsDataURL($refs.photo.files[0]);
                                " />
                                
                    <x-input-error for="photo" class="mt-1 text-sm" />
                </div>
            @endif

            <!-- Name -->
            <div>
                <x-label for="name" value="{{ __('Name') }}" class="text-[#34495E] font-medium" />
                <div class="relative mt-1 rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <x-input id="name" 
                            type="text" 
                            class="pl-10 w-full border border-[#95A5A6] focus:border-[#34495E] focus:ring focus:ring-[#34495E] focus:ring-opacity-20 rounded-md shadow-sm transition duration-300 transform hover:scale-[1.01]" 
                            wire:model.live="state.name" 
                            required 
                            autocomplete="name" />
                </div>
                <x-input-error for="name" class="mt-1 text-sm" />
            </div>

            <!-- Email -->
            <div>
                <x-label for="email" value="{{ __('Email') }}" class="text-[#34495E] font-medium" />
                <div class="relative mt-1 rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <x-input id="email" 
                            type="email" 
                            class="pl-10 w-full border border-[#95A5A6] focus:border-[#34495E] focus:ring focus:ring-[#34495E] focus:ring-opacity-20 rounded-md shadow-sm transition duration-300 transform hover:scale-[1.01]" 
                            wire:model.live="state.email" 
                            required 
                            autocomplete="username" />
                </div>
                <x-input-error for="email" class="mt-1 text-sm" />
                
                @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                    <div class="mt-2 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    {{ __('Your email address is unverified.') }}
                                    <button type="button" class="underline text-sm text-yellow-600 hover:text-yellow-900 font-medium focus:outline-none focus:underline transition duration-150 ease-in-out" wire:click.prevent="sendEmailVerification">
                                        {{ __('Click here to re-send the verification email.') }}
                                    </button>
                                </p>
                                
                                @if ($this->verificationLinkSent)
                                    <p class="mt-2 font-medium text-sm text-green-600">
                                        <span class="inline-flex items-center">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                                <path d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            {{ __('A new verification link has been sent to your email address.') }}
                                        </span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-end">
                <x-action-message class="mr-3 text-green-500" on="saved">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('Saved.') }}
                    </div>
                </x-action-message>

                <x-button type="submit" class="bg-[#34495E] border-0 font-medium transform transition hover:scale-[1.02] duration-300 shadow-md" wire:loading.attr="disabled" wire:target="photo">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        {{ __('Save') }}
                    </div>
                </x-button>
            </div>
        </div>
    </form>
</div>
