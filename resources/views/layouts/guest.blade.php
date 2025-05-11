<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-hidden m-0 p-0 h-full min-h-full max-h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="FACERENDER - Professional Facial Composite System for forensic use">

        <title>
            {{ config('app.name', 'FACERENDER') }}
            @if(request()->routeIs('login'))
                | Login
            @elseif(request()->routeIs('register'))
                | Register
            @elseif(request()->routeIs('password.request') || request()->routeIs('password.reset'))
                | Reset Password
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
    <body class="font-roboto bg-[#2C3E50] text-[#34495E] overflow-hidden m-0 p-0 h-full min-h-full max-h-full">
        <!-- Grid pattern overlay for the entire page -->
        <div class="fixed inset-0 w-full h-full bg-grid-pattern opacity-20 pointer-events-none"></div>
        
        <div class="antialiased">
            {{ $slot }}
        </div>

        @livewireScripts
        
        <style>
            .bg-grid-pattern {
                background-color: transparent;
                background-image:
                    linear-gradient(to right, rgba(52, 152, 219, 0.6) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(52, 152, 219, 0.6) 1px, transparent 1px);
                background-size: 40px 40px;
                box-sizing: border-box;
                animation: moveGrid 15s linear infinite;
                z-index: 1;
            }

            @keyframes moveGrid {
                0% { background-position: 0 0; }
                100% { background-position: 40px 40px; }
            }
        </style>
    </body>
</html>
