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

            <div>
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
                        autocomplete="new-password"
                        placeholder="Enter new password" />
                </div>
                
                <!-- Password Strength Meter -->
                <div class="mt-2">
                    <div class="bg-gray-200 h-2 rounded-full overflow-hidden">
                        <div id="password-strength-meter" class="h-2 rounded-full" style="width: 0%; transition: width 0.3s;"></div>
                    </div>
                    <p id="password-strength-text" class="text-xs text-gray-500 mt-1">Password strength: Too weak</p>
                </div>

                <!-- Password Requirements -->
                <div class="mt-2 text-xs text-gray-500 space-y-1">
                    <p class="font-medium">Password must contain:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li id="req-length" class="text-gray-500">At least 8 characters</li>
                        <li id="req-uppercase" class="text-gray-500">At least one uppercase letter</li>
                        <li id="req-lowercase" class="text-gray-500">At least one lowercase letter</li>
                        <li id="req-number" class="text-gray-500">At least one number</li>
                        <li id="req-special" class="text-gray-500">At least one special character</li>
                    </ul>
                </div>

                <x-input-error for="password" class="mt-1 text-sm" />
            </div>

            <div>
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
                        autocomplete="new-password"
                        placeholder="Confirm your new password" />
                </div>
                <p id="password-match-text" class="text-xs mt-1 hidden text-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Passwords match
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
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const strengthMeter = document.getElementById('password-strength-meter');
            const strengthText = document.getElementById('password-strength-text');
            const matchText = document.getElementById('password-match-text');
            
            // Requirements elements
            const reqLength = document.getElementById('req-length');
            const reqUppercase = document.getElementById('req-uppercase');
            const reqLowercase = document.getElementById('req-lowercase');
            const reqNumber = document.getElementById('req-number');
            const reqSpecial = document.getElementById('req-special');
            
            // Check password strength and update requirements
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                let color = '';
                let text = '';
                
                // Reset requirements
                reqLength.classList.remove('text-green-500');
                reqUppercase.classList.remove('text-green-500');
                reqLowercase.classList.remove('text-green-500');
                reqNumber.classList.remove('text-green-500');
                reqSpecial.classList.remove('text-green-500');
                
                reqLength.classList.add('text-gray-500');
                reqUppercase.classList.add('text-gray-500');
                reqLowercase.classList.add('text-gray-500');
                reqNumber.classList.add('text-gray-500');
                reqSpecial.classList.add('text-gray-500');
                
                // Check length
                if (password.length >= 8) {
                    strength += 20;
                    reqLength.classList.remove('text-gray-500');
                    reqLength.classList.add('text-green-500');
                }
                
                // Check uppercase
                if (/[A-Z]/.test(password)) {
                    strength += 20;
                    reqUppercase.classList.remove('text-gray-500');
                    reqUppercase.classList.add('text-green-500');
                }
                
                // Check lowercase
                if (/[a-z]/.test(password)) {
                    strength += 20;
                    reqLowercase.classList.remove('text-gray-500');
                    reqLowercase.classList.add('text-green-500');
                }
                
                // Check numbers
                if (/[0-9]/.test(password)) {
                    strength += 20;
                    reqNumber.classList.remove('text-gray-500');
                    reqNumber.classList.add('text-green-500');
                }
                
                // Check special characters
                if (/[^A-Za-z0-9]/.test(password)) {
                    strength += 20;
                    reqSpecial.classList.remove('text-gray-500');
                    reqSpecial.classList.add('text-green-500');
                }
                
                // Determine strength text and color
                if (strength === 0) {
                    text = 'Too weak';
                    color = 'bg-gray-300';
                } else if (strength <= 20) {
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
                } else {
                    text = 'Very strong';
                    color = 'bg-green-600';
                }
                
                // Update the meter
                strengthMeter.className = 'h-2 rounded-full ' + color;
                strengthMeter.style.width = strength + '%';
                strengthText.textContent = 'Password strength: ' + text;
                
                // Check if passwords match
                checkPasswordMatch();
            });
            
            // Check if passwords match
            confirmInput.addEventListener('input', function() {
                checkPasswordMatch();
            });
            
            function checkPasswordMatch() {
                if (confirmInput.value && passwordInput.value === confirmInput.value) {
                    matchText.classList.remove('hidden', 'text-red-500');
                    matchText.classList.add('text-green-500');
                    matchText.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Passwords match';
                } else if (confirmInput.value) {
                    matchText.classList.remove('hidden', 'text-green-500');
                    matchText.classList.add('text-red-500');
                    matchText.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg> Passwords do not match';
                } else {
                    matchText.classList.add('hidden');
                }
            }
        });
    </script>
</div>
