<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FACERENDER') }} | Editor</title>

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
    <body class="font-roboto bg-slate-700 text-gray-200 overflow-hidden m-0 p-0 h-full min-h-full max-h-full">
        <!-- Grid pattern overlay for the entire page -->
        <!-- <div class="fixed inset-0 w-full h-full bg-grid-pattern opacity-20 pointer-events-none z-0"></div> -->

        <div class="h-screen flex flex-col relative z-10">
            <!-- Page Content - Full Screen Editor -->
            <main class="flex-1 overflow-hidden">
                {{ $slot }}
            </main>
        </div>

        <x-notifications z-index="z-50" />
        @stack('modals')
        @livewireScripts

        <style>
            .bg-grid-pattern {
                background-color: transparent;
                background-image:
                    linear-gradient(to right, rgba(52, 152, 219, 0.6) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(52, 152, 219, 0.6) 1px, transparent 1px);
                background-size: 40px 40px;
                box-sizing: border-box;
                /* animation: moveGrid 15s linear infinite; */ /* Animation removed */
            }

            /* @keyframes moveGrid {
                0% { background-position: 0 0; }
                100% { background-position: 40px 40px; }
            } */ /* Animation keyframes removed */
        </style>
    </body>
</html> 