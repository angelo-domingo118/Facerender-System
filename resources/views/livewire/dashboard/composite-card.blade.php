<div>
    <div x-data="{ hovered: false }" @mouseenter="hovered = true" @mouseleave="hovered = false" class="transition-all duration-300 hover:translate-y-[-2px]">
        <div class="flex items-start">
            <x-card class="w-full border-l-4 border-l-[#2C3E50] hover:shadow-xl transition-all duration-200 border border-gray-300 shadow-md overflow-hidden">
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
                        
                        <!-- Pin button overlay -->
                        <button 
                            wire:click="togglePin"
                            class="absolute top-1 right-1 p-1.5 rounded-full bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-500 hover:text-[#2C3E50] focus:outline-none transition-colors shadow-sm"
                            x-tooltip="'{{ $composite->is_pinned ? 'Unpin Composite' : 'Pin Composite' }}'"
                        >
                            <x-icon 
                                name="map-pin" 
                                class="h-3 w-3 {{ $composite->is_pinned ? 'text-[#2C3E50]' : '' }}" 
                                :solid="$composite->is_pinned"
                            />
                        </button>
                    </div>
                    
                    <!-- Info -->
                    <div class="flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between">
                                <h4 class="font-semibold text-[#2C3E50] flex items-center">
                                    <span>{{ $composite->title }}</span>
                                    @if($composite->is_pinned)
                                        <span class="ml-2 text-xs bg-[#2C3E50]/20 text-[#2C3E50] px-2 py-0.5 rounded-full font-medium">Pinned</span>
                                    @endif
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
                        <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-200">
                            <div class="flex space-x-1">
                                <x-button 
                                    wire:click="viewComposite" 
                                    icon="eye"
                                    xs
                                    class="bg-[#2C3E50] hover:bg-[#34495E] text-white transition-colors rounded-md"
                                    x-tooltip="'View this composite'"
                                />
                                <x-button 
                                    wire:click="editComposite" 
                                    icon="pencil"
                                    xs
                                    flat
                                    class="text-[#2C3E50] hover:bg-[#2C3E50]/20 transition-colors rounded-md"
                                    x-tooltip="'Edit this composite'"
                                />
                                <x-button 
                                    wire:click="downloadComposite" 
                                    icon="arrow-down-tray"
                                    xs
                                    flat
                                    class="text-[#2C3E50] hover:bg-[#2C3E50]/20 transition-colors rounded-md"
                                    x-tooltip="'Download this composite'"
                                />
                            </div>
                            
                            <x-button 
                                wire:click="deleteComposite" 
                                icon="trash"
                                xs
                                outline
                                :solid="true"
                                class="text-gray-700 border-gray-300 hover:bg-gray-100 transition-colors rounded-md"
                                x-tooltip="'Delete this composite'"
                            />
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <x-modal wire:model.defer="confirmingDelete">
        <x-card title="Delete Composite">
            <div class="flex flex-col items-center justify-center text-center">
                <div class="rounded-full bg-red-100 p-3 mb-4">
                    <x-icon name="exclamation-triangle" class="w-8 h-8 text-red-500" solid />
                </div>
                <p class="text-gray-600 mb-6">
                    Are you sure you want to delete this composite? This action cannot be undone.
                </p>
            </div>

            <x-slot name="footer">
                <div class="flex justify-end gap-x-4">
                    <x-button 
                        wire:click="cancelDelete" 
                        flat
                        label="Cancel"
                    />
                    <x-button 
                        wire:click="confirmDelete" 
                        red
                        spinner="confirmDelete" 
                        label="Delete Composite"
                    />
                </div>
            </x-slot>
        </x-card>
    </x-modal>
</div>
