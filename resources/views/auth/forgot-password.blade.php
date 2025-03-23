<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h2 class="text-2xl font-bold text-[#2C3E50] mb-2 text-center">{{ __('Forgot Password') }}</h2>
        
        <div class="mb-6 text-center">
            <p class="text-sm text-[#34495E]">{{ __('No problem. Just let us know your email address and we will email you a password reset link.') }}</p>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg border-l-4 border-green-500 animate-pulse">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="transform transition hover:scale-[1.01] duration-300">
                <x-input
                    label="{{ __('Email') }}"
                    id="email" 
                    type="email"
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="username"
                    class="mb-6" />
            </div>

            <div class="flex items-center justify-between mb-4">
                <a class="text-sm text-[#3498DB] hover:text-[#2980B9] transition-colors duration-300 hover:underline" href="{{ route('login') }}">
                    {{ __('Return to login') }}
                </a>

                <x-button primary
                          type="submit" 
                          spinner="submit">
                    {{ __('Email Password Reset Link') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
