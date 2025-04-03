<nav x-data="{ open: false }" class="bg-[#2C3E50] py-4 shadow-md">
    <!-- Primary Navigation Menu -->
    <div class="container mx-auto px-4 flex justify-between items-center">
        <div class="flex items-center">
            <!-- Logo -->
            <a href="{{ auth()->check() ? route('dashboard') : '/' }}" class="text-2xl font-bold text-white flex items-center mr-8">
                <span class="text-[#E74C3C]">FACE</span>RENDER
            </a>

            <!-- Navigation Links (Removed as requested) -->
        </div>

        <div class="flex items-center">
            <!-- Teams Dropdown -->
            @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="60" contentClasses="py-0 bg-white rounded-lg shadow-xl border border-gray-200">
                        <x-slot name="trigger">
                            <span class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white hover:text-[#3498DB] focus:outline-none transition ease-in-out duration-150">
                                    {{ Auth::user()->currentTeam->name }}

                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                    </svg>
                                </button>
                            </span>
                        </x-slot>

                        <x-slot name="content">
                            <div class="w-60">
                                <!-- Team Management -->
                                <div class="block px-4 py-3 text-xs font-medium text-gray-500 bg-gray-50 rounded-t-lg">
                                    {{ __('Manage Team') }}
                                </div>

                                <!-- Team Settings -->
                                <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ __('Team Settings') }}
                                </x-dropdown-link>

                                @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                    <x-dropdown-link href="{{ route('teams.create') }}" class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        {{ __('Create New Team') }}
                                    </x-dropdown-link>
                                @endcan

                                <!-- Team Switcher -->
                                @if (Auth::user()->allTeams()->count() > 1)
                                    <div class="border-t border-gray-200"></div>

                                    <div class="block px-4 py-3 text-xs font-medium text-gray-500 bg-gray-50">
                                        {{ __('Switch Teams') }}
                                    </div>

                                    @foreach (Auth::user()->allTeams() as $team)
                                        <x-switchable-team :team="$team" />
                                    @endforeach
                                @endif
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            @endif

            <!-- Settings Dropdown -->
            @auth
                <div class="ms-3 relative" x-data="{ userOpen: false }" @click.away="userOpen = false">
                    <div>
                        <button @click="userOpen = !userOpen" class="flex items-center space-x-2 px-3 py-2 rounded-full bg-[#34495E] hover:bg-[#3498DB] text-white focus:outline-none transition duration-150 ease-in-out group">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="size-8 rounded-full object-cover ring-2 ring-white/70" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            @else
                                <div class="size-8 rounded-full bg-[#3498DB] flex items-center justify-center text-white font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            
                            <div class="hidden md:flex items-center">
                                <span class="text-sm font-medium truncate max-w-[100px]">
                                    {{ Auth::user()->name }}
                                </span>
                                
                                <svg xmlns="http://www.w3.org/2000/svg" class="ms-1 size-4 transition-transform duration-200" :class="{'rotate-180': userOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            
                            <!-- Mobile menu toggle (only icon) -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="md:hidden size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                            </svg>
                        </button>
                    </div>

                    <div x-show="userOpen" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute z-50 mt-2 w-56 rounded-lg shadow-xl origin-top-right right-0 bg-white overflow-hidden"
                        style="display: none;">
                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-[#3498DB] to-[#2980B9] relative overflow-hidden">
                            <div class="absolute inset-0 bg-opacity-10 bg-white">
                                <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'30\' height=\'30\' viewBox=\'0 0 30 30\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M15 0C6.716 0 0 6.716 0 15c0 8.284 6.716 15 15 15 8.284 0 15-6.716 15-15 0-8.284-6.716-15-15-15zm0 6c4.986 0 9 4.014 9 9s-4.014 9-9 9-9-4.014-9-9 4.014-9 9-9z\' fill=\'%23FFF\' fill-opacity=\'.2\' fill-rule=\'evenodd\'/%3E%3C/svg%3E');background-size: 60px 60px;"></div>
                            </div>
                            <div class="flex items-center relative z-10">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <img class="h-10 w-10 rounded-full border-2 border-white shadow-md" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}">
                                @endif
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-white text-opacity-80 truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Account Management -->
                        <div class="block px-4 py-2 text-xs font-medium text-gray-500 bg-gray-50">
                            {{ __('Manage Account') }}
                        </div>

                        <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ __('Profile') }}
                        </a>

                        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                            <a href="{{ route('api-tokens.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                {{ __('API Tokens') }}
                            </a>
                        @endif

                        <div class="border-t border-gray-100"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" 
                               class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition"
                               onclick="event.preventDefault(); this.closest('form').submit();">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-white font-medium hover:text-[#3498DB] transition-colors duration-300">Log in</a>
                @if (Route::has('register'))
                    <x-button href="{{ route('register') }}" primary class="ml-4 flex items-center">Register</x-button>
                @endif
            @endauth

            <!-- Hamburger (only shown on mobile) -->
            <div class="md:hidden ml-4">
                <button @click="open = !open" class="text-white focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden bg-[#34495E] mt-2 rounded-b-lg shadow-lg">
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-600">
            <div class="flex items-center px-4">
                @auth
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div class="shrink-0 me-3">
                            <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                        </div>
                    @endif

                    <div>
                        <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-300">{{ Auth::user()->email }}</div>
                    </div>
                @else
                    <div class="py-2">
                        <a href="{{ route('login') }}" class="block text-white font-medium hover:text-[#3498DB] transition-colors duration-300">
                            {{ __('Log in') }}
                        </a>
                    </div>
                    @if (Route::has('register'))
                        <div class="py-2 ml-4">
                            <a href="{{ route('register') }}" class="block text-white bg-[#3498DB] hover:bg-[#2980B9] px-3 py-2 rounded-md font-medium transition-colors duration-300">
                                {{ __('Register') }}
                            </a>
                        </div>
                    @endif
                @endauth
            </div>

            @auth
                <div class="mt-3 space-y-1 px-4">
                    <!-- Account Management -->
                    <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')" class="text-white hover:text-[#3498DB]">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                        <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')" class="text-white hover:text-[#3498DB]">
                            {{ __('API Tokens') }}
                        </x-responsive-nav-link>
                    @endif

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" x-data>
                        @csrf

                        <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();" class="text-white hover:text-[#3498DB]">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>

                    <!-- Team Management -->
                    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                        <div class="border-t border-gray-600 my-3"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Manage Team') }}
                        </div>

                        <!-- Team Settings -->
                        <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')" class="text-white hover:text-[#3498DB]">
                            {{ __('Team Settings') }}
                        </x-responsive-nav-link>

                        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                            <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')" class="text-white hover:text-[#3498DB]">
                                {{ __('Create New Team') }}
                            </x-responsive-nav-link>
                        @endcan

                        <!-- Team Switcher -->
                        @if (Auth::user()->allTeams()->count() > 1)
                            <div class="border-t border-gray-600 my-3"></div>

                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Switch Teams') }}
                            </div>

                            @foreach (Auth::user()->allTeams() as $team)
                                <x-switchable-team :team="$team" component="responsive-nav-link" />
                            @endforeach
                        @endif
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>
