<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h2 class="text-2xl font-bold text-[#2C3E50] mb-4 text-center">{{ __('Two Factor Authentication') }}</h2>

        <div x-data="{ recovery: false }">
            <div class="mb-6 text-center bg-[#F5F7FA] p-4 rounded-lg" x-show="! recovery">
                <p class="text-sm text-[#34495E]">
                    {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
                </p>
            </div>

            <div class="mb-6 text-center bg-[#F5F7FA] p-4 rounded-lg" x-show="recovery">
                <p class="text-sm text-[#34495E]">
                    {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
                </p>
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('two-factor.login') }}">
                @csrf

                <div class="mt-4 transform transition hover:scale-[1.01] duration-300" x-show="! recovery">
                    <x-input
                        label="{{ __('Authentication Code') }}" 
                        id="code" 
                        type="text" 
                        inputmode="numeric" 
                        name="code" 
                        autofocus 
                        x-ref="code" 
                        autocomplete="one-time-code"
                        class="mb-4" />
                </div>

                <div class="mt-4 transform transition hover:scale-[1.01] duration-300" x-show="recovery">
                    <x-input
                        label="{{ __('Recovery Code') }}" 
                        id="recovery_code" 
                        type="text" 
                        name="recovery_code" 
                        x-ref="recovery_code" 
                        autocomplete="one-time-code"
                        class="mb-4" />
                </div>

                <div class="flex items-center justify-between mt-4">
                    <button type="button" class="text-sm text-[#3498DB] hover:text-[#2980B9] transition-colors duration-300 hover:underline"
                            x-show="! recovery"
                            x-on:click="
                                recovery = true;
                                $nextTick(() => { $refs.recovery_code.focus() })
                            ">
                        {{ __('Use a recovery code') }}
                    </button>

                    <button type="button" class="text-sm text-[#3498DB] hover:text-[#2980B9] transition-colors duration-300 hover:underline"
                            x-show="recovery"
                            x-on:click="
                                recovery = false;
                                $nextTick(() => { $refs.code.focus() })
                            ">
                        {{ __('Use an authentication code') }}
                    </button>

                    <x-button primary
                            type="submit" 
                            spinner="submit">
                        {{ __('Log in') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
