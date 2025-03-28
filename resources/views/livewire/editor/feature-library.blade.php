<div class="h-full flex flex-col bg-gray-50">
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
                    <span x-show="selected === 'eyes'" class="mr-2">👁️</span>
                    <span x-show="selected === 'nose'" class="mr-2">👃</span>
                    <span x-show="selected === 'mouth'" class="mr-2">👄</span>
                    <span x-show="selected === 'ears'" class="mr-2">👂</span>
                    <span x-show="selected === 'hair'" class="mr-2">💇</span>
                    <span x-show="selected === 'face'" class="mr-2">😊</span>
                    <span x-show="selected === 'accessories'" class="mr-2">🕶️</span>
                    
                    <span x-text="selected ? 
                        (selected === 'eyes' ? 'Eyes' : 
                        (selected === 'nose' ? 'Nose' : 
                        (selected === 'mouth' ? 'Mouth' : 
                        (selected === 'ears' ? 'Ears' : 
                        (selected === 'hair' ? 'Hair' : 
                        (selected === 'face' ? 'Face Shape' : 
                        (selected === 'accessories' ? 'Accessories' : 'Select a category'))))))) : 'Select a category'"></span>
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
                >
                    <span class="mr-2">👁️</span> Eyes
                </button>
                <button 
                    @click="selected = 'nose'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'nose' }"
                >
                    <span class="mr-2">👃</span> Nose
                </button>
                <button 
                    @click="selected = 'mouth'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'mouth' }"
                >
                    <span class="mr-2">👄</span> Mouth
                </button>
                <button 
                    @click="selected = 'ears'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'ears' }"
                >
                    <span class="mr-2">👂</span> Ears
                </button>
                <button 
                    @click="selected = 'hair'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'hair' }"
                >
                    <span class="mr-2">💇</span> Hair
                </button>
                <button 
                    @click="selected = 'face'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'face' }"
                >
                    <span class="mr-2">😊</span> Face Shape
                </button>
                <button 
                    @click="selected = 'accessories'; open = false" 
                    class="w-full px-3 py-2 text-sm flex items-center hover:bg-indigo-50"
                    :class="{ 'bg-indigo-50 text-indigo-700': selected === 'accessories' }"
                >
                    <span class="mr-2">🕶️</span> Accessories
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
    <div class="flex-1 overflow-y-auto p-3">
        <div class="grid grid-cols-2 gap-3">
            <!-- Sample feature items - in a real app, these would be dynamic -->
            @for($i = 1; $i <= 12; $i++)
                <div class="bg-white border border-gray-200 rounded-md overflow-hidden hover:border-[#2C3E50] transition-colors duration-200 cursor-pointer group">
                    <div class="aspect-square bg-gray-100 relative overflow-hidden">
                        <!-- Placeholder image -->
                        <div class="absolute inset-0 flex items-center justify-center bg-gray-200 text-gray-400 text-xs">
                            Feature {{ $i }}
                        </div>
                        
                        <!-- Hover overlay -->
                        <div class="absolute inset-0 bg-[#2C3E50]/60 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                            <button class="bg-white text-[#2C3E50] px-2 py-1 rounded-md text-xs font-medium">
                                Preview
                            </button>
                        </div>
                    </div>
                    <div class="p-2">
                        <p class="text-xs font-medium truncate">Feature {{ $i }}</p>
                        <p class="text-xs text-gray-500 truncate">Category</p>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
