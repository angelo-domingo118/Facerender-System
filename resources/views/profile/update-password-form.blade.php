<div>
    <form wire:submit="updatePassword">
        <div class="space-y-6">
            <div>
                <x-label for="current_password" value="{{ __('Current Password') }}" class="text-[#34495E] font-medium" />
                <div class="relative mt-1 rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <x-input id="current_password" 
                        type="password" 
                        class="pl-10 w-full border border-[#95A5A6] focus:border-[#34495E] focus:ring focus:ring-[#34495E] focus:ring-opacity-20 rounded-md shadow-sm" 
                        wire:model.live="state.current_password" 
                        autocomplete="current-password"
                        placeholder="Enter your current password" />
                </div>
                <x-input-error for="current_password" class="mt-1 text-sm" />
            </div>

            <div x-data="passwordStrengthValidator()" x-init="init($wire.get('state.password'), $wire.get('state.password_confirmation'))">
                <x-label for="password" value="{{ __('New Password') }}" class="text-[#34495E] font-medium" />
                <div class="relative mt-1 rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <x-input id="password" 
                        type="password" 
                        class="pl-10 w-full border border-[#95A5A6] focus:border-[#34495E] focus:ring focus:ring-[#34495E] focus:ring-opacity-20 rounded-md shadow-sm" 
                        wire:model.live="state.password" 
                        x-model="password"
                        autocomplete="new-password"
                        placeholder="Enter new password" />
                </div>
                
                <!-- Password Strength Meter -->
                <div class="mt-2">
                    <div class="bg-gray-200 h-2 rounded-full overflow-hidden">
                        <div id="password-strength-meter" class="h-2 rounded-full" 
                             :style="`width: ${strengthPercentage}%; transition: width 0.3s;`"
                             :class="strengthColor"></div>
                    </div>
                    <p id="password-strength-text" class="text-xs text-gray-500 mt-1" x-text="strengthText"></p>
                </div>

                <!-- Password Requirements -->
                <div class="mt-2 text-xs text-gray-500 space-y-1">
                    <p class="font-medium">Password must contain:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li id="req-length" :class="requirements.length ? 'text-green-500' : 'text-gray-500'">At least 8 characters</li>
                        <li id="req-uppercase" :class="requirements.uppercase ? 'text-green-500' : 'text-gray-500'">At least one uppercase letter</li>
                        <li id="req-lowercase" :class="requirements.lowercase ? 'text-green-500' : 'text-gray-500'">At least one lowercase letter</li>
                        <li id="req-number" :class="requirements.number ? 'text-green-500' : 'text-gray-500'">At least one number</li>
                        <li id="req-special" :class="requirements.special ? 'text-green-500' : 'text-gray-500'">At least one special character</li>
                    </ul>
                </div>

                <x-input-error for="password" class="mt-1 text-sm" />
            </div>

            <div x-data="{ confirmPassword: '', password: '', passwordsMatch: false, showMatchText: false }" 
                 x-init="password = document.getElementById('password').value; 
                         confirmPassword = $wire.get('state.password_confirmation') || ''; 
                         $watch('confirmPassword', value => { checkMatch(); }); 
                         Livewire.hook('message.processed', (message, component) => { password = document.getElementById('password').value; checkMatch(); });
                         function checkMatch() { 
                            passwordsMatch = password && confirmPassword && password === confirmPassword;
                            showMatchText = confirmPassword.length > 0;
                         }"
                 class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="text-[#34495E] font-medium" />
                <div class="relative mt-1 rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <x-input id="password_confirmation" 
                        type="password" 
                        class="pl-10 w-full border border-[#95A5A6] focus:border-[#34495E] focus:ring focus:ring-[#34495E] focus:ring-opacity-20 rounded-md shadow-sm" 
                        wire:model.live="state.password_confirmation" 
                        x-model="confirmPassword"
                        autocomplete="new-password"
                        placeholder="Confirm your new password" />
                </div>
                <p id="password-match-text" class="text-xs mt-1" 
                   :class="{ 'hidden': !showMatchText, 'text-green-500': passwordsMatch, 'text-red-500': !passwordsMatch && showMatchText }">
                    <template x-if="showMatchText">
                        <span class="inline-flex items-center">
                            <svg x-show="passwordsMatch" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg x-show="!passwordsMatch" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span x-text="passwordsMatch ? 'Passwords match' : 'Passwords do not match'"></span>
                        </span>
                    </template>
                </p>
                <x-input-error for="password_confirmation" class="mt-1 text-sm" />
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

                <x-button type="submit" class="bg-[#34495E] border-0 font-medium transform transition hover:scale-[1.02] duration-300 shadow-sm">
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

    <!-- JavaScript for Password Strength Meter -->
    <script>
        function passwordStrengthValidator() {
            return {
                password: '',
                strengthPercentage: 0,
                strengthColor: 'bg-gray-300',
                strengthText: 'Password strength: Too weak',
                requirements: {
                    length: false,
                    uppercase: false,
                    lowercase: false,
                    number: false,
                    special: false
                },
                init(initialPassword, initialConfirmPassword) {
                    this.password = initialPassword || '';
                    // We need a slight delay to ensure wire:model hydrates initial value if present
                    this.$nextTick(() => {
                         this.password = document.getElementById('password').value || initialPassword || '';
                         this.checkStrength();

                         // Initialize confirmation check (handled by a separate x-data for simplicity now)
                         // This could be combined, but separating concerns might be easier to manage
                    });

                    this.$watch('password', value => {
                        this.checkStrength();
                        // Dispatch event for confirmation watcher
                        this.$dispatch('password-updated', { password: this.password });
                    });
                },
                checkStrength() {
                    const pass = this.password;
                    let strength = 0;
                    let text = '';
                    let color = 'bg-gray-300'; // Default

                    // Reset requirements
                    this.requirements.length = false;
                    this.requirements.uppercase = false;
                    this.requirements.lowercase = false;
                    this.requirements.number = false;
                    this.requirements.special = false;

                    if (!pass) {
                        strength = 0;
                        text = 'Too weak';
                    } else {
                        // Check length
                        if (pass.length >= 8) { strength += 20; this.requirements.length = true; }
                        // Check uppercase
                        if (/[A-Z]/.test(pass)) { strength += 20; this.requirements.uppercase = true; }
                        // Check lowercase
                        if (/[a-z]/.test(pass)) { strength += 20; this.requirements.lowercase = true; }
                        // Check numbers
                        if (/[0-9]/.test(pass)) { strength += 20; this.requirements.number = true; }
                        // Check special characters
                        if (/[^A-Za-z0-9]/.test(pass)) { strength += 20; this.requirements.special = true; }

                        // Determine strength text and color
                        if (strength === 0) { // Should only happen if length < 8 and nothing else matches
                            text = 'Too weak';
                            color = 'bg-red-500'; // Make 'too weak' red if some input exists
                        } else if (strength <= 20) { // Likely only length or one other category
                             text = 'Weak';
                             color = 'bg-red-500';
                         } else if (strength <= 40) {
                            text = 'Fair';
                            color = 'bg-orange-500';
                        } else if (strength <= 60) {
                            text = 'Good';
                            color = 'bg-yellow-500';
                        } else if (strength <= 80) {
                            text = 'Strong';
                            color = 'bg-green-400';
                        } else { // strength = 100
                            text = 'Very strong';
                            color = 'bg-green-600';
                        }
                    }

                    this.strengthPercentage = strength;
                    this.strengthColor = color;
                    this.strengthText = 'Password strength: ' + text;
                }
            }
        }
    </script>
</div>
