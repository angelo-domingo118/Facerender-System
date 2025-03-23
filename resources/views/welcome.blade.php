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

        <!-- WireUI -->
        <wireui:scripts />
        @wireUiScripts

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Livewire Styles -->
        @livewireStyles
    </head>
    <body class="font-roboto bg-[#F5F7FA] text-[#34495E]">
        <!-- Navigation -->
        @livewire('navigation-menu')

        <!-- Hero Section with white background -->
        <section class="bg-white text-[#2C3E50] py-20">
            <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 md:pr-10">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 font-lato leading-tight">
                        Professional Facial <span class="text-[#3498DB]">Composite</span> System
                    </h1>
                    <p class="text-lg mb-8 text-gray-600 max-w-lg">
                        FACERENDER is a powerful web-based facial composite system designed for forensic professionals. Create accurate facial composites with an intuitive, precision-focused interface.
                    </p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <x-button href="{{ url('/dashboard') }}" lg primary class="shadow-md">
                                    <div class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                        Go to Dashboard
                                    </div>
                                </x-button>
                            @else
                                <x-button href="{{ route('login') }}" lg primary outline class="shadow-md">
                                    <div class="flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                        </svg>
                                        Get Started
                                    </div>
                                </x-button>
                                @if (Route::has('register'))
                                    <x-button href="{{ route('register') }}" lg primary class="shadow-md">
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
                <div class="md:w-1/2 mt-10 md:mt-0">
                    <div class="relative">
                        <div class="absolute -inset-1 bg-gradient-to-r from-[#3498DB] to-[#E74C3C] rounded-lg blur-sm opacity-75"></div>
                        <img src="https://placehold.co/600x400/2C3E50/FFFFFF?text=FACERENDER" alt="FACERENDER Interface" class="relative rounded-lg shadow-xl w-full transform hover:scale-105 transition-transform duration-500 z-10">
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section with enhanced cards -->
        <section id="features" class="py-24 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4 font-lato text-[#2C3E50]">Key Features</h2>
                    <div class="w-20 h-1 bg-[#3498DB] mx-auto mb-4"></div>
                    <p class="text-gray-600 max-w-2xl mx-auto">Our comprehensive set of features helps you create accurate and realistic facial composites quickly and efficiently.</p>
                </div>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature Cards with Improved UI -->
                    <div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                        <div class="p-6">
                            <div class="bg-[#EBF5FF] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB] group-hover:text-white transition-colors duration-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-[#2C3E50]">Extensive Feature Library</h3>
                            <p class="text-center text-gray-600">
                                Access a comprehensive collection of pre-cropped facial features organized by categories for efficient composite creation.
                            </p>
                        </div>
                    </div>
                    
                    <div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                        <div class="p-6">
                            <div class="bg-[#EBF5FF] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB] group-hover:text-white transition-colors duration-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-[#2C3E50]">Advanced Editing Tools</h3>
                            <p class="text-center text-gray-600">
                                Fine-tune facial composites with precision using our advanced editing tools designed specifically for forensic requirements.
                            </p>
                        </div>
                    </div>
                    
                    <div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                        <div class="p-6">
                            <div class="bg-[#EBF5FF] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB] group-hover:text-white transition-colors duration-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-[#2C3E50]">Interactive Canvas</h3>
                            <p class="text-center text-gray-600">
                                Seamlessly arrange, resize, and position facial features on our dynamic canvas for realistic composite creation.
                            </p>
                        </div>
                    </div>
                    
                    <div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                        <div class="p-6">
                            <div class="bg-[#EBF5FF] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB] group-hover:text-white transition-colors duration-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-[#2C3E50]">Texture & Shading</h3>
                            <p class="text-center text-gray-600">
                                Apply realistic texturing and shading to enhance composite authenticity and improve witness recognition rates.
                            </p>
                        </div>
                    </div>
                    
                    <div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                        <div class="p-6">
                            <div class="bg-[#EBF5FF] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB] group-hover:text-white transition-colors duration-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25a2.25 2.25 0 0 1 2.25-2.25h10.5A2.25 2.25 0 0 1 15 5.25v10.5a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15v10.5c0 .807.418 1.57 1.125 2.007.717.438 1.637.438 2.35 0l2.755-4.5a3 3 0 0 1 .879-2.121l4.5 2.755A3 3 0 0 1 15 17.25v1.007c.582.223 1.097.682 1.125 2.007.12.715.12 1.479 0 2.195h10.5a2.25 2.25 0 0 0 2.25-2.25V5.25a2.25 2.25 0 0 0-2.25-2.25H3.75z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-[#2C3E50]">Multi-Format Export</h3>
                            <p class="text-center text-gray-600">
                                Export completed composites in various formats suitable for case documentation, printing, and digital distribution.
                            </p>
                        </div>
                    </div>
                    
                    <div class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                        <div class="p-6">
                            <div class="bg-[#EBF5FF] rounded-full w-16 h-16 flex items-center justify-center mb-6 mx-auto group-hover:bg-[#3498DB] transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-[#3498DB] group-hover:text-white transition-colors duration-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75a1.5 1.5 0 0 0-1.5 1.5v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-3 text-center font-lato text-[#2C3E50]">Feature Customization</h3>
                            <p class="text-center text-gray-600">
                                Customize facial features with transformations, blending, and adjustments to match witness descriptions accurately.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section with enhanced UI -->
        <section id="about" class="py-24 bg-[#F8FAFC]">
            <div class="container mx-auto px-4">
                <div class="flex flex-col md:flex-row items-center gap-12">
                    <div class="md:w-1/2">
                        <div class="relative">
                            <div class="absolute -inset-1 bg-gradient-to-r from-[#3498DB] to-[#2C3E50] rounded-lg blur-sm opacity-75 -rotate-1"></div>
                            <img src="https://placehold.co/600x400/3498DB/FFFFFF?text=About+FACERENDER" alt="About FACERENDER" class="relative rounded-lg shadow-xl w-full z-10">
                        </div>
                    </div>
                    <div class="md:w-1/2">
                        <h2 class="text-3xl md:text-4xl font-bold mb-6 font-lato text-[#2C3E50]">About FACERENDER</h2>
                        <div class="w-20 h-1 bg-[#3498DB] mb-6"></div>
                        <div class="space-y-4 text-gray-700">
                            <p>
                                FACERENDER is a web-based facial composite system designed for forensic use. The application allows law enforcement professionals and forensic artists to assemble and edit facial composites using pre-cropped images of individual facial features (such as eyes, nose, and mouth).
                            </p>
                            <p>
                                Users can interactively arrange, adjust, and retouch these features on a dynamic canvas to create a composite that resembles a suspect's face. FACERENDER aims to streamline the composite creation process with an intuitive interface and robust image manipulation capabilities.
                            </p>
                            <p>
                                Our design philosophy follows a professional, focused approach that prioritizes functionality while maintaining a clean, modern aesthetic suitable for forensic work. The interface emphasizes precision, clarity, and efficiency to support law enforcement professionals in creating accurate facial composites.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Enhanced CTA Section -->
        <section class="py-20 bg-gradient-to-br from-[#2C3E50] to-[#34495E] text-white">
            <div class="container mx-auto px-4 text-center">
                <div class="max-w-3xl mx-auto">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 font-lato">Ready to Create Accurate Facial Composites?</h2>
                    <div class="w-20 h-1 bg-[#E74C3C] mx-auto mb-6"></div>
                    <p class="text-lg mb-10 text-gray-200">
                        Join law enforcement agencies nationwide using FACERENDER to create precise facial composites that help identify suspects and solve cases.
                    </p>
                    @if (Route::has('login'))
                        @auth
                            <x-button lg white href="{{ url('/dashboard') }}" class="shadow-lg hover:scale-105 transition-transform duration-300">
                                <div class="flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                    Access Your Dashboard
                                </div>
                            </x-button>
                        @else
                            <x-button lg primary href="{{ route('register') }}" class="shadow-lg hover:scale-105 transition-transform duration-300">
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

        <!-- Livewire Scripts -->
        @livewireScripts
    </body>
</html>
