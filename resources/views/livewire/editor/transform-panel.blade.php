<div class="h-full flex flex-col bg-slate-800/60">
    <div class="flex-1 overflow-y-auto transform-panel-scroll-container">
        <!-- Layer Selector Dropdown -->
        <div class="sticky top-0 z-10 p-3 border-b border-slate-600 bg-slate-700/80 backdrop-blur-sm shadow-md">
            <label for="layer-selector" class="block text-xs font-medium text-slate-300 mb-1">Select Layer</label>
            <select 
                id="layer-selector" 
                wire:model.live="selectedLayerId" 
                wire:change="handleLayerChange"
                class="w-full rounded-md bg-slate-600 border-slate-500 text-slate-200 shadow-sm focus:border-[#3498DB] focus:ring focus:ring-[#3498DB]/30 text-sm"
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
                    <h4 class="text-sm font-medium text-slate-200 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#3498DB]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Layer: {{ $selectedLayer['name'] }}
                    </h4>
                </div>
                
                <!-- Position Controls -->
                <div class="mb-5 bg-slate-700/50 p-3 rounded-md shadow-lg border border-slate-600">
                    <h4 class="text-sm font-medium text-slate-200 mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#3498DB]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                        Position
                    </h4>
                    
                    <!-- Direction Controls -->
                    <div class="grid grid-cols-3 gap-2 mb-2 relative">
                        <button wire:click="moveRelative('northwest')" class="aspect-square bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300 -rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('north')" class="aspect-square bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('northeast')" class="aspect-square bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300 rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('west')" class="aspect-square bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button 
                            wire:click="resetPosition" 
                            class="aspect-square bg-slate-600 hover:bg-[#3498DB] hover:text-white active:bg-slate-400 rounded-md flex items-center justify-center transition-colors text-xs font-medium text-slate-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('east')" class="aspect-square bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('southwest')" class="aspect-square bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300 rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('south')" class="aspect-square bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <button wire:click="moveRelative('southeast')" class="aspect-square bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md flex items-center justify-center transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300 -rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Direct Position Inputs -->
                    <div class="grid grid-cols-2 gap-3 mt-3 mb-3">
                        <div class="bg-slate-600 p-2 rounded-md text-center border border-slate-500">
                            <label class="block text-xs text-slate-300 mb-1">X</label>
                            <input
                                type="number"
                                wire:model.lazy="positionX"
                                wire:change="updateTransform()"
                                class="w-full bg-slate-500 border-slate-400 text-slate-200 placeholder-slate-400 text-sm px-2 py-1 text-center rounded-md focus:ring-[#3498DB] focus:border-[#3498DB]"
                            />
                        </div>
                        <div class="bg-slate-600 p-2 rounded-md text-center border border-slate-500">
                            <label class="block text-xs text-slate-300 mb-1">Y</label>
                            <input
                                type="number"
                                wire:model.lazy="positionY"
                                wire:change="updateTransform()"
                                class="w-full bg-slate-500 border-slate-400 text-slate-200 placeholder-slate-400 text-sm px-2 py-1 text-center rounded-md focus:ring-[#3498DB] focus:border-[#3498DB]"
                            />
                        </div>
                    </div>
                    
                    <!-- Movement Increment Slider -->
                    <div class="mt-3">
                        <div class="mb-2 flex justify-between items-center">
                            <label class="text-xs text-slate-300">Position Increment:</label>
                            <span class="text-xs font-medium text-slate-200">{{ $moveIncrement }}px</span>
                        </div>
                        <input 
                            type="range" 
                            wire:model="moveIncrement" 
                            min="1" 
                            max="20" 
                            step="1" 
                            class="w-full h-2 bg-slate-500 rounded-full appearance-none cursor-pointer accent-[#3498DB]"
                        >
                        <div class="flex justify-between mt-1">
                            <span class="text-xs text-slate-400">1px</span>
                            <span class="text-xs text-slate-400">20px</span>
                        </div>
                    </div>
                </div>
                
                <!-- Size Controls -->
                <div class="mb-5 bg-slate-700/50 p-3 rounded-md shadow-lg border border-slate-600">
                    <h4 class="text-sm font-medium text-slate-200 mb-3 flex items-center justify-between">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#3498DB]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                            </svg>
                            Size
                        </div>
                        <div class="text-xs text-slate-400 font-normal">
                            {{ $width }} × {{ $height }} px
                        </div>
                    </h4>
                    
                    <!-- Size Adjustment Step - Changed to Slider -->
                    <div class="mb-4 pt-3 border-t border-slate-500">
                        <div class="flex justify-between items-center mb-1">
                            <label for="resizeStepSlider" class="text-xs text-slate-300">Size Increment:</label>
                            <span class="text-xs font-medium text-slate-200">{{ $resizeStep }}px</span>
                        </div>
                        <input 
                            id="resizeStepSlider" 
                            type="range" 
                            wire:model.live="resizeStep" 
                            min="1" max="50" step="1" 
                            class="w-full h-1.5 bg-slate-500 rounded-full appearance-none cursor-pointer accent-[#3498DB]/80 {{ $selectedLayer ? '' : 'opacity-50 cursor-not-allowed' }}"
                            {{ $selectedLayer ? '' : 'disabled' }}
                        >
                        <div class="flex justify-between text-xs text-slate-400 mt-1">
                            <span>1px</span>
                            <span>25px</span>
                            <span>50px</span>
                        </div>
                    </div>
                    
                    <!-- Aspect Ratio Toggle - Moved to top for better visibility -->
                    <div class="flex items-center mb-4 p-2 rounded-md bg-slate-600 border border-slate-500">
                        <input 
                            id="preserve-aspect-ratio" 
                            type="checkbox" 
                            wire:model.live="preserveAspectRatio"
                            class="w-4 h-4 text-[#3498DB] border-slate-400 rounded focus:ring focus:ring-[#3498DB]/30"
                        >
                        <label for="preserve-aspect-ratio" class="ml-2 text-xs font-medium {{ $preserveAspectRatio ? 'text-[#3498DB]' : 'text-slate-300' }}">
                            Preserve aspect ratio 
                            <span class="inline-block ml-1 px-1.5 py-0.5 text-xs font-medium rounded-full {{ $preserveAspectRatio ? 'bg-[#3498DB]/20 text-[#3498DB]' : 'bg-slate-500 text-slate-200' }}">
                                {{ $preserveAspectRatio ? 'ON' : 'OFF' }}
                            </span>
                        </label>
                    </div>
                    
                    <!-- Size Adjustment Buttons - Updated with labels and better visuals -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="space-y-2">
                            <label class="block text-xs font-medium text-slate-300 text-center">Width</label>
                            <div class="flex items-center space-x-2">
                                <button wire:click="adjustSize('decrease-width')" class="bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md p-2 flex-1 flex items-center justify-center transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <button wire:click="adjustSize('increase-width')" class="bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md p-2 flex-1 flex items-center justify-center transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-medium text-slate-300 text-center">Height</label>
                            <div class="flex items-center space-x-2">
                                <button wire:click="adjustSize('decrease-height')" class="bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md p-2 flex-1 flex items-center justify-center transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300 rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <button wire:click="adjustSize('increase-height')" class="bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md p-2 flex-1 flex items-center justify-center transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300 rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Direct Size Inputs - Enhanced with visual cues -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="bg-slate-600 p-2 rounded-md text-center border border-slate-500 {{ !$preserveAspectRatio ? 'ring-2 ring-[#3498DB]/20' : '' }}">
                            <label class="block text-xs text-slate-300 mb-1">Width</label>
                            <div class="relative">
                                <input
                                    type="number"
                                    wire:model.lazy="width"
                                    wire:change="$set('size_control_used', true); updateTransform()"
                                    class="w-full bg-slate-500 border-slate-400 text-slate-200 placeholder-slate-400 text-sm px-2 py-1 text-center rounded-md focus:ring-[#3498DB] focus:border-[#3498DB]"
                                    min="10"
                                />
                                <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-xs text-slate-400">px</span>
                            </div>
                        </div>
                        <div class="bg-slate-600 p-2 rounded-md text-center border border-slate-500 {{ !$preserveAspectRatio ? 'ring-2 ring-[#3498DB]/20' : '' }}">
                            <label class="block text-xs text-slate-300 mb-1">Height</label>
                            <div class="relative">
                                <input
                                    type="number"
                                    wire:model.lazy="height"
                                    wire:change="$set('size_control_used', true); updateTransform()"
                                    class="w-full bg-slate-500 border-slate-400 text-slate-200 placeholder-slate-400 text-sm px-2 py-1 text-center rounded-md focus:ring-[#3498DB] focus:border-[#3498DB]"
                                    min="10"
                                />
                                <span class="absolute right-2 top-1/2 transform -translate-y-1/2 text-xs text-slate-400">px</span>
                            </div>
                        </div>
                    </div>
                    
                    <button 
                        wire:click="resetSize" 
                        class="w-full text-xs text-center font-medium text-slate-300 bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md py-2 px-2 transition-colors flex items-center justify-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset Size
                    </button>
                </div>
                
                <!-- Rotation Controls -->
                <div class="mb-4 bg-slate-700/50 p-3 rounded-md shadow-lg border border-slate-600">
                    <h4 class="text-sm font-medium text-slate-200 mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#3498DB]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Rotation
                    </h4>
                    
                    <!-- Main Rotation Slider & Input -->
                    <div class="mb-3">
                        <input 
                            type="range" 
                            wire:model.live="rotation" 
                            wire:change="updateTransform"
                            min="-180" 
                            max="180" 
                            step="0.1" 
                            class="w-full h-2 bg-slate-500 rounded-full appearance-none cursor-pointer accent-[#3498DB]"
                        >
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs text-slate-400">-180°</span>
                            <!-- Input and Fine Adjustment Buttons Group -->
                            <div class="flex items-center space-x-2">
                                 <button wire:click="rotateBy('decrease')" class="p-1 bg-slate-600 hover:bg-slate-500 rounded disabled:opacity-50" title="Rotate -{{ number_format($rotationIncrement, 1) }}°" {{ $selectedLayer ? '' : 'disabled' }}>
                                     <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300">
                                        <path d="M4 7H15C16.8692 7 17.8038 7 18.5 7.40193C18.9561 7.66523 19.3348 8.04394 19.5981 8.49999C20 9.19615 20 10.1308 20 12C20 13.8692 20 14.8038 19.5981 15.5C19.3348 15.9561 18.9561 16.3348 18.5 16.5981C17.8038 17 16.8692 17 15 17H8M4 7L7 4M4 7L7 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                     </svg>
                                 </button>
                                 <input 
                                     type="number" 
                                     wire:model.lazy="rotation"
                                     wire:change="updateTransform()"
                                     class="w-16 bg-slate-500 border-slate-400 text-slate-200 placeholder-slate-400 text-sm px-1 py-0.5 text-center rounded-md disabled:opacity-50 disabled:cursor-not-allowed focus:ring-[#3498DB] focus:border-[#3498DB]"
                                     step="0.1" 
                                     min="-180" 
                                     max="180"
                                     {{ $selectedLayer ? '' : 'disabled' }}
                                 >
                                 <button wire:click="rotateBy('increase')" class="p-1 bg-slate-600 hover:bg-slate-500 rounded disabled:opacity-50" title="Rotate +{{ number_format($rotationIncrement, 1) }}°" {{ $selectedLayer ? '' : 'disabled' }}>
                                      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-300">
                                         <path d="M20 7H9.00001C7.13077 7 6.19615 7 5.5 7.40193C5.04395 7.66523 4.66524 8.04394 4.40193 8.49999C4 9.19615 4 10.1308 4 12C4 13.8692 4 14.8038 4.40192 15.5C4.66523 15.9561 5.04394 16.3348 5.5 16.5981C6.19615 17 7.13077 17 9 17H16M20 7L17 4M20 7L17 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                      </svg>
                                 </button>
                            </div>
                            <span class="text-xs text-slate-400">180°</span>
                        </div>
                    </div>

                    <!-- Rotation Increment Controls -->
                    <div class="mt-4 mb-4 pt-3 border-t border-slate-500" x-data="{ incrementValue: @entangle('rotationIncrement').live }">
                        <div class="flex justify-between items-center mb-1">
                             <label for="rotationIncrementInput" class="text-xs text-slate-300">Increment Step:</label>
                             <input
                                 id="rotationIncrementInput"
                                 type="number"
                                 x-model.number.lazy="incrementValue" 
                                 @change="$wire.set('rotationIncrement', Math.max(0, Math.min(45, parseFloat($event.target.value) || 0)))" 
                                 class="w-16 bg-slate-600 border-slate-500 text-slate-200 placeholder-slate-400 text-xs px-1 py-0.5 text-center rounded-md disabled:opacity-50 disabled:cursor-not-allowed focus:ring-[#3498DB] focus:border-[#3498DB]"
                                 step="1" min="0" max="45"
                                 {{ $selectedLayer ? '' : 'disabled' }}
                             >
                         </div>
                        <input 
                            id="rotationIncrementSlider" 
                            type="range" 
                            x-model.number.debounce.10ms="incrementValue" 
                            @input="$wire.set('rotationIncrement', Math.max(0, Math.min(45, parseFloat($event.target.value) || 0)))" 
                            min="0" max="45" step="1" 
                            class="w-full h-1.5 bg-slate-500 rounded-full appearance-none cursor-pointer accent-[#3498DB]/80 disabled:opacity-50 disabled:cursor-not-allowed"
                             {{ $selectedLayer ? '' : 'disabled' }}
                        >
                        <div class="flex justify-between text-xs text-slate-400 mt-1">
                            <span>0°</span>
                            <span>22.5°</span>
                            <span>45°</span>
                        </div>
                    </div>
                    
                    <button 
                        wire:click="resetRotation" 
                        class="w-full text-xs text-center font-medium text-slate-300 bg-slate-600 hover:bg-slate-500 active:bg-slate-400 rounded-md py-2 px-2 transition-colors flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                         {{ $selectedLayer ? '' : 'disabled' }}
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
                    <p class="text-slate-400 text-sm">Select a layer from the dropdown to transform it</p>
                </div>
            </div>
        @endif
    </div>
    <style>
        .transform-panel-scroll-container::-webkit-scrollbar {
            width: 8px;
        }
        .transform-panel-scroll-container::-webkit-scrollbar-track {
            background-color: #1e293b; /* slate-800 */
        }
        .transform-panel-scroll-container::-webkit-scrollbar-thumb {
            background-color: #94a3b8; /* slate-400 */
            border-radius: 4px;
        }
        /* For Firefox */
        .transform-panel-scroll-container {
            scrollbar-width: thin;
            scrollbar-color: #94a3b8 #1e293b; /* thumb track */
        }
    </style>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('TRANSFORM PANEL: Script loaded');
        
        function updateDimensionsAndPosition(component, obj) {
            if (!obj) return;

            console.log('TRANSFORM PANEL: Updating state for object:', obj.id || 'unknown');

            // Update Position
            const newPosX = Math.round(obj.left || 0);
            const newPosY = Math.round(obj.top || 0);
            component.set('positionX', newPosX);
            component.set('positionY', newPosY);
            console.log(`TRANSFORM PANEL: Set position to X: ${newPosX}, Y: ${newPosY}`);

            // Update Rotation
            if (obj.angle !== undefined) {
                 const newRotation = parseFloat(obj.angle.toFixed(1));
                 component.set('rotation', newRotation);
                 console.log(`TRANSFORM PANEL: Set rotation to: ${newRotation}°`);
            }

            // Update Dimensions (Using Scaled Values)
            let newWidth = component.get('width'); // Default to current value
            let newHeight = component.get('height'); // Default to current value

            if (obj.width !== undefined && obj.scaleX !== undefined) {
                newWidth = Math.round(obj.width * obj.scaleX);
                component.set('width', newWidth);
                 console.log(`TRANSFORM PANEL: Set width to: ${newWidth} (Original: ${obj.width}, ScaleX: ${obj.scaleX})`);
            } else {
                 console.warn('TRANSFORM PANEL: Width properties missing', {width: obj.width, scaleX: obj.scaleX});
            }

            if (obj.height !== undefined && obj.scaleY !== undefined) {
                newHeight = Math.round(obj.height * obj.scaleY);
                component.set('height', newHeight);
                 console.log(`TRANSFORM PANEL: Set height to: ${newHeight} (Original: ${obj.height}, ScaleY: ${obj.scaleY})`);
            } else {
                 console.warn('TRANSFORM PANEL: Height properties missing', {height: obj.height, scaleY: obj.scaleY});
            }

             // Store original dimensions on selection/modification for aspect ratio
            if (obj.width && obj.height) {
                component.set('originalWidth', obj.width);
                component.set('originalHeight', obj.height);
                console.log(`TRANSFORM PANEL: Stored original dimensions - W: ${obj.width}, H: ${obj.height}`);
            }
        }

        window.addEventListener('fabricjs:object-selected', function(event) {
            const obj = event.detail;
            console.log('TRANSFORM PANEL: fabricjs:object-selected event received', obj);
            if (obj) {
                updateDimensionsAndPosition(@this, obj);
                // Indicate that the selection came from canvas, not panel controls
                // Prevents aspect ratio lock from triggering incorrectly on initial selection
                @this.set('size_control_used', false); 
            }
        });
        
        window.addEventListener('fabricjs:object-modified', function(event) {
            const obj = event.detail;
            console.log('TRANSFORM PANEL: fabricjs:object-modified event received', obj);
            if (obj) {
                updateDimensionsAndPosition(@this, obj);
                 // Indicate that the modification came from canvas, not panel controls
                @this.set('size_control_used', false);
            }
        });

        window.addEventListener('fabricjs:object-moving', function(event) {
            const obj = event.detail;
            if (obj) {
                const newPosX = Math.round(obj.left || 0);
                const newPosY = Math.round(obj.top || 0);
                // Use .live here for smoother updates during drag if needed, 
                // but standard set might be sufficient and less chatty.
                @this.set('positionX', newPosX);
                @this.set('positionY', newPosY);
                console.log(`TRANSFORM PANEL: Object moving - X: ${newPosX}, Y: ${newPosY}`);
            }
        });

        window.addEventListener('fabricjs:object-scaling', function(event) {
            const obj = event.detail;
            if (obj) {
                const currentWidth = Math.round(obj.width * obj.scaleX);
                const currentHeight = Math.round(obj.height * obj.scaleY);
                // Use .live for smoother updates during scaling
                @this.livewire.set('width', currentWidth);
                @this.livewire.set('height', currentHeight);

                // Update position as well, as origin might change during scaling
                const newPosX = Math.round(obj.left || 0);
                const newPosY = Math.round(obj.top || 0);
                 @this.livewire.set('positionX', newPosX);
                 @this.livewire.set('positionY', newPosY);

                console.log(`TRANSFORM PANEL: Object scaling - W: ${currentWidth}, H: ${currentHeight}, X: ${newPosX}, Y: ${newPosY}`);
            }
        });

        window.addEventListener('fabricjs:object-rotating', function(event) {
            const obj = event.detail;
            if (obj && obj.angle !== undefined) {
                const newRotation = parseFloat(obj.angle.toFixed(1));
                // Use .live for smoother updates during rotation
                @this.livewire.set('rotation', newRotation);
                console.log(`TRANSFORM PANEL: Object rotating - Angle: ${newRotation}°`);
            }
        });

        Livewire.on('preserveAspectRatioChanged', function(isPreserved) {
            console.log('TRANSFORM PANEL: Aspect ratio preservation changed:', isPreserved);
            const widthInputDiv = document.querySelector('input[wire\\:model\\.lazy="width"]')?.closest('.border');
            const heightInputDiv = document.querySelector('input[wire\\:model\\.lazy="height"]')?.closest('.border');

            if (widthInputDiv && heightInputDiv) {
                const ringClass = 'ring-2';
                const ringColorClass = 'ring-[#3498DB]/20';
                if (isPreserved) {
                    widthInputDiv.classList.remove(ringClass, ringColorClass);
                    heightInputDiv.classList.remove(ringClass, ringColorClass);
                } else {
                    widthInputDiv.classList.add(ringClass, ringColorClass);
                    heightInputDiv.classList.add(ringClass, ringColorClass);
                }
            }
        });
    });
</script>
