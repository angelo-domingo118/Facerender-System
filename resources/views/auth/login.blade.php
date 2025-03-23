<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h2 class="text-2xl font-bold text-[#2C3E50] mb-6 text-center">{{ __('Sign In') }}</h2>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg border-l-4 border-green-500 animate-pulse">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="transform transition hover:scale-[1.01] duration-300">
                <x-input label="{{ __('Email') }}" 
                         id="email" 
                         type="email" 
                         name="email" 
                         :value="old('email')" 
                         required 
                         autofocus 
                         autocomplete="username"
                         class="mb-4" />
            </div>

            <div class="transform transition hover:scale-[1.01] duration-300">
                <x-input label="{{ __('Password') }}" 
                         id="password" 
                         type="password" 
                         name="password" 
                         required 
                         autocomplete="current-password"
                         class="mb-4" />
            </div>

            <div class="block mb-4">
                <x-checkbox id="remember_me" 
                            name="remember" 
                            label="{{ __('Remember me') }}" />
            </div>

            <div class="flex items-center justify-between mb-4">
                @if (Route::has('password.request'))
                    <a class="text-sm text-[#3498DB] hover:text-[#2980B9] transition-colors duration-300 hover:underline" 
                       href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button primary
                          type="submit" 
                          spinner="submit">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>

        <div class="mt-4 pt-4 border-t border-[#95A5A6]/20 text-center">
            <p class="text-sm text-[#34495E]">{{ __("Don't have an account?") }} 
                <a href="{{ route('register') }}" class="text-[#3498DB] hover:text-[#2980B9] transition-colors duration-300 font-medium hover:underline">
                    {{ __('Register') }}
                </a>
            </p>
        </div>
    </x-authentication-card>
</x-guest-layout>
