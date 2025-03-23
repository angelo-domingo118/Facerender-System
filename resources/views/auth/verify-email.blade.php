<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <h2 class="text-2xl font-bold text-[#2C3E50] mb-2 text-center">{{ __('Email Verification') }}</h2>

        <div class="mb-6 text-center">
            <p class="text-sm text-[#34495E]">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </p>
        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg border-l-4 border-green-500 animate-pulse">
                {{ $value }}
            </div>
        @endsession

        <div class="mt-4 flex flex-col items-center justify-center space-y-4">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <div class="text-center transform transition hover:scale-[1.01] duration-300">
                    <x-button primary type="submit" spinner="submit" class="w-full">
                        {{ __('Resend Verification Email') }}
                    </x-button>
                </div>
            </form>

            <div class="text-center">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-[#3498DB] hover:text-[#2980B9] transition-colors duration-300 hover:underline">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </x-authentication-card>
</x-guest-layout>
