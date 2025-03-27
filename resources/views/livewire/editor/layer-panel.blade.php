<div class="h-full flex flex-col bg-gray-50">
    <div class="p-3 border-b border-gray-200 bg-white">
        <div class="flex justify-between items-center">
            <h3 class="font-medium text-[#2C3E50]">Layers</h3>
            <div class="flex space-x-1">
                <button class="p-1 rounded-md text-gray-500 hover:bg-gray-100 transition-colors duration-200">
                    <!-- Plus Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
                <button class="p-1 rounded-md text-gray-500 hover:bg-gray-100 transition-colors duration-200">
                    <!-- Document Stack Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                    </svg>
                </button>
                <button class="p-1 rounded-md text-gray-500 hover:bg-gray-100 transition-colors duration-200">
                    <!-- Trash Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Layer List -->
    <div class="flex-1 overflow-y-auto">
        <div class="p-2">
            <!-- Active Layer -->
            <div class="mb-2 bg-[#2C3E50]/5 border border-[#2C3E50]/20 p-2 rounded-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <button class="text-[#2C3E50]">
                            <!-- Eye Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        <span class="text-sm font-medium text-[#2C3E50]">Eyes</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="text-[#2C3E50]/70">
                            <!-- Lock Open Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                        </button>
                        <button class="text-[#2C3E50]/70">
                            <!-- Ellipsis Vertical Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Inactive Layer -->
            <div class="mb-2 bg-white border border-gray-200 p-2 rounded-md">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <button class="text-gray-400">
                            <!-- Eye Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        <span class="text-sm text-gray-600">Nose</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="text-gray-400">
                            <!-- Lock Closed Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </button>
                        <button class="text-gray-400">
                            <!-- Ellipsis Vertical Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Empty State -->
            <div class="mt-6 text-center py-6">
                <!-- Square Stack Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                <p class="text-sm text-gray-500">Add features to see them appear as layers</p>
            </div>
        </div>
    </div>
    
    <!-- Layer Properties -->
    <div class="p-3 border-t border-gray-200">
        <div class="space-y-3">
            <h4 class="text-xs font-medium text-gray-500 uppercase">Layer Properties</h4>
            
            <div>
                <label class="text-xs text-gray-600 mb-1 block">Opacity</label>
                <input type="range" min="0" max="100" value="100" class="w-full accent-[#2C3E50]">
            </div>
            
            <div>
                <label class="text-xs text-gray-600 mb-1 block">Blend Mode</label>
                <x-native-select
                    :options="[
                        'Normal',
                        'Multiply',
                        'Screen',
                        'Overlay',
                        'Darken',
                        'Lighten',
                    ]"
                    wire:model.live="blendMode"
                />
            </div>
        </div>
    </div>
</div>
