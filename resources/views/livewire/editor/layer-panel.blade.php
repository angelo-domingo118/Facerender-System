<div class="h-full flex flex-col bg-gray-50">
    <!-- Layer List - Adding flex-1 and min-height to ensure it takes available space -->
    <div class="flex-1 overflow-y-auto min-h-0">
        <div class="p-2" wire:sortable="updateLayerOrder">
            @forelse($layers as $index => $layer)
                <script>
                    console.log('LAYER DEBUG (Blade): Rendering layer', {{ $index }}, {{ json_encode($layer['name']) }});
                </script>
                <div 
                    wire:key="layer-{{ $layer['id'] }}"
                    wire:sortable.item="{{ $layer['id'] }}"
                    wire:click="selectLayer({{ $layer['id'] }})" 
                    class="mb-2 {{ $selectedLayerId == $layer['id'] ? 'bg-[#2C3E50]/5 border-[#2C3E50]/20' : 'bg-white border-gray-200' }} border p-2 rounded-md transition-colors duration-150 hover:border-[#2C3E50]/20 cursor-move"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2" wire:sortable.handle>
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
                            <!-- Drag Handle Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6.75h16.5" />
                            </svg>
                            <span class="{{ $selectedLayerId == $layer['id'] ? 'text-[#2C3E50] font-medium' : 'text-gray-600' }} text-sm truncate">{{ $layer['name'] }}</span>
                        </div>
                        <div>
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
            <h4 class="text-xs font-medium text-gray-500 uppercase flex justify-between items-center">
                Layer Properties
                <!-- Add actual dimensions display -->
                @if($selectedLayerId)
                <span class="text-xs font-normal text-gray-400">{{ $width }} × {{ $height }} px</span>
                @endif
            </h4>
            
            <!-- Position Controls -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-600 mb-1 block">X Position</label>
                    <div class="flex">
                        <input 
                            type="number" 
                            wire:model.defer="positionX" 
                            wire:change="updatePosition"
                            class="w-full text-sm border border-gray-300 rounded-md px-2 py-1"
                            step="1"
                        >
                    </div>
                </div>
                <div>
                    <label class="text-xs text-gray-600 mb-1 block">Y Position</label>
                    <div class="flex">
                        <input 
                            type="number" 
                            wire:model.defer="positionY" 
                            wire:change="updatePosition"
                            class="w-full text-sm border border-gray-300 rounded-md px-2 py-1"
                            step="1"
                        >
                    </div>
                </div>
            </div>
            
            <!-- Add Size Display Section -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-gray-600 mb-1 block">Width</label>
                    <div class="flex">
                        <input 
                            type="text" 
                            value="{{ $width }} px"
                            readonly
                            class="w-full text-sm border border-gray-100 bg-gray-50 text-gray-500 rounded-md px-2 py-1"
                        >
                    </div>
                </div>
                <div>
                    <label class="text-xs text-gray-600 mb-1 block">Height</label>
                    <div class="flex">
                        <input 
                            type="text" 
                            value="{{ $height }} px"
                            readonly
                            class="w-full text-sm border border-gray-100 bg-gray-50 text-gray-500 rounded-md px-2 py-1"
                        >
                    </div>
                </div>
            </div>
            
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

<!-- Add JavaScript for tracking and logging position and dimensions -->
<script>
    // TESTING: Log when script loads
    console.log('POSITION TRACKING: Layer panel script loaded');
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('POSITION TRACKING: DOM content loaded in layer panel');
        
        // Log initial width and height values
        console.log('DIMENSION DEBUG: Initial values:', {
            width: @json($width ?? 'undefined'),
            height: @json($height ?? 'undefined')
        });
        
        // For Livewire 3, we need to use the dispatch method in a special way
        window.addEventListener('fabricjs:object-selected', function(event) {
            const obj = event.detail;
            console.log('POSITION TRACKING: fabricjs:object-selected event received', event);
            console.log('DIMENSION DEBUG: Raw object from event:', obj);
            
            if (obj) {
                // Debug all properties of the object
                console.log('DIMENSION DEBUG: Object properties:', Object.keys(obj));
                
                // Update position values in Livewire component
                @this.set('positionX', Math.round(obj.left || 0));
                @this.set('positionY', Math.round(obj.top || 0));
                
                // Debug width calculations
                console.log('DIMENSION DEBUG: Width calculation:', {
                    originalWidth: obj.width,
                    scaleX: obj.scaleX,
                    calculated: obj.width * obj.scaleX,
                    typeofWidth: typeof obj.width,
                    typeofScaleX: typeof obj.scaleX
                });
                
                // Debug height calculations
                console.log('DIMENSION DEBUG: Height calculation:', {
                    originalHeight: obj.height,
                    scaleY: obj.scaleY,
                    calculated: obj.height * obj.scaleY,
                    typeofHeight: typeof obj.height,
                    typeofScaleY: typeof obj.scaleY
                });
                
                // Update actual dimensions
                if (obj.width !== undefined && obj.scaleX !== undefined) {
                    const actualWidth = Math.round(obj.width * obj.scaleX);
                    console.log('DIMENSION DEBUG: Setting width to:', actualWidth);
                    @this.set('width', actualWidth);
                } else {
                    console.warn('DIMENSION DEBUG: Cannot calculate width - missing properties', { 
                        width: obj.width, 
                        scaleX: obj.scaleX 
                    });
                }
                
                if (obj.height !== undefined && obj.scaleY !== undefined) {
                    const actualHeight = Math.round(obj.height * obj.scaleY);
                    console.log('DIMENSION DEBUG: Setting height to:', actualHeight);
                    @this.set('height', actualHeight);
                } else {
                    console.warn('DIMENSION DEBUG: Cannot calculate height - missing properties', { 
                        height: obj.height, 
                        scaleY: obj.scaleY 
                    });
                }
                
                // Call the handleObjectSelected method directly instead of relying on event system
                console.log('DIMENSION DEBUG: Calling handleObjectSelected with:', obj);
                @this.call('handleObjectSelected', obj);
                
                // Check livewire component properties after setting
                setTimeout(() => {
                    console.log('DIMENSION DEBUG: After update, checking component properties');
                    @this.get('width', (value) => {
                        console.log('DIMENSION DEBUG: Current width in component:', value);
                    });
                    @this.get('height', (value) => {
                        console.log('DIMENSION DEBUG: Current height in component:', value);
                    });
                }, 100);
                
                // Log position and dimensions for debugging
                console.log('POSITION TRACKING: Selected object details:', { 
                    x: obj.left, 
                    y: obj.top,
                    width: obj.width,
                    height: obj.height,
                    scaleX: obj.scaleX,
                    scaleY: obj.scaleY,
                    actualWidth: obj.width * obj.scaleX,
                    actualHeight: obj.height * obj.scaleY
                });
            }
        });
        
        window.addEventListener('fabricjs:object-modified', function(event) {
            const obj = event.detail;
            console.log('POSITION TRACKING: fabricjs:object-modified event received', event);
            console.log('DIMENSION DEBUG: Modified object raw data:', obj);
            
            if (obj) {
                // Update position values in Livewire component
                @this.set('positionX', Math.round(obj.left || 0));
                @this.set('positionY', Math.round(obj.top || 0));
                
                // Debug width calculations for modified object
                console.log('DIMENSION DEBUG: Modified width calculation:', {
                    originalWidth: obj.width,
                    scaleX: obj.scaleX,
                    calculated: obj.width * obj.scaleX
                });
                
                // Debug height calculations for modified object
                console.log('DIMENSION DEBUG: Modified height calculation:', {
                    originalHeight: obj.height,
                    scaleY: obj.scaleY,
                    calculated: obj.height * obj.scaleY
                });
                
                // Update actual dimensions
                if (obj.width !== undefined && obj.scaleX !== undefined) {
                    const actualWidth = Math.round(obj.width * obj.scaleX);
                    console.log('DIMENSION DEBUG: Setting modified width to:', actualWidth);
                    @this.set('width', actualWidth);
                } else {
                    console.warn('DIMENSION DEBUG: Cannot calculate modified width - missing properties');
                }
                
                if (obj.height !== undefined && obj.scaleY !== undefined) {
                    const actualHeight = Math.round(obj.height * obj.scaleY);
                    console.log('DIMENSION DEBUG: Setting modified height to:', actualHeight);
                    @this.set('height', actualHeight);
                } else {
                    console.warn('DIMENSION DEBUG: Cannot calculate modified height - missing properties');
                }
                
                // Call the handleObjectModified method directly instead of relying on event system
                console.log('DIMENSION DEBUG: Calling handleObjectModified with:', obj);
                @this.call('handleObjectModified', obj);
                
                // Log position and dimensions for debugging
                console.log('POSITION TRACKING: Modified object details:', { 
                    x: obj.left, 
                    y: obj.top,
                    width: obj.width,
                    height: obj.height,
                    scaleX: obj.scaleX,
                    scaleY: obj.scaleY,
                    actualWidth: obj.width * obj.scaleX,
                    actualHeight: obj.height * obj.scaleY
                });
            }
        });
        
        // Listen for scaling events to update dimensions in real-time
        window.addEventListener('fabricjs:object-scaling', function(event) {
            const obj = event.detail;
            console.log('DIMENSION DEBUG: Scaling event received:', event);
            
            if (obj) {
                console.log('DIMENSION DEBUG: Scaling object raw data:', obj);
                
                // Debug scaling calculations
                console.log('DIMENSION DEBUG: Scaling width calculation:', {
                    originalWidth: obj.width,
                    scaleX: obj.scaleX,
                    calculated: obj.width * obj.scaleX
                });
                
                console.log('DIMENSION DEBUG: Scaling height calculation:', {
                    originalHeight: obj.height,
                    scaleY: obj.scaleY,
                    calculated: obj.height * obj.scaleY
                });
                
                // Update actual dimensions during scaling
                if (obj.width !== undefined && obj.scaleX !== undefined) {
                    const actualWidth = Math.round(obj.width * obj.scaleX);
                    console.log('DIMENSION DEBUG: Setting scaling width to:', actualWidth);
                    @this.set('width', actualWidth);
                } else {
                    console.warn('DIMENSION DEBUG: Cannot calculate scaling width - missing properties');
                }
                
                if (obj.height !== undefined && obj.scaleY !== undefined) {
                    const actualHeight = Math.round(obj.height * obj.scaleY);
                    console.log('DIMENSION DEBUG: Setting scaling height to:', actualHeight);
                    @this.set('height', actualHeight);
                } else {
                    console.warn('DIMENSION DEBUG: Cannot calculate scaling height - missing properties');
                }
                
                console.log('POSITION TRACKING: Object dimensions during scaling:', { 
                    actualWidth: obj.width * obj.scaleX,
                    actualHeight: obj.height * obj.scaleY
                });
            }
        });
        
        // Listen for Livewire events to update the canvas object position
        Livewire.on('updateObjectPosition', function(data) {
            console.log('POSITION TRACKING: updateObjectPosition event received', data);
            
            const x = data.x;
            const y = data.y;
            
            // This assumes you have a function to update the selected object in your canvas
            if (window.fabricCanvas && window.fabricCanvas.getActiveObject()) {
                const obj = window.fabricCanvas.getActiveObject();
                obj.set({ left: x, top: y });
                // Call setCoords to update the object's coordinates for proper rendering
                obj.setCoords();
                window.fabricCanvas.renderAll();
                console.log('POSITION TRACKING: Position updated from panel:', { x, y });
            } else {
                console.log('POSITION TRACKING: Cannot update position - no active object or canvas not available');
            }
        });
        
        // Log when input values change
        document.querySelectorAll('input[wire\\:model\\.defer="positionX"], input[wire\\:model\\.defer="positionY"]').forEach(input => {
            input.addEventListener('change', function() {
                console.log('POSITION TRACKING: Position input changed:', { 
                    inputName: this.getAttribute('wire:model.defer'),
                    value: this.value 
                });
            });
        });
        
        // Check if width and height are displayed in the UI
        const dimensionDisplay = document.querySelector('.text-xs.font-normal.text-gray-400');
        if (dimensionDisplay) {
            console.log('DIMENSION DEBUG: Dimension display element found:', dimensionDisplay.textContent);
        } else {
            console.warn('DIMENSION DEBUG: Dimension display element not found');
        }
        
        // Check if width and height input fields are properly populated
        const widthInput = document.querySelector('input[value*="width"]');
        const heightInput = document.querySelector('input[value*="height"]');
        
        if (widthInput) {
            console.log('DIMENSION DEBUG: Width input found with value:', widthInput.value);
        } else {
            console.warn('DIMENSION DEBUG: Width input not found');
        }
        
        if (heightInput) {
            console.log('DIMENSION DEBUG: Height input found with value:', heightInput.value);
        } else {
            console.warn('DIMENSION DEBUG: Height input not found');
        }
    });
    
    // Log when position is updated via direct DOM events
    document.addEventListener('livewire:direct-update-object-position', function(event) {
        console.log('POSITION TRACKING: direct-update-object-position event received', event.detail);
    });
    
    // Additional Livewire debugging
    document.addEventListener('livewire:initialized', function() {
        console.log('DIMENSION DEBUG: Livewire initialized');
    });
    
    document.addEventListener('livewire:update', function() {
        console.log('DIMENSION DEBUG: Livewire update triggered');
    });
</script>
