<div class="h-full flex flex-col bg-gray-50">
    <div class="p-3 border-b border-gray-200 bg-white">
        <div class="flex justify-between items-center">
            <h3 class="font-medium text-[#2C3E50]">Layers</h3>
            <div class="flex space-x-1">
                <!-- Actions removed as they'll be handled by the feature library -->
            </div>
        </div>
    </div>
    
    <!-- Layer List - Adding flex-1 and min-height to ensure it takes available space -->
    <div class="flex-1 overflow-y-auto min-h-0">
        <div class="p-2">
            @forelse($layers as $layer)
                <div 
                    wire:key="layer-{{ $layer['id'] }}"
                    wire:click="selectLayer({{ $layer['id'] }})" 
                    class="mb-2 {{ $selectedLayerId == $layer['id'] ? 'bg-[#2C3E50]/5 border-[#2C3E50]/20' : 'bg-white border-gray-200' }} border p-2 rounded-md transition-colors duration-150 hover:border-[#2C3E50]/20"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <button 
                                wire:click.stop="toggleVisibility({{ $layer['id'] }})"
                                class="{{ $layer['visible'] ? 'text-[#2C3E50]' : 'text-gray-400' }} hover:text-indigo-500 transition-colors duration-150 p-1"
                                title="{{ $layer['visible'] ? 'Hide layer' : 'Show layer' }}"
                            >
                                @if($layer['visible'])
                                    <!-- Eye Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                @else
                                    <!-- Eye Off Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                @endif
                            </button>
                            <!-- Lock Button -->
                            <button 
                                wire:click.stop="toggleLock({{ $layer['id'] }})"
                                class="{{ $layer['locked'] ? 'text-red-500' : 'text-gray-400' }} hover:text-red-600 transition-colors duration-150 p-1"
                                title="{{ $layer['locked'] ? 'Unlock layer' : 'Lock layer' }}"
                            >
                                @if($layer['locked'])
                                    <!-- Locked Icon (Heroicon: lock-closed outline) -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                @else
                                    <!-- Unlocked Icon (Heroicon: lock-open outline) -->
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                    </svg>
                                @endif
                            </button>
                            <span class="{{ $selectedLayerId == $layer['id'] ? 'text-[#2C3E50] font-medium' : 'text-gray-600' }} text-sm truncate">{{ $layer['name'] }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Move Layer Up -->
                            <button 
                                wire:click.stop="moveLayerUp({{ $layer['id'] }})"
                                class="text-gray-500 hover:text-indigo-500 transition-colors duration-150 p-1 bg-gray-100 rounded-md"
                                title="Move layer up"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                </svg>
                            </button>
                            
                            <!-- Move Layer Down -->
                            <button 
                                wire:click.stop="moveLayerDown({{ $layer['id'] }})"
                                class="text-gray-500 hover:text-indigo-500 transition-colors duration-150 p-1 bg-gray-100 rounded-md"
                                title="Move layer down"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Delete Layer Button -->
                            <button 
                                wire:click.stop="requestDeletion({{ $layer['id'] }})"
                                class="text-gray-500 hover:text-red-600 transition-colors duration-150 p-1 bg-gray-100 rounded-md"
                                title="Delete layer"
                            >
                                <!-- Trash Icon (Heroicon: trash outline) -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="mt-6 text-center py-6">
                    <!-- Square Stack Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <p class="text-sm text-gray-500">Add features to see them appear as layers</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Layer Properties - Explicitly set as a fixed height section with flex-shrink-0 -->
    <div class="flex-shrink-0 p-3 border-t border-gray-200 bg-white {{ empty($layers) ? 'opacity-50 pointer-events-none' : '' }}">
        <div class="space-y-3">
            <h4 class="text-xs font-medium text-gray-500 uppercase">Layer Properties</h4>
            
            <div>
                <label class="text-xs text-gray-600 mb-1 block">Opacity</label>
                <input 
                    type="range" 
                    min="0" 
                    max="100" 
                    wire:model="opacity" 
                    wire:change="updateOpacity"
                    class="w-full accent-[#2C3E50]"
                >
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                    <span>0%</span>
                    <span>{{ $opacity }}%</span>
                </div>
            </div>
        </div>
    </div>
</div>
