<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
            {{ config('app.name', 'FACERENDER') }}
            @if(request()->routeIs('dashboard'))
                | Dashboard
            @elseif(request()->routeIs('profile.show'))
                | Profile
            @endif
        </title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:400,500,700|lato:400,700" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- WireUI -->
        <wireui:scripts />
        @wireUiScripts
        
        <!-- Styles -->
        @livewireStyles

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    </head>
    <body class="font-roboto bg-[#2C3E50] text-gray-300 overflow-auto">
        <!-- Grid pattern overlay for the entire page -->
        <div class="fixed inset-0 w-full h-full bg-grid-pattern opacity-20 pointer-events-none -z-10"></div>

        <x-banner />

        <div class="h-full">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-transparent border-b border-[#3498DB]/20">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <x-notifications z-index="z-50" />
        @stack('modals')
        
        @livewireScripts

        @livewire('forms.edit-case-form')
        @livewire('forms.add-witness-form')
        @livewire('forms.edit-witness-form')
        @livewire('forms.create-composite-form')
        @livewire('delete-modal-manager')
        
        <!-- Page-specific scripts -->
        @stack('scripts')

        <style>
            .bg-grid-pattern {
                background-color: transparent;
                background-image:
                    linear-gradient(to right, rgba(52, 152, 219, 0.6) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(52, 152, 219, 0.6) 1px, transparent 1px);
                background-size: 40px 40px;
                box-sizing: border-box;
                animation: moveGrid 15s linear infinite;
            }

            @keyframes moveGrid {
                0% { background-position: 0 0; }
                100% { background-position: 40px 40px; }
            }
        </style>
    </body>
</html>
