<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="FACERENDER - Professional Facial Composite System for forensic use">
        <title>FACERENDER - Facial Composite System</title>

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
    <body class="font-roboto text-white antialiased bg-[#2C3E50] relative">
        <!-- Grid pattern overlay for the entire page -->
        <div class="fixed inset-0 w-full h-full bg-grid-pattern opacity-10 pointer-events-none"></div>
        
        <!-- Navigation -->
        @livewire('navigation-menu')

        <!-- Hero Section -->
        <section class="text-white min-h-screen flex items-center relative overflow-hidden bg-[#2C3E50]">
            <div class="container mx-auto px-4 z-10 py-20">
                <div class="max-w-4xl mx-auto">
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold mb-8 font-lato leading-tight text-center">
                        Professional Facial <span class="text-[#3498DB]">Composite</span> System
                    </h1>
                    <p class="text-xl mb-12 text-gray-300 text-center max-w-3xl mx-auto leading-relaxed">
                        FACERENDER is a powerful web-based facial composite system designed for forensic professionals. Create accurate facial composites with an intuitive, precision-focused interface.
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

        <!-- Features Section with enhanced cards -->
        <section id="features" class="py-24 bg-[#2C3E50] backdrop-blur-sm">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4 font-lato text-white">Key Features</h2>
                    <div class="w-20 h-1 bg-[#E74C3C] mx-auto mb-6"></div>
                    <p class="text-gray-300 max-w-2xl mx-auto text-lg">Our comprehensive set of features helps you create accurate and realistic facial composites quickly and efficiently.</p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature Cards with Improved UI -->
                    <div class="group bg-[#243342] rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-[#3498DB]/20 transform hover:-translate-y-2 hover:border-[#3498DB]/70">
                        <div class="p-6">
                            <div class="bg-[#34495E] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB] group-hover:text-white transition-colors duration-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-white">Extensive Feature Library</h3>
                            <p class="text-center text-gray-300">
                                Access a comprehensive collection of pre-cropped facial features organized by categories for efficient composite creation.
                            </p>
                        </div>
                    </div>
                    
                    <div class="group bg-[#243342] rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-[#3498DB]/20 transform hover:-translate-y-2 hover:border-[#3498DB]/70">
                        <div class="p-6">
                            <div class="bg-[#34495E] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB] group-hover:text-white transition-colors duration-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-white">Advanced Editing Tools</h3>
                            <p class="text-center text-gray-300">
                                Fine-tune facial composites with precision using our advanced editing tools designed specifically for forensic requirements.
                            </p>
                        </div>
                    </div>
                    
                    <div class="group bg-[#243342] rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-[#3498DB]/20 transform hover:-translate-y-2 hover:border-[#3498DB]/70">
                        <div class="p-6">
                            <div class="bg-[#34495E] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB] group-hover:text-white transition-colors duration-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-white">Interactive Canvas</h3>
                            <p class="text-center text-gray-300">
                                Seamlessly arrange, resize, and position facial features on our dynamic canvas for realistic composite creation.
                            </p>
                        </div>
                    </div>
                    
                    <div class="group bg-[#243342] rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-[#3498DB]/20 transform hover:-translate-y-2 hover:border-[#3498DB]/70">
                        <div class="p-6">
                            <div class="bg-[#34495E] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB] group-hover:text-white transition-colors duration-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-white">Texture & Shading</h3>
                            <p class="text-center text-gray-300">
                                Apply realistic texturing and shading to enhance composite authenticity and improve witness recognition rates.
                            </p>
                        </div>
                    </div>
                    
                    <div class="group bg-[#243342] rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-[#3498DB]/20 transform hover:-translate-y-2 hover:border-[#3498DB]/70">
                        <div class="p-6">
                            <div class="bg-[#34495E] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB] group-hover:text-white transition-colors duration-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-white">Multi-Format Export</h3>
                            <p class="text-center text-gray-300">
                                Export completed composites in various formats suitable for case documentation, printing, and digital distribution.
                            </p>
                        </div>
                    </div>
                    
                    <div class="group bg-[#243342] rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-[#3498DB]/20 transform hover:-translate-y-2 hover:border-[#3498DB]/70">
                        <div class="p-6">
                            <div class="bg-[#34495E] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB] group-hover:text-white transition-colors duration-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-white">Feature Customization</h3>
                            <p class="text-center text-gray-300">
                                Customize facial features with transformations, blending, and adjustments to match witness descriptions accurately.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section with enhanced UI -->
        <section id="about" class="py-24 bg-[#2C3E50]">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4 font-lato text-white">About FACERENDER</h2>
                    <div class="w-20 h-1 bg-[#E74C3C] mx-auto mb-6"></div>
                    <p class="text-gray-300 max-w-2xl mx-auto text-lg">Our powerful web-based facial composite system is designed specifically for forensic professionals.</p>
                </div>
                
                <div class="max-w-4xl mx-auto grid md:grid-cols-3 gap-8">
                    <!-- About Cards -->
                    <div class="bg-[#243342] backdrop-blur-sm rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-[#3498DB]/20 hover:border-[#3498DB]/70">
                        <div class="p-6">
                            <div class="bg-[#34495E] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-white">What is FACERENDER?</h3>
                            <p class="text-center text-gray-300">
                                FACERENDER is a web-based system that allows law enforcement and forensic artists to create facial composites using pre-cropped facial features.
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-[#243342] backdrop-blur-sm rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-[#3498DB]/20 hover:border-[#3498DB]/70">
                        <div class="p-6">
                            <div class="bg-[#34495E] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-white">How It Works</h3>
                            <p class="text-center text-gray-300">
                                Users can interactively arrange, adjust, and retouch facial features on a dynamic canvas to create accurate suspect composites.
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-[#243342] backdrop-blur-sm rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-[#3498DB]/20 hover:border-[#3498DB]/70">
                        <div class="p-6">
                            <div class="bg-[#34495E] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB]">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.115 5.19l.319 1.913A6 6 0 008.11 10.36L9.75 12l-.387.775c-.217.433-.132.956.21 1.298l1.348 1.348c.21.21.329.497.329.795v1.089c0 .426.24.815.622 1.006l.153.076c.433.217.956.132 1.298-.21l.723-.723a8.7 8.7 0 002.288-4.042 1.087 1.087 0 00-.358-1.099l-1.33-1.108c-.251-.21-.582-.299-.905-.245l-1.17.195a1.125 1.125 0 01-.98-.314l-.295-.295a1.125 1.125 0 010-1.591l.13-.132a1.125 1.125 0 011.3-.21l.603.302a.809.809 0 001.086-1.086L14.25 7.5l1.256-.837a4.5 4.5 0 001.528-1.732l.146-.292" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-white">Our Philosophy</h3>
                            <p class="text-center text-gray-300">
                                We prioritize functionality with a clean, modern aesthetic. Our interface emphasizes precision and efficiency for law enforcement professionals.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Enhanced CTA Section -->
        <section class="py-20 bg-[#2C3E50] text-white">
            <div class="container mx-auto px-4 text-center">
                <div class="max-w-3xl mx-auto">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 font-lato">Ready to Create Accurate Facial Composites?</h2>
                    <div class="w-20 h-1 bg-[#E74C3C] mx-auto mb-6"></div>
                    <p class="text-lg mb-10 text-gray-200">
                        Join law enforcement agencies nationwide using FACERENDER to create precise facial composites that help identify suspects and solve cases.
                    </p>
                    @if (Route::has('login'))
                        @auth
                            <x-button lg href="{{ url('/dashboard') }}" class="shadow-lg hover:scale-105 transition-transform duration-300 bg-white text-[#2C3E50] hover:bg-gray-100">
                                <div class="flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    Access Your Dashboard
                                </div>
                            </x-button>
                        @else
                            <x-button lg primary href="{{ route('register') }}" class="shadow-lg hover:scale-105 transition-transform duration-300 bg-[#3498DB] hover:bg-[#2980B9]">
                                <div class="flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Get Started Today
                                </div>
                            </x-button>
                        @endauth
                    @endif
                    
                    <!-- Trust badges -->
                    <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-4 items-center justify-center text-center">
                        <div class="flex flex-col items-center">
                            <div class="text-[#3498DB] mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium">Secure Platform</p>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="text-[#3498DB] mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium">Fast Results</p>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="text-[#3498DB] mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium">Export Ready</p>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="text-[#3498DB] mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium">24/7 Support</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#2C3E50] text-white py-8">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <a href="/" class="text-2xl font-bold flex items-center">
                            <span class="text-[#E74C3C]">FACE</span>RENDER
                        </a>
                        <p class="text-sm text-gray-400 mt-2">Professional Facial Composite System</p>
                    </div>
                    <div class="text-sm text-gray-400">
                        &copy; {{ date('Y') }} FACERENDER. All rights reserved.
                    </div>
                </div>
            </div>
        </footer>

        <!-- Livewire Scripts -->
        @livewireScripts

        <style>
            .bg-grid-pattern {
                background-color: transparent;
                border: 1px dotted rgba(52, 152, 219, 0.1);
            }
        </style>
    </body>
</html>
