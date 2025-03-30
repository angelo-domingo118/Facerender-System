<div class="h-full flex flex-col bg-gray-50" 
    x-data="{ 
        init() {
            Livewire.on('scrollToTop', () => {
                const featureGrid = document.querySelector('.feature-grid-container');
                if (featureGrid) {
                    featureGrid.scrollTop = 0;
                }
            });
        }
    }"
>
    <!-- Removed the redundant "Feature Library" heading -->
    
    <!-- Feature Category Selection -->
    <div class="p-3 border-b border-gray-200 bg-white">
        <div x-data="{ open: false, selected: @entangle('selectedCategory') }" class="relative">
            <label class="text-sm font-medium text-gray-700 mb-1 block">Feature Category</label>
            
            <!-- Custom Dropdown Trigger -->
            <button 
                @click="open = !open" 
                type="button"
                class="w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-left text-sm flex items-center justify-between hover:border-indigo-500 transition-colors duration-150"
            >
                <!-- Show selected category or placeholder -->
                <span class="flex items-center">
                    <!-- Category Icon - conditionally show based on selection -->
                    <span x-show="selected === 'eyes'" class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </span>
                    <span x-show="selected === 'eyebrows'" class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                        </svg>
                    </span>
                    <span x-show="selected === 'nose'" class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </span>
                    <span x-show="selected === 'mouth'" class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                        </svg>
                    </span>
                    <span x-show="selected === 'ears'" class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                        </svg>
                    </span>
                    <span x-show="selected === 'hair'" class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                        </svg>
                    </span>
                    <span x-show="selected === 'face'" class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </span>
                    <span x-show="selected === 'accessories'" class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </span>
                    
                    <!-- Replace the complex nested ternary with a simpler implementation -->
                    <span class="text-gray-700 font-medium" x-show="!selected">Select a category</span>
                    <span class="text-gray-700 font-medium" x-show="selected === 'eyes'">Eyes</span>
                    <span class="text-gray-700 font-medium" x-show="selected === 'eyebrows'">Eyebrows</span>
                    <span class="text-gray-700 font-medium" x-show="selected === 'nose'">Nose</span>
                    <span class="text-gray-700 font-medium" x-show="selected === 'mouth'">Mouth</span>
                    <span class="text-gray-700 font-medium" x-show="selected === 'ears'">Ears</span>
                    <span class="text-gray-700 font-medium" x-show="selected === 'hair'">Hair</span>
                    <span class="text-gray-700 font-medium" x-show="selected === 'face'">Face Shape</span>
                    <span class="text-gray-700 font-medium" x-show="selected === 'accessories'">Accessories</span>
                </span>
                
                <!-- Dropdown indicator -->
                <svg class="h-5 w-5 text-gray-400" x-bind:class="{ 'transform rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div 
                x-show="open" 
                @click.away="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute mt-1 w-full z-10 bg-white shadow-lg rounded-lg py-1 max-h-60 overflow-auto"
                style="display: none;"
            >
                <!-- Category Options -->
                <button 
                    @click="selected = 'eyes'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'eyes' }"
                    wire:click="$set('selectedCategory', 'eyes')"
                >
                    <span class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600" :class="{ 'text-indigo-700': selected === 'eyes' }">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </span> Eyes
                </button>
                <button 
                    @click="selected = 'eyebrows'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'eyebrows' }"
                    wire:click="$set('selectedCategory', 'eyebrows')"
                >
                    <span class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600" :class="{ 'text-indigo-700': selected === 'eyebrows' }">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                        </svg>
                    </span> Eyebrows
                </button>
                <button 
                    @click="selected = 'nose'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'nose' }"
                    wire:click="$set('selectedCategory', 'nose')"
                >
                    <span class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600" :class="{ 'text-indigo-700': selected === 'nose' }">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </span> Nose
                </button>
                <button 
                    @click="selected = 'mouth'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'mouth' }"
                    wire:click="$set('selectedCategory', 'mouth')"
                >
                    <span class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600" :class="{ 'text-indigo-700': selected === 'mouth' }">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                        </svg>
                    </span> Mouth
                </button>
                <button 
                    @click="selected = 'ears'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'ears' }"
                    wire:click="$set('selectedCategory', 'ears')"
                >
                    <span class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600" :class="{ 'text-indigo-700': selected === 'ears' }">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                        </svg>
                    </span> Ears
                </button>
                <button 
                    @click="selected = 'hair'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'hair' }"
                    wire:click="$set('selectedCategory', 'hair')"
                >
                    <span class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600" :class="{ 'text-indigo-700': selected === 'hair' }">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                        </svg>
                    </span> Hair
                </button>
                <button 
                    @click="selected = 'face'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'face' }"
                    wire:click="$set('selectedCategory', 'face')"
                >
                    <span class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600" :class="{ 'text-indigo-700': selected === 'face' }">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </span> Face Shape
                </button>
                <button 
                    @click="selected = 'accessories'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'accessories' }"
                    wire:click="$set('selectedCategory', 'accessories')"
                >
                    <span class="mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-600" :class="{ 'text-indigo-700': selected === 'accessories' }">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 0 1 0 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 0 1 0-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </span> Accessories
                </button>
            </div>
        </div>
        
        <!-- Search input -->
        <div class="mt-3">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    placeholder="Search features..." 
                    wire:model.live="search"
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg w-full text-sm focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150"
                />
            </div>
        </div>
    </div>
    
    <!-- Feature Grid -->
    <div class="flex-1 overflow-y-auto p-3 feature-grid-container">
        @if(!$selectedCategory)
            <div class="flex flex-col items-center justify-center h-full text-center">
                <div class="bg-gray-50 p-8 rounded-lg border border-gray-200 max-w-md">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-gray-400 mx-auto mb-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Select a feature category</h3>
                    <p class="text-gray-500 mb-4">Please select a feature category from the dropdown above to view available features.</p>
                    <div class="flex justify-center">
                        <div class="animate-bounce rounded-full bg-indigo-50 p-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        @else
            @if($selectedCategory)
                <div class="mb-3">
                    <h3 class="text-sm font-medium text-gray-700">Categories</h3>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach($subcategories as $category)
                            <button 
                                wire:click="selectSubcategory({{ $category->id }})"
                                class="px-2 py-1 text-xs rounded-full {{ $selectedSubcategory == $category->id ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            >
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-3">
                @forelse($features as $feature)
                    <div 
                        wire:click="selectFeature({{ $feature->id }})" 
                        class="bg-white border border-gray-200 rounded-md overflow-hidden hover:border-[#2C3E50] transition-colors duration-200 cursor-pointer group"
                    >
                        <div class="aspect-square bg-gray-100 relative overflow-hidden">
                            <!-- Debug info to verify image path -->
                            <div class="hidden">Path: {{ $feature->image_path }}</div>
                            
                            <!-- Feature image -->
                            <img 
                                src="{{ asset('storage/' . $feature->image_path) }}" 
                                alt="{{ $feature->name }}" 
                                class="absolute inset-0 w-full h-full object-contain p-2"
                                onerror="this.src='{{ asset('images/placeholder.png') }}'; this.onerror=null; console.error('Failed to load image: {{ $feature->image_path }}')"
                            >
                            
                            <!-- Hover overlay -->
                            <div class="absolute inset-0 bg-[#2C3E50]/60 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                                <button class="bg-white text-[#2C3E50] px-2 py-1 rounded-md text-xs font-medium">
                                    Select
                                </button>
                            </div>
                        </div>
                        <div class="p-2">
                            <p class="text-xs font-medium truncate">{{ $feature->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $feature->category->name }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 p-4 text-center text-gray-500">
                        <p>No features found. Try adjusting your search or category.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</div>
