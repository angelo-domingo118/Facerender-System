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
                    
                    <!-- Direct Position Inputs -->
                    <div class="grid grid-cols-2 gap-3 mt-3 mb-3">
                        <div class="bg-gray-50 p-2 rounded-md text-center border border-gray-200">
                            <label class="block text-xs text-gray-500 mb-1">X</label>
                            <input
                                type="number"
                                wire:model.lazy="positionX"
                                wire:change="updateTransform()"
                                class="w-full bg-white border border-gray-300 text-sm px-2 py-1 text-center rounded-md"
                            />
                        </div>
                        <div class="bg-gray-50 p-2 rounded-md text-center border border-gray-200">
                            <label class="block text-xs text-gray-500 mb-1">Y</label>
                            <input
                                type="number"
                                wire:model.lazy="positionY"
                                wire:change="updateTransform()"
                                class="w-full bg-white border border-gray-300 text-sm px-2 py-1 text-center rounded-md"
                            />
                        </div>
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
                    
                    <!-- Main Rotation Slider & Input -->
                    <div class="mb-3">
                        <input 
                            type="range" 
                            wire:model.live="rotation" 
                            wire:change="updateTransform"
                            min="-180" 
                            max="180" 
                            step="0.1" 
                            class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-[#2C3E50]"
                        >
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs text-gray-500">-180°</span>
                            <!-- Input and Fine Adjustment Buttons Group -->
                            <div class="flex items-center space-x-2">
                                 <button wire:click="rotateBy('decrease')" class="p-1 bg-gray-100 hover:bg-gray-200 rounded disabled:opacity-50" title="Rotate -{{ number_format($rotationIncrement, 1) }}°" {{ $selectedLayer ? '' : 'disabled' }}>
                                     <!-- SVG for rotate counter-clockwise (Using Redo Icon - Swapped) -->
                                     <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600">
                                        <path d="M4 7H15C16.8692 7 17.8038 7 18.5 7.40193C18.9561 7.66523 19.3348 8.04394 19.5981 8.49999C20 9.19615 20 10.1308 20 12C20 13.8692 20 14.8038 19.5981 15.5C19.3348 15.9561 18.9561 16.3348 18.5 16.5981C17.8038 17 16.8692 17 15 17H8M4 7L7 4M4 7L7 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                     </svg>
                                 </button>
                                 <input 
                                     type="number" 
                                     wire:model.lazy="rotation"
                                     wire:change="updateTransform()"
                                     class="w-16 bg-white border border-gray-300 text-sm px-1 py-0.5 text-center rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
                                     step="0.1" 
                                     min="-180" 
                                     max="180"
                                     {{ $selectedLayer ? '' : 'disabled' }}
                                 >
                                 <button wire:click="rotateBy('increase')" class="p-1 bg-gray-100 hover:bg-gray-200 rounded disabled:opacity-50" title="Rotate +{{ number_format($rotationIncrement, 1) }}°" {{ $selectedLayer ? '' : 'disabled' }}>
                                      <!-- SVG for rotate clockwise (Using Undo Icon - Swapped) -->
                                      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600">
                                         <path d="M20 7H9.00001C7.13077 7 6.19615 7 5.5 7.40193C5.04395 7.66523 4.66524 8.04394 4.40193 8.49999C4 9.19615 4 10.1308 4 12C4 13.8692 4 14.8038 4.40192 15.5C4.66523 15.9561 5.04394 16.3348 5.5 16.5981C6.19615 17 7.13077 17 9 17H16M20 7L17 4M20 7L17 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                      </svg>
                                 </button>
                            </div>
                            <span class="text-xs text-gray-500">180°</span>
                        </div>
                    </div>

                    <!-- Rotation Increment Controls -->
                    <div class="mt-4 mb-4 pt-3 border-t border-gray-100" x-data="{ incrementValue: @entangle('rotationIncrement').live }">
                        <div class="flex justify-between items-center mb-1">
                             <label for="rotationIncrementInput" class="text-xs text-gray-500">Increment Step:</label>
                             <input
                                 id="rotationIncrementInput"
                                 type="number"
                                 x-model.number.lazy="incrementValue" 
                                 @change="$wire.set('rotationIncrement', Math.max(0, Math.min(45, parseFloat($event.target.value) || 0)))" 
                                 class="w-16 bg-gray-50 border border-gray-200 text-xs px-1 py-0.5 text-center rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
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
                            class="w-full h-1.5 bg-gray-200 rounded-full appearance-none cursor-pointer accent-[#2C3E50]/50 disabled:opacity-50 disabled:cursor-not-allowed"
                             {{ $selectedLayer ? '' : 'disabled' }}
                        >
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>0°</span>
                            <span>22.5°</span>
                            <span>45°</span>
                        </div>
                    </div>
                    
                    <button 
                        wire:click="resetRotation" 
                        class="w-full text-xs text-center font-medium text-gray-500 bg-gray-100 hover:bg-gray-200 active:bg-gray-300 rounded-md py-2 px-2 transition-colors flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
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
                    <p class="text-gray-500 text-sm">Select a layer from the dropdown to transform it</p>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('TRANSFORM PANEL: Script loaded');
        
        // Simple direct approach - copy exactly what works in layer-panel.blade.php
        window.addEventListener('fabricjs:object-selected', function(event) {
            const obj = event.detail;
            console.log('TRANSFORM PANEL: fabricjs:object-selected event received', event);
            
            if (obj) {
                // Direct update using the same approach as layer-panel
                @this.set('positionX', Math.round(obj.left));
                @this.set('positionY', Math.round(obj.top));
                
                // Also handle rotation if available
                if (obj.angle !== undefined) {
                    @this.set('rotation', parseFloat(obj.angle.toFixed(1)));
                }
                
                // Log position for debugging
                console.log('TRANSFORM PANEL: Selected object position:', { x: obj.left, y: obj.top });
            }
        });
        
        window.addEventListener('fabricjs:object-modified', function(event) {
            const obj = event.detail;
            console.log('TRANSFORM PANEL: fabricjs:object-modified event received', event);
            
            if (obj) {
                // Direct update using the same approach as layer-panel
                @this.set('positionX', Math.round(obj.left));
                @this.set('positionY', Math.round(obj.top));
                
                // Also handle rotation if available
                if (obj.angle !== undefined) {
                    @this.set('rotation', parseFloat(obj.angle.toFixed(1)));
                }
                
                // Log position for debugging
                console.log('TRANSFORM PANEL: Object position updated:', { x: obj.left, y: obj.top });
            }
        });
        
        // Also listen for moving events to update during dragging
        window.addEventListener('fabricjs:object-moving', function(event) {
            const obj = event.detail;
            
            if (obj) {
                // Direct update of position during dragging
                @this.set('positionX', Math.round(obj.left));
                @this.set('positionY', Math.round(obj.top));
                
                console.log('TRANSFORM PANEL: Object position during move:', { x: obj.left, y: obj.top });
            }
        });
        
        // Listen for rotation events
        window.addEventListener('fabricjs:object-rotating', function(event) {
            const obj = event.detail;
            
            if (obj && obj.angle !== undefined) {
                @this.set('rotation', parseFloat(obj.angle.toFixed(1)));
                console.log('TRANSFORM PANEL: Object rotation during rotate:', { angle: obj.angle });
            }
        });
    });
</script>
