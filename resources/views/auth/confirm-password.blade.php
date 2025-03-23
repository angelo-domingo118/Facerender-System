<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h2 class="text-2xl font-bold text-[#2C3E50] mb-2 text-center">{{ __('Secure Area') }}</h2>

        <div class="mb-6 text-center">
            <p class="text-sm text-[#34495E]">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>
        </div>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="transform transition hover:scale-[1.01] duration-300">
                <x-input
                    label="{{ __('Password') }}"
                    id="password" 
                    type="password"
                    name="password" 
                    required 
                    autocomplete="current-password" 
                    autofocus
                    class="mb-6" />
            </div>

            <div class="flex items-center justify-end mb-4">
                <x-button primary
                          type="submit" 
                          spinner="submit">
                    {{ __('Confirm') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
