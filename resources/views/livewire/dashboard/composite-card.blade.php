<div>
    <div x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false" class="transition-all duration-300">
        <div class="flex items-start">
            <x-card class="w-full border-l-4 border-l-[#2C3E50] transition-all duration-200 border border-gray-300 shadow-md overflow-hidden">
                <div class="flex space-x-4">
                    <!-- Image with hover effect -->
                    <div class="relative h-24 w-24 flex-shrink-0 rounded-md bg-gray-100 overflow-hidden border border-gray-300 group">
                        @if($composite->image_path)
                            <img
                                src="{{ Storage::url($composite->image_path) }}"
                                alt="{{ $composite->title }}"
                                class="h-full w-full object-cover transition-transform duration-500"
                                :class="hovered ? 'scale-110' : ''"
                            />
                            <div 
                                class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 transition-opacity duration-300"
                                :class="hovered ? 'opacity-100' : ''"
                            ></div>
                        @else
                            <div class="flex h-full items-center justify-center bg-gray-100 transition-colors duration-300" :class="hovered ? 'bg-gray-200' : ''">
                                <x-icon name="photo" class="h-8 w-8 text-gray-400" />
                            </div>
                        @endif
                    </div>
                    
                    <!-- Info -->
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <h4 class="font-semibold text-[#2C3E50] flex items-center">
                                    <span>{{ $composite->title }}</span>
                                </h4>
                            </div>
                            
                            <div class="mt-1 flex items-center space-x-4">
                                <span class="text-xs text-gray-600 flex items-center bg-gray-100 px-2 py-1 rounded-md">
                                    <x-icon name="calendar" class="h-3 w-3 mr-1 text-[#2C3E50]" />
                                    Created: {{ $composite->created_at->format('M d, Y') }}
                                </span>
                                <span class="text-xs text-gray-600 flex items-center bg-gray-100 px-2 py-1 rounded-md">
                                    <x-icon name="user" class="h-3 w-3 mr-1 text-[#2C3E50]" />
                                    Witness: {{ $composite->witness ? $composite->witness->name : 'N/A' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="mt-3 pt-2 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <!-- Left-aligned buttons -->
                                <div class="flex space-x-2">
                                    <x-button 
                                        wire:click="viewComposite" 
                                        icon="eye"
                                        size="sm"
                                        flat
                                        class="text-[#6366F1] hover:bg-gray-100 transition-colors rounded-md"
                                    >
                                        View Details
                                    </x-button>
                                    
                                    <x-button 
                                        href="{{ route('composite.editor', $composite->id) }}"
                                        icon="pencil-square"
                                        size="sm"
                                        class="bg-[#6366F1] hover:bg-[#4F46E5] text-white transition-colors rounded-md"
                                    >
                                        Edit Composite
                                    </x-button>
                                </div>
                                
                                <!-- Right-aligned buttons -->
                                <div class="flex space-x-2">
                                    <x-button 
                                        wire:click="downloadComposite" 
                                        icon="arrow-down-tray"
                                        size="sm"
                                        flat
                                        class="text-[#6366F1] hover:bg-gray-100 transition-colors rounded-md"
                                    >
                                        Download
                                    </x-button>
                                    
                                    <x-button 
                                        wire:click="deleteComposite" 
                                        icon="trash"
                                        size="sm"
                                        outline
                                        class="text-red-600 border-red-300 hover:bg-red-50 transition-colors rounded-md"
                                    >
                                        Delete
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
