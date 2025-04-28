<div class="h-full flex flex-col bg-gray-50">
    <div class="flex-1 overflow-y-auto">
        <!-- Layer Selector Dropdown -->
        <div class="sticky top-0 z-10 p-3 border-b border-gray-200 bg-white shadow-sm">
            <label for="layer-adjustment-selector" class="block text-xs font-medium text-gray-500 mb-1">Select Layer</label>
            <select 
                id="layer-adjustment-selector" 
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
                <!-- Basic Adjustments -->
                <div class="mb-4 bg-white p-3 rounded-lg border shadow-sm">
                    <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#2C3E50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                        Basic Adjustments
                    </h4>
                    
                    <!-- Contrast Control -->
                    <div class="mb-3">
                        <div class="flex justify-between items-center mb-1">
                            <label class="text-xs font-medium text-gray-500">Contrast</label>
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded-md font-medium">{{ $contrast < 0 ? $contrast : '+' . $contrast }}</span>
                        </div>
                        <input 
                            type="range" 
                            wire:model.live="contrast" 
                            min="-100" 
                            max="100" 
                            step="1" 
                            class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-[#2C3E50]"
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
                            <label class="text-xs font-medium text-gray-500">Saturation</label>
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded-md font-medium">{{ $saturation < 0 ? $saturation : '+' . $saturation }}</span>
                        </div>
                        <input 
                            type="range" 
                            wire:model.live="saturation" 
                            min="-100" 
                            max="100" 
                            step="1" 
                            class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-[#2C3E50]"
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
                            <label class="text-xs font-medium text-gray-500">Sharpness</label>
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded-md font-medium">{{ $sharpness }}</span>
                        </div>
                        <input 
                            type="range" 
                            wire:model.live="sharpness" 
                            min="0" 
                            max="100" 
                            step="1" 
                            class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-[#2C3E50]"
                        >
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>None</span>
                            <span class="font-medium"></span>
                            <span>Maximum</span>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Adjustments -->
                <div class="mb-4 bg-white p-3 rounded-lg border shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#2C3E50]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Advanced Adjustments
                        </h4>
                        <button 
                            wire:click="toggleAdvancedPanel"
                            class="bg-gray-100 hover:bg-gray-200 rounded p-1 flex items-center justify-center transition-colors duration-150"
                        >
                            <span class="text-xs p-1">{{ $showAdvanced ? 'Collapse' : 'Expand' }}</span>
                        </button>
                    </div>
                    
                    @if($showAdvanced)
                        <!-- Edge Feathering Control -->
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-1">
                                <label class="text-xs font-medium text-gray-500">Edge Feathering</label>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded-md font-medium">{{ $feathering }}%</span>
                            </div>
                            <input 
                                type="range" 
                                wire:model.live="feathering" 
                                min="0" 
                                max="100" 
                                step="1" 
                                class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer accent-[#2C3E50]"
                            >
                        </div>
                        
                        <!-- Skin Tone Matching -->
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-xs font-medium text-gray-500">Skin Tone</label>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded-md font-medium">{{ $skinToneLabel }}</span>
                            </div>
                            
                            <!-- Skin Tone Gradient Slider -->
                            <div class="mb-3 relative">
                                <div class="h-6 rounded-lg w-full mb-2" style="background: linear-gradient(to right, #FFE4C4, #DEB887, #CD853F, #8B4513, #654321, #3B2F2F, #2F1F1F);"></div>
                                <input 
                                    type="range" 
                                    wire:model.live="skinTone" 
                                    min="0" 
                                    max="100" 
                                    step="1"
                                    class="absolute top-0 w-full h-6 opacity-0 cursor-pointer"
                                >
                            </div>

                            <!-- Preset Buttons -->
                            <div class="flex flex-wrap gap-2">
                                <button 
                                    wire:click="setSkinTone('light')"
                                    class="px-2 py-1 text-xs rounded-md border transition-all duration-150 hover:border-[#2C3E50] flex items-center space-x-1 bg-[#FFE4C4] text-gray-700"
                                    title="Very Light"
                                >
                                    <div class="w-3 h-3 rounded-full bg-[#FFE4C4] border border-gray-300"></div>
                                    <span>Light</span>
                                </button>
                                <button 
                                    wire:click="setSkinTone('natural')"
                                    class="px-2 py-1 text-xs rounded-md border transition-all duration-150 hover:border-[#2C3E50] flex items-center space-x-1 bg-[#CD853F] text-white"
                                    title="Natural"
                                >
                                    <div class="w-3 h-3 rounded-full bg-[#CD853F] border border-gray-300"></div>
                                    <span>Natural</span>
                                </button>
                                <button 
                                    wire:click="setSkinTone('medium')"
                                    class="px-2 py-1 text-xs rounded-md border transition-all duration-150 hover:border-[#2C3E50] flex items-center space-x-1 bg-[#8B4513] text-white"
                                    title="Medium"
                                >
                                    <div class="w-3 h-3 rounded-full bg-[#8B4513] border border-gray-300"></div>
                                    <span>Medium</span>
                                </button>
                                <button 
                                    wire:click="setSkinTone('dark')"
                                    class="px-2 py-1 text-xs rounded-md border transition-all duration-150 hover:border-[#2C3E50] flex items-center space-x-1 bg-[#2F1F1F] text-white"
                                    title="Dark"
                                >
                                    <div class="w-3 h-3 rounded-full bg-[#2F1F1F] border border-gray-300"></div>
                                    <span>Dark</span>
                                </button>
                            </div>
                        </div>
                    @endif
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
</div>
