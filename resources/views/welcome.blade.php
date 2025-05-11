<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-hidden m-0 p-0 h-full min-h-full max-h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="FACERENDER - Professional Facial Composite System for forensic use">
        <title>FACERENDER</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:400,500,700|lato:400,700" rel="stylesheet" />

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- WireUI -->
        <wireui:scripts />
        @wireUiScripts

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Livewire Styles -->
        @livewireStyles
    </head>
    <body class="font-roboto text-white antialiased bg-[#2C3E50] relative overflow-hidden m-0 p-0 h-full min-h-full max-h-full">
        <!-- Grid pattern overlay for the entire page -->
        <div class="fixed inset-0 w-full h-full bg-grid-pattern opacity-20 pointer-events-none"></div>
        
        <!-- Navigation -->
        @livewire('navigation-menu')

        <!-- Hero Section -->
        <section class="text-white h-[calc(100vh-64px)] flex items-center justify-center relative bg-[#2C3E50]">
            <div class="container mx-auto px-4 z-10">
                <div class="max-w-4xl mx-auto text-center">
                    <!-- Main Brand Name -->
                    <h1 class="text-6xl md:text-7xl lg:text-8xl font-bold font-lato leading-none mb-4">
                        <span class="text-[#E74C3C]">FACE</span><span class="text-white">RENDER</span>
                    </h1>
                    <!-- Tagline / Sub-heading -->
                    <h2 class="text-3xl md:text-4xl font-semibold text-gray-200 mb-8 font-lato leading-snug">
                        Professional Facial <span class="text-[#3498DB]">Composite</span> System
                    </h2>
                    <!-- Description -->
                    <p class="text-lg md:text-xl text-gray-300 max-w-2xl mx-auto mb-12 leading-relaxed">
                        The powerful web-based tool for forensic professionals. Create accurate facial composites with an intuitive, precision-focused interface.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-6 justify-center">
                        @if (Route::has('login'))
                            @auth
                                <x-button href="{{ url('/dashboard') }}" lg primary class="shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 bg-[#3498DB] hover:bg-[#2980B9]">
                                    <div class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                        Go to Dashboard
                                    </div>
                                </x-button>
                            @else
                                <x-button href="{{ route('login') }}" lg outline class="shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 text-white border-white hover:bg-white/10">
                                    <div class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                        Get Started
                                    </div>
                                </x-button>
                                @if (Route::has('register'))
                                    <x-button href="{{ route('register') }}" lg primary class="shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 bg-[#3498DB] hover:bg-[#2980B9]">
                                        <div class="flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                            </svg>
                                            Create Account
                                        </div>
                                    </x-button>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Livewire Scripts -->
        @livewireScripts

        <style>
            .bg-grid-pattern {
                background-color: transparent;
                background-image:
                    linear-gradient(to right, rgba(52, 152, 219, 0.6) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(52, 152, 219, 0.6) 1px, transparent 1px);
                background-size: 40px 40px;
                box-sizing: border-box;
                animation: moveGrid 15s linear infinite; /* Slowed down animation */
                z-index: 1; /* Ensure it's above the background */
            }

            @keyframes moveGrid {
                0% { background-position: 0 0; }
                100% { background-position: 40px 40px; }
            }
        </style>
    </body>
</html>
