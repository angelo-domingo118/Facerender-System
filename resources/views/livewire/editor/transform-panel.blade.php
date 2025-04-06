<div class="h-full flex flex-col bg-gray-50">
    <div class="flex-1 overflow-y-auto">
        <!-- Layer Selector Dropdown -->
        <div class="sticky top-0 z-10 p-3 border-b border-gray-200 bg-white shadow-sm">
            <label for="layer-selector" class="block text-xs font-medium text-gray-500 mb-1">Select Layer</label>
            <select 
                id="layer-selector" 
                wire:model.live="selectedLayerId" 
                wire:change="handleLayerChange"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#2C3E50] focus:ring focus:ring-[#2C3E50]/20 text-sm"
            >
                <option value="">-- Select a layer --</option>
                @foreach($layers as $layer)
                    <option value="{{ $layer['id'] }}">{{ $layer['name'] }}</option>
                @endforeach
            </select>
        </div>
        
        @if($selectedLayer)
            <div class="p-3">
                <!-- Layer Name -->
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#2C3E50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Layer: {{ $selectedLayer['name'] }}
                    </h4>
                </div>
                
                <!-- Position Controls -->
                <div class="mb-5 bg-white p-3 rounded-md shadow-sm border border-gray-100">
                    <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#2C3E50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                        Position
                    </h4>
                    
                    <!-- Direction Controls -->
                    <div class="grid grid-cols-3 gap-2 mb-2 relative">
                        <button wire:click="moveRelative('northwest')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 -rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('north')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('northeast')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('west')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button 
                            wire:click="resetPosition" 
                            class="aspect-square bg-gray-100 hover:bg-[#2C3E50] hover:text-white active:bg-gray-600 rounded-md flex items-center justify-center transition-colors text-xs font-medium text-gray-500"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('east')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('southwest')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('south')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('southeast')" class="aspect-square bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 -rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Movement Increment Slider -->
                    <div class="mt-3">
                        <div class="mb-2 flex justify-between items-center">
                            <label class="text-xs text-gray-500">Position Increment:</label>
                            <span class="text-xs font-medium">{{ $moveIncrement }}px</span>
                        </div>
                        <input 
                            type="range" 
                            wire:model="moveIncrement" 
                            min="1" 
                            max="20" 
                            step="1" 
                            class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-[#2C3E50]"
                        >
                        <div class="flex justify-between mt-1">
                            <span class="text-xs text-gray-500">1px</span>
                            <span class="text-xs text-gray-500">20px</span>
                        </div>
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
                    
                    <!-- Size Adjustment Step -->
                    <div class="mb-3">
                        <label class="block text-xs text-gray-500 mb-1">Size Adjustment Step:</label>
                        <div class="flex space-x-2">
                            <button 
                                wire:click="setResizeStep(1)" 
                                class="flex-1 text-xs text-center font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-1 transition-colors {{ $resizeStep === 1 ? 'bg-[#2C3E50] text-white' : '' }}"
                            >
                                1px
                            </button>
                            <button 
                                wire:click="setResizeStep(5)" 
                                class="flex-1 text-xs text-center font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-1 transition-colors {{ $resizeStep === 5 ? 'bg-[#2C3E50] text-white' : '' }}"
                            >
                                5px
                            </button>
                            <button 
                                wire:click="setResizeStep(10)" 
                                class="flex-1 text-xs text-center font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-1 transition-colors {{ $resizeStep === 10 ? 'bg-[#2C3E50] text-white' : '' }}"
                            >
                                10px
                            </button>
                            <button 
                                wire:click="setResizeStep(25)" 
                                class="flex-1 text-xs text-center font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-1 transition-colors {{ $resizeStep === 25 ? 'bg-[#2C3E50] text-white' : '' }}"
                            >
                                25px
                            </button>
                        </div>
                    </div>
                    
                    <!-- Size Adjustment Buttons -->
                    <div class="grid grid-cols-4 gap-2 mb-4">
                        <button wire:click="adjustSize('decrease-width')" class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md p-2 flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                        <button wire:click="adjustSize('increase-width')" class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md p-2 flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                        <button wire:click="adjustSize('decrease-height')" class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md p-2 flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </button>
                        <button wire:click="adjustSize('increase-height')" class="bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md p-2 flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Direct Size Inputs -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-gray-50 p-2 rounded-md text-center border border-gray-200">
                            <label class="block text-xs text-gray-500 mb-1">Width</label>
                            <input
                                type="number"
                                wire:model.lazy="width"
                                wire:change="$set('size_control_used', true); updateTransform()"
                                class="w-full bg-white border border-gray-300 text-sm px-2 py-1 text-center rounded-md"
                                min="10"
                            />
                        </div>
                        <div class="bg-gray-50 p-2 rounded-md text-center border border-gray-200">
                            <label class="block text-xs text-gray-500 mb-1">Height</label>
                            <input
                                type="number"
                                wire:model.lazy="height"
                                wire:change="$set('size_control_used', true); updateTransform()"
                                class="w-full bg-white border border-gray-300 text-sm px-2 py-1 text-center rounded-md"
                                min="10"
                            />
                        </div>
                    </div>
                    
                    <!-- Aspect Ratio Toggle -->
                    <div class="flex items-center mb-4">
                        <input 
                            id="preserve-aspect-ratio" 
                            type="checkbox" 
                            wire:model.live="preserveAspectRatio"
                            class="w-4 h-4 text-[#2C3E50] border-gray-300 rounded focus:ring focus:ring-[#2C3E50]/20"
                        >
                        <label for="preserve-aspect-ratio" class="ml-2 text-xs text-gray-700">
                            Preserve aspect ratio ({{ $preserveAspectRatio ? 'On' : 'Off' }})
                        </label>
                    </div>
                    
                    <button 
                        wire:click="resetSize" 
                        class="w-full text-xs text-center font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-2 px-2 transition-colors flex items-center justify-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset Size
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
                    
                    <div class="mb-4">
                        <input 
                            type="range" 
                            wire:model="rotation" 
                            wire:change="updateTransform"
                            min="-180" 
                            max="180" 
                            step="1" 
                            class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-[#2C3E50]"
                        >
                        <div class="flex justify-between mt-2">
                            <span class="text-xs text-gray-500">-180°</span>
                            <span class="text-xs font-medium">{{ $rotation }}°</span>
                            <span class="text-xs text-gray-500">180°</span>
                        </div>
                    </div>
                    
                    <button 
                        wire:click="resetRotation" 
                        class="w-full text-xs text-center font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-2 px-2 transition-colors flex items-center justify-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset Rotation
                    </button>
                </div>
            </div>
        @else
            <div class="flex items-center justify-center p-6">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                    </svg>
                    <p class="text-gray-500 text-sm">Select a layer from the dropdown to transform it</p>
                </div>
            </div>
        @endif
    </div>
</div>
