<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h2 class="text-2xl font-bold text-[#2C3E50] mb-6 text-center">{{ __('Reset Password') }}</h2>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="transform transition hover:scale-[1.01] duration-300">
                <x-input
                    label="{{ __('Email') }}"
                    id="email" 
                    type="email" 
                    name="email"
                    :value="old('email', $request->email)" 
                    required 
                    autofocus 
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
                    class="mb-6" />
            </div>

            <div class="flex items-center justify-end mb-4">
                <x-button primary
                          type="submit" 
                          spinner="submit">
                    {{ __('Reset Password') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
