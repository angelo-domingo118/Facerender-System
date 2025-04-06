<div class="h-full flex flex-col bg-gray-50">
    <div class="p-3 border-b border-gray-200 bg-white">
        <h3 class="font-medium text-[#2C3E50]">Feature Transform</h3>
    </div>
    
    <div class="flex-1 overflow-y-auto">
        <div class="p-3">
            <!-- Feature Name -->
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#2C3E50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Feature: {{ $featureName }}
                </h4>
                <div class="bg-gray-100 p-2 rounded-md border border-gray-200">
                    <p class="text-xs text-gray-500">Selected feature information</p>
                </div>
            </div>
            
            <!-- Position Controls -->
            <div class="mb-5 bg-white p-3 rounded-md shadow-sm border border-gray-100">
                <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#2C3E50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                    Position
                </h4>
                
                <!-- Movement Increment Slider -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-1">
                        <label class="text-xs font-medium text-gray-500">Movement Increment</label>
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded-md font-medium">{{ $moveIncrement }} px</span>
                    </div>
                    <input 
                        type="range" 
                        wire:model="moveIncrement" 
                        min="1" 
                        max="10" 
                        step="1" 
                        class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-[#2C3E50]"
                    >
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-gray-400">Fine (1px)</span>
                        <span class="text-xs text-gray-400">Coarse (10px)</span>
                    </div>
                </div>
                
                <!-- Position Indicators -->
                <!-- Removing position indicators as requested -->
                
                <!-- Direction Controls - 8 directions -->
                <div class="grid grid-cols-3 gap-2 mb-2 relative">
                    <button wire:click="moveRelative('northwest')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                        <!-- NW Arrow -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 -rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <button wire:click="moveRelative('north')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                        <!-- North Arrow -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <button wire:click="moveRelative('northeast')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                        <!-- NE Arrow -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <button wire:click="moveRelative('west')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                        <!-- West Arrow -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button 
                        wire:click="resetPosition" 
                        class="aspect-square bg-gray-100 hover:bg-[#2C3E50] hover:text-white active:bg-gray-600 rounded-md flex items-center justify-center transition-colors text-xs font-medium text-gray-500"
                        x-data=""
                        x-tooltip.raw="Reset position to 0,0"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                    <button wire:click="moveRelative('east')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                        <!-- East Arrow -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <button wire:click="moveRelative('southwest')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                        <!-- SW Arrow -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <button wire:click="moveRelative('south')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                        <!-- South Arrow -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <button wire:click="moveRelative('southeast')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                        <!-- SE Arrow -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 -rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Size Controls -->
            <div class="mb-5 bg-white p-3 rounded-md shadow-sm border border-gray-100">
                <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#2C3E50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                    </svg>
                    Size
                </h4>
                
                <!-- Size Values Display -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-gray-50 p-2 rounded-md text-center border border-gray-200">
                        <label class="block text-xs text-gray-500 mb-1">Width</label>
                        <span class="font-medium text-sm">{{ $width }} px</span>
                    </div>
                    <div class="bg-gray-50 p-2 rounded-md text-center border border-gray-200">
                        <label class="block text-xs text-gray-500 mb-1">Height</label>
                        <span class="font-medium text-sm">{{ $height }} px</span>
                    </div>
                </div>
                
                <!-- Aspect Ratio Control -->
                <div class="flex items-center justify-between mb-4 px-2 py-2 bg-gray-50 rounded-md border border-gray-200">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z" />
                        </svg>
                        <label class="text-xs font-medium text-gray-500">Lock Aspect Ratio</label>
                    </div>
                    <div 
                        wire:click="toggleAspectRatio" 
                        class="relative flex items-center cursor-pointer w-10 h-5"
                    >
                        <div class="h-5 w-10 rounded-full {{ $aspectRatioLocked ? 'bg-[#2C3E50]' : 'bg-gray-300' }} transition-colors"></div>
                        <div class="absolute h-4 w-4 bg-white rounded-full transition-all transform {{ $aspectRatioLocked ? 'translate-x-5' : 'translate-x-1' }} shadow-md"></div>
                    </div>
                </div>
                
                <!-- Size Sliders -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-medium text-gray-500">Width</label>
                        <div class="flex">
                            <button 
                                wire:click="adjustSize('width', -5)" 
                                class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-l-md text-xs px-3 py-1 font-medium transition-colors"
                            >−</button>
                            <input 
                                type="number" 
                                wire:model.lazy="width" 
                                class="w-16 bg-white border-y border-gray-300 text-sm px-2 py-1 text-center"
                                min="10"
                                max="1000"
                            />
                            <button 
                                wire:click="adjustSize('width', 5)" 
                                class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-r-md text-xs px-3 py-1 font-medium transition-colors"
                            >+</button>
                        </div>
                    </div>
                    <input 
                        type="range" 
                        wire:model="width" 
                        min="10" 
                        max="500" 
                        step="1" 
                        class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-[#2C3E50]"
                    >
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-xs font-medium text-gray-500">Height</label>
                        <div class="flex">
                            <button 
                                wire:click="adjustSize('height', -5)" 
                                class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-l-md text-xs px-3 py-1 font-medium transition-colors"
                            >−</button>
                            <input 
                                type="number" 
                                wire:model.lazy="height" 
                                class="w-16 bg-white border-y border-gray-300 text-sm px-2 py-1 text-center"
                                min="10"
                                max="1000"
                            />
                            <button 
                                wire:click="adjustSize('height', 5)" 
                                class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-r-md text-xs px-3 py-1 font-medium transition-colors"
                            >+</button>
                        </div>
                    </div>
                    <input 
                        type="range" 
                        wire:model="height" 
                        min="10" 
                        max="500" 
                        step="1" 
                        class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-[#2C3E50]"
                    >
                </div>
                
                <button 
                    wire:click="resetSize" 
                    class="w-full text-xs text-center font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-2 px-2 transition-colors flex items-center justify-center"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset to Default Size (100×100)
                </button>
            </div>
            
            <!-- Rotation Controls -->
            <div class="mb-4 bg-white p-3 rounded-md shadow-sm border border-gray-100">
                <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#2C3E50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Rotation
                </h4>
                
                <!-- Visual Rotation Display -->
                <div class="mb-4 bg-gray-50 p-3 rounded-md border border-gray-200 flex items-center justify-center">
                    <div class="relative w-16 h-16 flex items-center justify-center">
                        <!-- Rotation Circle -->
                        <div class="absolute w-full h-full rounded-full border-2 border-dashed border-gray-300"></div>
                        
                        <!-- Rotation Indicator -->
                        <div class="absolute w-full h-full" style="transform: rotate({{ $rotation }}deg)">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-1 h-3 bg-[#2C3E50]"></div>
                        </div>
                        
                        <!-- Rotation Value -->
                        <span class="font-medium text-sm relative">{{ $rotation }}°</span>
                    </div>
                </div>
                
                <!-- Rotation Quick Adjust Buttons -->
                <div class="grid grid-cols-2 gap-2 mb-4">
                    <button 
                        wire:click="adjustRotation(-15)" 
                        class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md text-xs py-2 font-medium transition-colors flex items-center justify-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        -15°
                    </button>
                    <button 
                        wire:click="adjustRotation(15)" 
                        class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md text-xs py-2 font-medium transition-colors flex items-center justify-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        +15°
                    </button>
                </div>
                
                <!-- Angle Slider -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex">
                            <button wire:click="adjustRotation(-5)" class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md text-xs px-2 py-1 mr-1 font-medium transition-colors">-5°</button>
                            <button wire:click="adjustRotation(-1)" class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md text-xs px-2 py-1 mr-1 font-medium transition-colors">-1°</button>
                        </div>
                        <div class="flex">
                            <button wire:click="adjustRotation(1)" class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md text-xs px-2 py-1 ml-1 font-medium transition-colors">+1°</button>
                            <button wire:click="adjustRotation(5)" class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md text-xs px-2 py-1 ml-1 font-medium transition-colors">+5°</button>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-xs text-gray-400 mr-2">-180°</span>
                        <input 
                            type="range" 
                            wire:model="rotation" 
                            min="-180" 
                            max="180" 
                            step="1" 
                            class="flex-1 h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-[#2C3E50]"
                        >
                        <span class="text-xs text-gray-400 ml-2">180°</span>
                    </div>
                </div>
                
                <!-- Quick Rotation Buttons -->
                <div class="grid grid-cols-4 gap-2 mb-4">
                    <button 
                        wire:click="setRotation(0)" 
                        class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-2 text-xs font-medium transition-colors {{ $rotation == 0 ? 'bg-[#2C3E50] text-white' : '' }}"
                    >0°</button>
                    <button 
                        wire:click="setRotation(90)" 
                        class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-2 text-xs font-medium transition-colors {{ $rotation == 90 ? 'bg-[#2C3E50] text-white' : '' }}"
                    >90°</button>
                    <button 
                        wire:click="setRotation(180)" 
                        class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-2 text-xs font-medium transition-colors {{ $rotation == 180 ? 'bg-[#2C3E50] text-white' : '' }}"
                    >180°</button>
                    <button 
                        wire:click="setRotation(-90)" 
                        class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-2 text-xs font-medium transition-colors {{ $rotation == -90 ? 'bg-[#2C3E50] text-white' : '' }}"
                    >-90°</button>
                </div>
                
                <!-- Flip Controls -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <button 
                        wire:click="toggleFlipHorizontal" 
                        class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-2 px-2 transition-colors flex items-center justify-center {{ $flipHorizontal ? 'bg-[#2C3E50] text-white' : '' }}"
                    >
                        <!-- Arrows Right Left Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <span class="text-xs font-medium">Flip Horizontal</span>
                    </button>
                    <button 
                        wire:click="toggleFlipVertical" 
                        class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-2 px-2 transition-colors flex items-center justify-center {{ $flipVertical ? 'bg-[#2C3E50] text-white' : '' }}"
                    >
                        <!-- Arrows Up Down Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                        </svg>
                        <span class="text-xs font-medium">Flip Vertical</span>
                    </button>
                </div>
                
                <button 
                    wire:click="resetRotation" 
                    class="w-full text-xs text-center font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-2 px-2 transition-colors flex items-center justify-center"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset Rotation & Flips
                </button>
            </div>
        </div>
    </div>
</div>

