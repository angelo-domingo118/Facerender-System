<x-app-layout>
    <div class="py-8 bg-[#F5F7FA]">
        <div class="max-w-7xl mx-auto sm:px-4 lg:px-6">
            <div class="grid md:grid-cols-4 gap-6">
                <!-- Sidebar - made narrower -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-4 border border-[#95A5A6] sticky top-4">
                        <div class="flex items-center flex-col justify-center mb-4">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <div class="relative group mb-3">
                                    <img class="h-20 w-20 rounded-full border-2 border-[#95A5A6] object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </div>
                            @endif
                            <div class="text-center">
                                <h3 class="text-base font-bold text-[#34495E]">{{ Auth::user()->name }}</h3>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                <p class="text-xs mt-1 text-[#34495E] bg-[#F5F7FA] px-2 py-0.5 rounded-full inline-block">Account Settings</p>
                            </div>
                        </div>

                        <div class="border-t border-[#95A5A6] pt-3">
                            <nav class="flex flex-col space-y-0.5" id="profile-nav">
                                <a href="#profile-info" class="profile-nav-link group flex items-center px-2 py-1.5 text-sm font-medium rounded-md hover:bg-[#EBF0F5] hover:border-l-4 hover:border-[#95A5A6] hover:pl-1 text-gray-600 hover:text-[#34495E]" data-section="profile-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 text-gray-500 group-hover:text-[#34495E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ __('Profile Information') }}
                                </a>
                                <a href="#update-password" class="profile-nav-link group flex items-center px-2 py-1.5 text-sm font-medium rounded-md text-gray-600 hover:bg-[#EBF0F5] hover:border-l-4 hover:border-[#95A5A6] hover:pl-1 hover:text-[#34495E]" data-section="update-password">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 text-gray-500 group-hover:text-[#34495E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                    {{ __('Update Password') }}
                                </a>
                                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                                <a href="#two-factor" class="profile-nav-link group flex items-center px-2 py-1.5 text-sm font-medium rounded-md text-gray-600 hover:bg-[#EBF0F5] hover:border-l-4 hover:border-[#95A5A6] hover:pl-1 hover:text-[#34495E]" data-section="two-factor">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 text-gray-500 group-hover:text-[#34495E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    {{ __('Two Factor Authentication') }}
                                </a>
                                @endif
                                <a href="#browser-sessions" class="profile-nav-link group flex items-center px-2 py-1.5 text-sm font-medium rounded-md text-gray-600 hover:bg-[#EBF0F5] hover:border-l-4 hover:border-[#95A5A6] hover:pl-1 hover:text-[#34495E]" data-section="browser-sessions">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 text-gray-500 group-hover:text-[#34495E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ __('Browser Sessions') }}
                                </a>
                                @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                                <a href="#delete-account" class="profile-nav-link group flex items-center px-2 py-1.5 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 hover:border-l-4 hover:border-red-500 hover:pl-1" data-section="delete-account">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    {{ __('Delete Account') }}
                                </a>
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Main Content - expanded to use more space -->
                <div class="md:col-span-3 space-y-6">
                    @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                        <div id="profile-info" class="profile-section">
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-[#95A5A6]">
                                <div class="px-4 py-3 bg-[#34495E] text-white">
                                    <h3 class="text-base font-bold">{{ __('Profile Information') }}</h3>
                                    <p class="text-xs opacity-80">{{ __('Update your account\'s profile information and email address.') }}</p>
                                </div>
                                <div class="p-4">
                                    @livewire('profile.update-profile-information-form')
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                        <div id="update-password" class="profile-section">
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-[#95A5A6]">
                                <div class="px-4 py-3 bg-[#34495E] text-white">
                                    <h3 class="text-base font-bold">{{ __('Update Password') }}</h3>
                                    <p class="text-xs opacity-80">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
                                </div>
                                <div class="p-4">
                                    @livewire('profile.update-password-form')
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                        <div id="two-factor" class="profile-section">
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-[#95A5A6]">
                                <div class="px-4 py-3 bg-[#34495E] text-white">
                                    <h3 class="text-base font-bold">{{ __('Two Factor Authentication') }}</h3>
                                    <p class="text-xs opacity-80">{{ __('Add additional security to your account using two factor authentication.') }}</p>
                                </div>
                                <div class="p-4">
                                    @livewire('profile.two-factor-authentication-form')
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="browser-sessions" class="profile-section">
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-[#95A5A6]">
                            <div class="px-4 py-3 bg-[#34495E] text-white">
                                <h3 class="text-base font-bold">{{ __('Browser Sessions') }}</h3>
                                <p class="text-xs opacity-80">{{ __('Manage and log out your active sessions on other browsers and devices.') }}</p>
                            </div>
                            <div class="p-4">
                                @livewire('profile.logout-other-browser-sessions-form')
                            </div>
                        </div>
                    </div>

                    @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                        <div id="delete-account" class="profile-section">
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-[#95A5A6]">
                                <div class="px-4 py-3 bg-[#E74C3C] text-white">
                                    <h3 class="text-base font-bold">{{ __('Delete Account') }}</h3>
                                    <p class="text-xs opacity-80">{{ __('Permanently delete your account.') }}</p>
                                </div>
                                <div class="p-4">
                                    @livewire('profile.delete-user-form')
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Navigation Highlighting -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all navigation links and sections
            const navLinks = document.querySelectorAll('.profile-nav-link');
            const sections = document.querySelectorAll('.profile-section');
            
            // Set active nav link style
            function setActiveLink(sectionId) {
                navLinks.forEach(link => {
                    // Remove active class from all links
                    link.classList.remove('bg-[#EBF0F5]', 'text-[#34495E]', 'border-l-4', 'border-[#34495E]', 'pl-1', 'font-medium');
                    link.classList.add('text-gray-600');
                    
                    // Find svg element within link and update its color
                    const svg = link.querySelector('svg');
                    if (svg) {
                        svg.classList.remove('text-[#34495E]');
                        svg.classList.add('text-gray-500');
                    }
                    
                    // Add active class to current section link
                    if (link.getAttribute('data-section') === sectionId) {
                        link.classList.remove('text-gray-600');
                        link.classList.add('bg-[#EBF0F5]', 'text-[#34495E]', 'border-l-4', 'border-[#34495E]', 'pl-1', 'font-medium');
                        
                        // Update svg color for active link
                        if (svg) {
                            svg.classList.remove('text-gray-500');
                            svg.classList.add('text-[#34495E]');
                        }
                    }
                });
            }
            
            // Handle click events on navigation links
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const sectionId = this.getAttribute('data-section');
                    setActiveLink(sectionId);
                });
            });
            
            // Handle scroll events to update active link based on scroll position
            window.addEventListener('scroll', function() {
                let currentSection = '';
                
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    
                    // If we've scrolled to or past a section, mark it as current
                    if (pageYOffset >= (sectionTop - 200)) {
                        currentSection = section.getAttribute('id');
                    }
                });
                
                if (currentSection) {
                    setActiveLink(currentSection);
                }
            });
            
            // Set initial active link based on URL hash or default to first section
            function setInitialActiveLink() {
                let hash = window.location.hash;
                let sectionId = hash ? hash.substring(1) : sections[0].getAttribute('id');
                
                // If hash exists but doesn't match a section, default to first section
                if (hash && !document.getElementById(sectionId)) {
                    sectionId = sections[0].getAttribute('id');
                }
                
                setActiveLink(sectionId);
                
                // If hash exists, scroll to that section
                if (hash) {
                    setTimeout(() => {
                        const element = document.querySelector(hash);
                        if (element) {
                            element.scrollIntoView();
                        }
                    }, 100);
                }
            }
            
            // Initialize
            setInitialActiveLink();
        });
    </script>
</x-app-layout>
