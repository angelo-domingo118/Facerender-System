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
            
            <!-- Position Controls -->
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Position</h4>
                
                <!-- X and Y inputs -->
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">X Position</label>
                        <div class="flex">
                            <input type="number" class="w-full bg-white border border-gray-300 rounded-md text-sm px-2 py-1" placeholder="0" />
                            <span class="ml-1 text-xs text-gray-500 flex items-center">px</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Y Position</label>
                        <div class="flex">
                            <input type="number" class="w-full bg-white border border-gray-300 rounded-md text-sm px-2 py-1" placeholder="0" />
                            <span class="ml-1 text-xs text-gray-500 flex items-center">px</span>
                        </div>
                    </div>
                </div>
                
                <!-- Arrow buttons -->
                <div class="grid grid-cols-3 gap-1 mb-3">
                    <div></div>
                    <button class="bg-gray-100 hover:bg-gray-200 rounded p-1 flex items-center justify-center">
                        <!-- Arrow Up Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <div></div>
                    <button class="bg-gray-100 hover:bg-gray-200 rounded p-1 flex items-center justify-center">
                        <!-- Arrow Left Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div></div>
                    <button class="bg-gray-100 hover:bg-gray-200 rounded p-1 flex items-center justify-center">
                        <!-- Arrow Right Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <div></div>
                    <button class="bg-gray-100 hover:bg-gray-200 rounded p-1 flex items-center justify-center">
                        <!-- Arrow Down Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div></div>
                </div>
            </div>
            
            <!-- Size Controls -->
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Size</h4>
                
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Width</label>
                        <div class="flex">
                            <input type="number" class="w-full bg-white border border-gray-300 rounded-md text-sm px-2 py-1" placeholder="100" />
                            <span class="ml-1 text-xs text-gray-500 flex items-center">px</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Height</label>
                        <div class="flex">
                            <input type="number" class="w-full bg-white border border-gray-300 rounded-md text-sm px-2 py-1" placeholder="100" />
                            <span class="ml-1 text-xs text-gray-500 flex items-center">px</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="text-xs text-gray-500">Lock Aspect Ratio</label>
                    <div class="relative flex items-center">
                        <div class="h-4 w-7 bg-gray-300 rounded-full"></div>
                        <div class="absolute h-3 w-3 bg-white rounded-full left-1"></div>
                    </div>
                </div>
            </div>
            
            <!-- Rotation Controls -->
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Rotation</h4>
                
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Angle</label>
                    <div class="flex">
                        <input type="number" class="w-full bg-white border border-gray-300 rounded-md text-sm px-2 py-1" placeholder="0" />
                        <span class="ml-1 text-xs text-gray-500 flex items-center">°</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <button class="bg-gray-100 hover:bg-gray-200 rounded py-1 px-2 flex items-center justify-center">
                        <!-- Arrows Right Left Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <span class="text-xs">Flip H</span>
                    </button>
                    <button class="bg-gray-100 hover:bg-gray-200 rounded py-1 px-2 flex items-center justify-center">
                        <!-- Arrows Up Down Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                        </svg>
                        <span class="text-xs">Flip V</span>
                    </button>
                </div>
            </div>
            
            <!-- Opacity Controls -->
            <div class="mb-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Appearance</h4>
                
                <div class="mb-3">
                    <label class="block text-xs text-gray-500 mb-1">Opacity</label>
                    <div class="flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-1.5 flex-1 mr-2">
                            <div class="bg-[#2C3E50] h-1.5 rounded-full" style="width: 75%"></div>
                        </div>
                        <span class="text-xs text-gray-500 min-w-[30px]">75%</span>
                    </div>
                </div>
                
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
            
            <!-- Custom Controls -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-sm font-medium text-gray-700">Custom Settings</h4>
                    <button class="bg-gray-100 hover:bg-gray-200 rounded p-1 flex items-center justify-center">
                        <!-- Plus Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-xs">Add</span>
                    </button>
                </div>
                
                <div class="bg-gray-100 p-3 rounded-md text-xs text-gray-500 text-center">
                    No custom settings available for this feature
                </div>
            </div>
        </div>
    </div>
</div>
