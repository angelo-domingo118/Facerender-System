<div class="h-full flex flex-col bg-slate-800/60">
    <script>
        // Debug code to monitor Livewire events
        document.addEventListener('livewire:initialized', () => {
            console.log('Feature adjustment panel initialized');
            
            // Listen for layers-updated event
            Livewire.on('layers-updated', (layers) => {
                console.log('Layers updated received in panel:', layers);
                
                // Check first layer for adjustments
                if (layers && layers.length > 0) {
                    console.log('First layer:', layers[0]);
                    console.log('Has adjustments?', !!layers[0].adjustments);
                    if (layers[0].adjustments) {
                        console.log('Adjustments:', layers[0].adjustments);
                        console.log('Contrast value:', layers[0].adjustments.contrast);
                    }
                }
            });
            
            // Also monitor when layer is selected
            Livewire.on('layer-selected', (layer) => {
                console.log('Layer selected:', layer);
                if (layer && layer.adjustments) {
                    console.log('Selected layer adjustments:', layer.adjustments);
                }
            });
        });
    </script>
    <div class="flex-1 overflow-y-auto feature-adjustment-panel-scroll-container">
        <!-- Layer Selector Dropdown -->
        <div class="sticky top-0 z-10 p-3 border-b border-slate-600 bg-slate-700/80 backdrop-blur-sm shadow-md">
            <label for="layer-adjustment-selector" class="block text-xs font-medium text-gray-300 mb-1">Select Layer</label>
            <select 
                id="layer-adjustment-selector" 
                wire:model.live="selectedLayerId" 
                wire:change="handleLayerChange"
                class="w-full rounded-md bg-slate-600 border-slate-500 text-gray-200 shadow-sm focus:border-[#3498DB] focus:ring focus:ring-[#3498DB]/30 text-sm"
            >
                <option value="">-- Select a layer --</option>
                @foreach($layers as $layer)
                    <option value="{{ $layer['id'] }}">{{ $layer['name'] }}</option>
                @endforeach
            </select>
        </div>
        
        @if($selectedLayer)
            <div class="p-3">
                <!-- Basic Adjustments -->
                <div class="mb-4 bg-slate-700/50 p-3 rounded-lg border border-slate-600 shadow-lg">
                    <h4 class="text-sm font-medium text-gray-200 mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#3498DB]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Basic Adjustments
                    </h4>
                    
                    <!-- Contrast Control -->
                    <div class="mb-3">
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-xs font-medium text-gray-300">Contrast</label>
                            <span class="text-xs bg-slate-600 text-gray-200 px-2 py-1 rounded-md font-medium">{{ $contrast < 0 ? $contrast : '+' . $contrast }}</span>
                        </div>
                        <input 
                            type="range" 
                            wire:model.live="contrast" 
                            min="-100" 
                            max="100" 
                            step="1" 
                            class="w-full h-2 bg-slate-500 rounded-full appearance-none cursor-pointer accent-[#3498DB]"
                        >
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>Less</span>
                            <span class="font-medium">Normal</span>
                            <span>More</span>
                        </div>
                    </div>
                    
                    <!-- Saturation Control -->
                    <div class="mb-3">
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-xs font-medium text-gray-300">Saturation</label>
                            <span class="text-xs bg-slate-600 text-gray-200 px-2 py-1 rounded-md font-medium">{{ $saturation < 0 ? $saturation : '+' . $saturation }}</span>
                        </div>
                        <input 
                            type="range" 
                            wire:model.live="saturation" 
                            min="-100" 
                            max="100" 
                            step="1" 
                            class="w-full h-2 bg-slate-500 rounded-full appearance-none cursor-pointer accent-[#3498DB]"
                        >
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>Less</span>
                            <span class="font-medium">Normal</span>
                            <span>More</span>
                        </div>
                    </div>

                    <!-- Sharpness Control -->
                    <div class="mb-3">
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-xs font-medium text-gray-300">Sharpness</label>
                            <span class="text-xs bg-slate-600 text-gray-200 px-2 py-1 rounded-md font-medium">{{ $sharpness }}</span>
                        </div>
                        <input 
                            type="range" 
                            wire:model.live="sharpness" 
                            min="0" 
                            max="100" 
                            step="1" 
                            class="w-full h-2 bg-slate-500 rounded-full appearance-none cursor-pointer accent-[#3498DB]"
                        >
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>None</span>
                            <span class="font-medium"></span>
                            <span>Maximum</span>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Adjustments -->
                <div class="mb-4 bg-slate-700/50 p-3 rounded-lg border border-slate-600 shadow-lg">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#3498DB]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Advanced Adjustments
                        </h4>
                    </div>
                    
                        <!-- Edge Feathering Control -->
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-1">
                                <label class="text-xs font-medium text-gray-300">Edge Feathering</label>
                                <span class="text-xs bg-slate-600 text-gray-200 px-2 py-1 rounded-md font-medium">{{ $feathering }}%</span>
                            </div>
                            <input 
                                type="range" 
                                wire:model.live="feathering" 
                                min="0" 
                                max="100" 
                                step="1" 
                                class="w-full h-2 bg-slate-500 rounded-full appearance-none cursor-pointer accent-[#3498DB]"
                            >
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>None</span>
                            <span class="font-medium">Medium</span>
                            <span>Maximum</span>
                        </div>
                        
                        <!-- Feathering Curve Control - always visible -->
                        <div class="mt-2 border-t pt-2 border-slate-600">
                            <div class="flex justify-between items-center mb-1">
                                <label class="text-xs font-medium text-gray-300">Edge Softness</label>
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5 text-gray-400 mr-1">
                                        <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                                    </svg>
                                    <span class="text-xs bg-slate-600 text-gray-200 px-2 py-1 rounded-md font-medium">
                                        {{ $featheringCurve < 3 ? 'Soft' : ($featheringCurve > 3 ? 'Sharp' : 'Medium') }}
                                    </span>
                                </div>
                            </div>
                            <input 
                                type="range" 
                                wire:model.live="featheringCurve" 
                                min="1" 
                                max="5" 
                                step="1" 
                                class="w-full h-2 bg-slate-500 rounded-full appearance-none cursor-pointer accent-[#3498DB]"
                            >
                            <div class="flex justify-between text-xs text-gray-400 mt-1">
                                <span>Soft Edge</span>
                                <span class="font-medium">Medium</span>
                                <span>Sharp Edge</span>
                            </div>
                        </div>
                        </div>
                        
                        <!-- Skin Tone Matching -->
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-xs font-medium text-gray-300">Skin Tone</label>
                                <span class="text-xs bg-slate-600 text-gray-200 px-2 py-1 rounded-md font-medium">{{ $skinToneLabel }}</span>
                            </div>
                            
                        <!-- Skin Tone Gradient Slider with visible indicator -->
                            <div class="mb-3 relative">
                            <div class="h-6 rounded-lg w-full" style="background: linear-gradient(to right, #FFE4C4, #DEB887, #CD853F, #8B4513, #654321, #3B2F2F, #2F1F1F);"></div>
                            
                            <!-- Custom Slider Indicator -->
                            <div class="absolute top-0 h-6 pointer-events-none" style="left: calc({{ $skinTone }}% - 6px);">
                                <div class="h-full w-4 border-2 border-slate-300 rounded-sm shadow-md"></div>
                                <div class="absolute -bottom-1.5 left-0 right-0 flex justify-center">
                                    <div class="w-0 h-0 border-l-[4px] border-l-transparent border-r-[4px] border-r-transparent border-t-[5px] border-t-slate-300 shadow-sm"></div>
                                </div>
                            </div>
                            
                                <input 
                                    type="range" 
                                    wire:model.live="skinTone" 
                                    min="0" 
                                    max="100" 
                                    step="1"
                                    class="absolute top-0 w-full h-6 opacity-0 cursor-pointer"
                                >
                            
                            <!-- Slider markers -->
                            <div class="w-full flex justify-between px-1 mt-1">
                                <div class="flex flex-col items-center">
                                    <div class="h-2 w-0.5 bg-gray-400"></div>
                                    <span class="text-[9px] text-gray-400">Light</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="h-2 w-0.5 bg-gray-400"></div>
                                    <span class="text-[9px] text-gray-400">Natural</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="h-2 w-0.5 bg-gray-400"></div>
                                    <span class="text-[9px] text-gray-400">Medium</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="h-2 w-0.5 bg-gray-400"></div>
                                    <span class="text-[9px] text-gray-400">Dark</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Skin Tone Color Grid -->
                        <div class="grid grid-cols-6 gap-1.5 mb-2 mt-3">
                            <button 
                                wire:click="setSkinToneExact(5)" 
                                class="h-8 rounded-md border hover:ring-1 hover:ring-[#3498DB] {{ $skinTone == 5 ? 'ring-2 ring-[#3498DB]' : '' }}" 
                                style="background-color: #FFEFD5;"
                                title="Very Light (5)"
                            ></button>
                            <button 
                                wire:click="setSkinToneExact(15)" 
                                class="h-8 rounded-md border hover:ring-1 hover:ring-[#3498DB] {{ $skinTone == 15 ? 'ring-2 ring-[#3498DB]' : '' }}" 
                                style="background-color: #FFE4C4;"
                                title="Light (15)"
                            ></button>
                            <button 
                                wire:click="setSkinToneExact(25)" 
                                class="h-8 rounded-md border hover:ring-1 hover:ring-[#3498DB] {{ $skinTone == 25 ? 'ring-2 ring-[#3498DB]' : '' }}" 
                                style="background-color: #F5DEB3;"
                                title="Light Medium (25)"
                            ></button>
                            <button 
                                wire:click="setSkinToneExact(35)" 
                                class="h-8 rounded-md border hover:ring-1 hover:ring-[#3498DB] {{ $skinTone == 35 ? 'ring-2 ring-[#3498DB]' : '' }}" 
                                style="background-color: #DEB887;"
                                title="Medium Light (35)"
                            ></button>
                            <button 
                                wire:click="setSkinToneExact(45)" 
                                class="h-8 rounded-md border hover:ring-1 hover:ring-[#3498DB] {{ $skinTone == 45 ? 'ring-2 ring-[#3498DB]' : '' }}" 
                                style="background-color: #D2B48C;"
                                title="Medium (45)"
                            ></button>
                            <button 
                                wire:click="setSkinToneExact(55)" 
                                class="h-8 rounded-md border hover:ring-1 hover:ring-[#3498DB] {{ $skinTone == 55 ? 'ring-2 ring-[#3498DB]' : '' }}" 
                                style="background-color: #CD853F;"
                                title="Medium Tan (55)"
                            ></button>
                            <button 
                                wire:click="setSkinToneExact(65)" 
                                class="h-8 rounded-md border hover:ring-1 hover:ring-[#3498DB] {{ $skinTone == 65 ? 'ring-2 ring-[#3498DB]' : '' }}" 
                                style="background-color: #A0522D;"
                                title="Medium Dark (65)"
                            ></button>
                            <button 
                                wire:click="setSkinToneExact(70)" 
                                class="h-8 rounded-md border hover:ring-1 hover:ring-[#3498DB] {{ $skinTone == 70 ? 'ring-2 ring-[#3498DB]' : '' }}" 
                                style="background-color: #8B4513;"
                                title="Dark Medium (70)"
                            ></button>
                            <button 
                                wire:click="setSkinToneExact(75)" 
                                class="h-8 rounded-md border hover:ring-1 hover:ring-[#3498DB] {{ $skinTone == 75 ? 'ring-2 ring-[#3498DB]' : '' }}" 
                                style="background-color: #6B4423;"
                                title="Dark (75)"
                            ></button>
                            <button 
                                wire:click="setSkinToneExact(80)" 
                                class="h-8 rounded-md border hover:ring-1 hover:ring-[#3498DB] {{ $skinTone == 80 ? 'ring-2 ring-[#3498DB]' : '' }}" 
                                style="background-color: #5C4033;"
                                title="Dark Brown (80)"
                            ></button>
                            <button 
                                wire:click="setSkinToneExact(90)" 
                                class="h-8 rounded-md border hover:ring-1 hover:ring-[#3498DB] {{ $skinTone == 90 ? 'ring-2 ring-[#3498DB]' : '' }}" 
                                style="background-color: #3B2F2F;"
                                title="Very Dark (90)"
                            ></button>
                            <button 
                                wire:click="setSkinToneExact(95)" 
                                class="h-8 rounded-md border hover:ring-1 hover:ring-[#3498DB] {{ $skinTone == 95 ? 'ring-2 ring-[#3498DB]' : '' }}" 
                                style="background-color: #2F1F1F;"
                                title="Deep (95)"
                            ></button>
                            </div>

                            <!-- Preset Buttons -->
                        <div class="flex flex-wrap gap-2 justify-center mb-1">
                                <button 
                                    wire:click="setSkinTone('light')"
                                class="px-2 py-1 text-xs rounded-md border transition-all duration-150 hover:bg-opacity-90 flex items-center space-x-1 bg-[#FFE4C4] text-gray-700 {{ $skinToneLabel === 'Light' ? 'ring-1 ring-[#3498DB]' : '' }}"
                                >
                                    <span>Light</span>
                                </button>
                                <button 
                                    wire:click="setSkinTone('natural')"
                                class="px-2 py-1 text-xs rounded-md border transition-all duration-150 hover:bg-opacity-90 flex items-center space-x-1 bg-[#CD853F] text-white {{ $skinToneLabel === 'Natural' ? 'ring-1 ring-[#3498DB]' : '' }}"
                                >
                                    <span>Natural</span>
                                </button>
                                <button 
                                    wire:click="setSkinTone('medium')"
                                class="px-2 py-1 text-xs rounded-md border transition-all duration-150 hover:bg-opacity-90 flex items-center space-x-1 bg-[#8B4513] text-white {{ $skinToneLabel === 'Medium' ? 'ring-1 ring-[#3498DB]' : '' }}"
                                >
                                    <span>Medium</span>
                                </button>
                                <button 
                                    wire:click="setSkinTone('dark')"
                                class="px-2 py-1 text-xs rounded-md border transition-all duration-150 hover:bg-opacity-90 flex items-center space-x-1 bg-[#2F1F1F] text-white {{ $skinToneLabel === 'Dark' ? 'ring-1 ring-[#3498DB]' : '' }}"
                                >
                                    <span>Dark</span>
                                </button>
                            </div>
                        </div>
                </div>
                
                <!-- Reset Button -->
                <button 
                    wire:click="resetAllAdjustments"
                    class="w-full py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm font-medium transition-colors duration-150 flex items-center justify-center"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset All Adjustments
                </button>
            </div>
        @else
            <div class="flex items-center justify-center p-6">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                    </svg>
                    <p class="text-gray-500 text-sm">Select a layer from the dropdown to adjust it</p>
                </div>
            </div>
        @endif
    </div>
    <style>
        .feature-adjustment-panel-scroll-container::-webkit-scrollbar {
            width: 8px;
        }
        .feature-adjustment-panel-scroll-container::-webkit-scrollbar-track {
            background-color: #1e293b; /* slate-800 */
        }
        .feature-adjustment-panel-scroll-container::-webkit-scrollbar-thumb {
            background-color: #94a3b8; /* slate-400 */
            border-radius: 4px;
        }
        /* For Firefox */
        .feature-adjustment-panel-scroll-container {
            scrollbar-width: thin;
            scrollbar-color: #94a3b8 #1e293b; /* thumb track */
        }
    </style>
</div>
