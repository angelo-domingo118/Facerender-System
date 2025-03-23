<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h2 class="text-2xl font-bold text-[#2C3E50] mb-6 text-center">{{ __('Create Account') }}</h2>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="transform transition hover:scale-[1.01] duration-300">
                <x-input 
                    label="{{ __('Name') }}" 
                    id="name" 
                    type="text" 
                    name="name"
                    :value="old('name')" 
                    required 
                    autofocus 
                    autocomplete="name"
                    class="mb-4" />
            </div>

            <div class="transform transition hover:scale-[1.01] duration-300">
                <x-input 
                    label="{{ __('Email') }}"
                    id="email" 
                    type="email"
                    name="email" 
                    :value="old('email')" 
                    required 
                    autocomplete="username"
                    class="mb-4" />
            </div>

            <div class="transform transition hover:scale-[1.01] duration-300">
                <x-input 
                    label="{{ __('Password') }}"
                    id="password" 
                    type="password"
                    name="password" 
                    required 
                    autocomplete="new-password"
                    class="mb-4" />
            </div>

            <div class="transform transition hover:scale-[1.01] duration-300">
                <x-input 
                    label="{{ __('Confirm Password') }}"
                    id="password_confirmation" 
                    type="password"
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    class="mb-4" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mb-4 bg-[#F5F7FA] p-3 rounded-lg border-l-4 border-[#3498DB]/50">
                    <x-checkbox 
                        id="terms"
                        name="terms"
                        required
                        :label="__('I agree to the :terms_of_service and :privacy_policy', [
                            'terms_of_service' => '<a target=\'_blank\' href=\''.route('terms.show').'\' class=\'text-[#3498DB] hover:text-[#2980B9] transition-colors duration-300 font-medium hover:underline\'>'.__('Terms of Service').'</a>',
                            'privacy_policy' => '<a target=\'_blank\' href=\''.route('policy.show').'\' class=\'text-[#3498DB] hover:text-[#2980B9] transition-colors duration-300 font-medium hover:underline\'>'.__('Privacy Policy').'</a>',
                        ])" />
                </div>
            @endif

            <div class="flex items-center justify-between mb-4">
                <a class="text-sm text-[#3498DB] hover:text-[#2980B9] transition-colors duration-300 hover:underline" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button primary
                          type="submit" 
                          spinner="submit">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
