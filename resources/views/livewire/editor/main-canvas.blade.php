<div class="h-full flex flex-col bg-gray-100 overflow-hidden" id="main-canvas-component" wire:ignore>
    <!-- Selection Tools Toolbar -->
    <div class="bg-white border-b border-gray-200 p-2 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <button id="move-tool" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 {{ $activeTool === 'move' ? 'bg-gray-100' : '' }} flex items-center" x-tooltip="'Move Tool (V)'">
                <!-- Cursor Arrow Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                </svg>
                <span class="text-xs">Move</span>
            </button>
            <button id="delete-selected" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Delete Selected'">
                <!-- Delete Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span class="text-xs">Delete</span>
            </button>
            <button id="clear-canvas" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Clear Canvas'">
                <!-- Clear Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="text-xs">Clear</span>
            </button>
        </div>
        
        <div class="flex items-center space-x-3">
            <button id="reset-zoom" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Fit to Canvas'">
                <!-- Viewfinder Circle Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-xs">Fit</span>
            </button>
            <button id="zoom-in" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Zoom In (+)'">
                <!-- Plus Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-xs">Zoom In</span>
            </button>
            <button id="zoom-out" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Zoom Out (-)'">
                <!-- Minus Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
                <span class="text-xs">Zoom Out</span>
            </button>
        </div>
    </div>
    
    <!-- Canvas Area -->
    <div class="flex-1 overflow-auto relative p-6 flex items-center justify-center bg-gray-200">
        <!-- Canvas container -->
        <div class="relative bg-white shadow-md" style="width: 600px; height: 600px;">
            <canvas id="editor-canvas" width="600" height="600"></canvas>
        </div>
    </div>
    
    @vite(['resources/js/editor-canvas.js'])

    <script>
        // Debug event communication
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for feature-selected event and log it
            window.addEventListener('feature-selected', (event) => {
                console.log('Feature selected event caught in main-canvas:', event.detail);
            });
            
            // Listen for direct update-canvas event
            window.addEventListener('direct-update-canvas', (event) => {
                console.log('Direct update-canvas event caught in main-canvas:', event.detail);
            });
        });
    </script>
</div>
