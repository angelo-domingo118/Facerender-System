<div class="h-full flex flex-col bg-gray-50">
    <div class="p-3 border-b border-gray-200 bg-white">
        <div class="flex justify-between items-center">
            <h3 class="font-medium text-[#2C3E50]">Layers</h3>
            <div class="flex space-x-1">
                <!-- Actions removed as they'll be handled by the feature library -->
            </div>
        </div>
    </div>
    
    <!-- Layer List -->
    <div class="flex-1 overflow-y-auto">
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
    
    <!-- Layer Properties -->
    <div class="p-3 border-t border-gray-200 {{ empty($layers) ? 'opacity-50 pointer-events-none' : '' }}">
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
                    <span>100%</span>
                </div>
            </div>
            
            <div>
                <label class="text-xs text-gray-600 mb-1 block">Blend Mode</label>
                <select 
                    wire:model.live="blendMode"
                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-xs"
                >
                    <option value="Normal">Normal</option>
                    <option value="Multiply">Multiply</option>
                    <option value="Screen">Screen</option>
                    <option value="Overlay">Overlay</option>
                    <option value="Darken">Darken</option>
                    <option value="Lighten">Lighten</option>
                </select>
            </div>
        </div>
    </div>
</div>
