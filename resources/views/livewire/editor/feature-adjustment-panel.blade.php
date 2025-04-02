<div class="h-full flex flex-col bg-gray-50">
    <div class="p-3 border-b border-gray-200 bg-white">
        <h3 class="font-medium text-[#2C3E50]">Feature Adjustments</h3>
    </div>
    
    <div class="flex-1 overflow-y-auto">
        <div class="p-3">
            <!-- Feature Name -->
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Feature: Eyes</h4>
                <div class="bg-gray-100 p-2 rounded-md">
                    <p class="text-xs text-gray-500">Selected feature information</p>
                </div>
            </div>
            
            <!-- Basic Adjustments -->
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Basic Adjustments</h4>
                
                <!-- Opacity Control -->
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Opacity</label>
                    <div class="flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-1.5 flex-1 mr-2">
                            <div class="bg-[#2C3E50] h-1.5 rounded-full" style="width: 75%"></div>
                        </div>
                        <span class="text-xs text-gray-500 min-w-[30px]">75%</span>
                    </div>
                </div>
                
                <!-- Brightness Control -->
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Brightness</label>
                    <div class="flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-1.5 flex-1 mr-2">
                            <div class="bg-[#2C3E50] h-1.5 rounded-full" style="width: 50%"></div>
                        </div>
                        <span class="text-xs text-gray-500 min-w-[30px]">50%</span>
                    </div>
                </div>
                
                <!-- Contrast Control -->
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Contrast</label>
                    <div class="flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-1.5 flex-1 mr-2">
                            <div class="bg-[#2C3E50] h-1.5 rounded-full" style="width: 50%"></div>
                        </div>
                        <span class="text-xs text-gray-500 min-w-[30px]">50%</span>
                    </div>
                </div>
                
                <!-- Saturation Control -->
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Saturation</label>
                    <div class="flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-1.5 flex-1 mr-2">
                            <div class="bg-[#2C3E50] h-1.5 rounded-full" style="width: 50%"></div>
                        </div>
                        <span class="text-xs text-gray-500 min-w-[30px]">50%</span>
                    </div>
                </div>
                
                <!-- Blend Mode -->
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Blend Mode</label>
                    <select class="w-full bg-white border border-gray-300 rounded-md text-sm px-2 py-1">
                        <option>Normal</option>
                        <option>Multiply</option>
                        <option>Screen</option>
                        <option>Overlay</option>
                    </select>
                </div>
            </div>
            
            <!-- Advanced Adjustments -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-medium text-gray-700">Advanced Adjustments</h4>
                    <button class="bg-gray-100 hover:bg-gray-200 rounded p-1 flex items-center justify-center">
                        <span class="text-xs p-1">Expand</span>
                    </button>
                </div>
                
                <!-- Sharpness/Blur Control -->
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Sharpness/Blur</label>
                    <div class="flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-1.5 flex-1 mr-2">
                            <div class="bg-[#2C3E50] h-1.5 rounded-full" style="width: 50%"></div>
                        </div>
                        <span class="text-xs text-gray-500 min-w-[30px]">50%</span>
                    </div>
                </div>
                
                <!-- Edge Feathering Control -->
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Edge Feathering</label>
                    <div class="flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-1.5 flex-1 mr-2">
                            <div class="bg-[#2C3E50] h-1.5 rounded-full" style="width: 20%"></div>
                        </div>
                        <span class="text-xs text-gray-500 min-w-[30px]">20%</span>
                    </div>
                </div>
                
                <!-- Skin Tone Matching -->
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Skin Tone Matching</label>
                    <button class="w-full bg-gray-100 hover:bg-gray-200 rounded py-1 px-2 text-xs text-left">
                        Apply Skin Tone Matching
                    </button>
                </div>
            </div>
            
            <!-- Adjustment Presets -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-medium text-gray-700">Adjustment Presets</h4>
                </div>
                
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <button class="bg-gray-100 hover:bg-gray-200 rounded py-1 px-2 text-xs">
                        Save Current
                    </button>
                    <button class="bg-gray-100 hover:bg-gray-200 rounded py-1 px-2 text-xs">
                        Reset to Default
                    </button>
                </div>
                
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Saved Presets</label>
                    <select class="w-full bg-white border border-gray-300 rounded-md text-sm px-2 py-1">
                        <option>Default</option>
                        <option>High Contrast</option>
                        <option>Soft Light</option>
                        <option>Vintage</option>
                    </select>
                </div>
                
                <div class="grid grid-cols-2 gap-2">
                    <button class="bg-gray-100 hover:bg-gray-200 rounded py-1 px-2 text-xs">
                        Import Presets
                    </button>
                    <button class="bg-gray-100 hover:bg-gray-200 rounded py-1 px-2 text-xs">
                        Export Presets
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
