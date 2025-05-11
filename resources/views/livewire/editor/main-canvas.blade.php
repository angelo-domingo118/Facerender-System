<div class="h-full flex flex-col bg-transparent overflow-hidden" id="main-canvas-component">
    <!-- Selection Tools Toolbar -->
    <div class="bg-slate-800/50 backdrop-blur-sm border-b border-slate-700 p-2 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <button 
                wire:click="toggleMoveMode" 
                id="move-tool" 
                class="p-1.5 rounded-md transition-colors duration-200 flex items-center {{ $moveEnabled ? 'bg-[#3498DB]/30 text-[#3498DB] border border-[#3498DB]/50' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}" 
                x-tooltip="'{{ $moveEnabled ? 'Disable' : 'Enable' }} Move Mode'"
            >
                <!-- Cursor Arrow Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                </svg>
                <span class="text-xs">{{ $moveEnabled ? 'Move: ON' : 'Move: OFF' }}</span>
            </button>
            <button id="delete-selected" class="p-1.5 rounded-md text-gray-300 hover:bg-slate-700 hover:text-white transition-colors duration-200 flex items-center" x-tooltip="'Delete Selected'">
                <!-- Delete Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span class="text-xs">Delete</span>
            </button>
            <button id="clear-canvas" class="p-1.5 rounded-md text-gray-300 hover:bg-slate-700 hover:text-white transition-colors duration-200 flex items-center" x-tooltip="'Clear Canvas'">
                <!-- Clear Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="text-xs">Clear</span>
            </button>
        </div>
        
        <div class="flex items-center space-x-3">
            <button id="reset-zoom" class="p-1.5 rounded-md text-gray-300 hover:bg-slate-700 hover:text-white transition-colors duration-200 flex items-center" x-tooltip="'Fit to Canvas'">
                <!-- Viewfinder Circle Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-xs">Fit</span>
            </button>
            <button id="zoom-in" class="p-1.5 rounded-md text-gray-300 hover:bg-slate-700 hover:text-white transition-colors duration-200 flex items-center" x-tooltip="'Zoom In (+)'">
                <!-- Plus Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-xs">Zoom In</span>
            </button>
            <button id="zoom-out" class="p-1.5 rounded-md text-gray-300 hover:bg-slate-700 hover:text-white transition-colors duration-200 flex items-center" x-tooltip="'Zoom Out (-)'">
                <!-- Minus Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
                <span class="text-xs">Zoom Out</span>
            </button>
            <div class="px-2 py-1 bg-slate-600 rounded-md text-xs text-gray-300">
                <span id="zoom-level">{{ $zoomLevel }}%</span>
            </div>
        </div>
    </div>
    
    <!-- Canvas Area -->
    <div class="flex-1 overflow-auto relative p-6 flex items-center justify-center bg-transparent" id="canvas-viewport">
        <!-- Canvas container - this will be transformed for zooming -->
        <div class="relative bg-transparent shadow-none" style="width: 600px; height: 600px; transform-origin: center center;" data-scale="1" wire:ignore>
            <canvas id="editor-canvas" width="600" height="600"></canvas>
        </div>
    </div>
    
    <!-- Export Options Modal -->
    <div id="export-options-modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-slate-800 rounded-lg shadow-xl w-full max-w-md p-6 border border-slate-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-200">Export Options</h3>
                <button id="close-export-modal" class="text-gray-400 hover:text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <!-- File Format -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">File Format</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="file-format" value="png" class="form-radio h-4 w-4 text-[#3498DB] bg-slate-700 border-slate-600 focus:ring-[#3498DB]" checked>
                            <span class="ml-2 text-gray-300">PNG</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="file-format" value="jpeg" class="form-radio h-4 w-4 text-[#3498DB] bg-slate-700 border-slate-600 focus:ring-[#3498DB]">
                            <span class="ml-2 text-gray-300">JPEG</span>
                        </label>
                    </div>
                </div>
                
                <!-- Transparency Option (Only for PNG) -->
                <div id="transparency-option" class="transition-opacity duration-200">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Transparency</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="transparency" value="keep" class="form-radio h-4 w-4 text-[#3498DB] bg-slate-700 border-slate-600 focus:ring-[#3498DB]" checked>
                            <span class="ml-2 text-gray-300">Keep transparency</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="transparency" value="remove" class="form-radio h-4 w-4 text-[#3498DB] bg-slate-700 border-slate-600 focus:ring-[#3498DB]">
                            <span class="ml-2 text-gray-300">White background</span>
                        </label>
                    </div>
                </div>
                
                <!-- Background Grid Option -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Background Grid</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="grid" value="hide" class="form-radio h-4 w-4 text-[#3498DB] bg-slate-700 border-slate-600 focus:ring-[#3498DB]" checked>
                            <span class="ml-2 text-gray-300">Hide grid</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="grid" value="show" class="form-radio h-4 w-4 text-[#3498DB] bg-slate-700 border-slate-600 focus:ring-[#3498DB]">
                            <span class="ml-2 text-gray-300">Show grid</span>
                        </label>
                    </div>
                </div>
                
                <!-- Quality Option (Only for JPEG) -->
                <div id="quality-option" class="hidden transition-opacity duration-200">
                    <label for="quality-slider" class="block text-sm font-medium text-gray-300 mb-2">
                        Quality: <span id="quality-value">90</span>%
                    </label>
                    <input type="range" id="quality-slider" min="10" max="100" value="90" step="5"
                        class="w-full h-2 bg-slate-600 rounded-lg appearance-none cursor-pointer accent-[#3498DB]">
                </div>
                
                <!-- Export Size Option -->
                <div>
                    <label for="export-size" class="block text-sm font-medium text-gray-300 mb-2">
                        Export Size
                    </label>
                    <select id="export-size" class="w-full border border-slate-600 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-[#3498DB] focus:border-[#3498DB] bg-slate-700 text-gray-200">
                        <option value="1">Original size (1x)</option>
                        <option value="2" selected>Double size (2x)</option>
                        <option value="3">Triple size (3x)</option>
                        <option value="4">Quadruple size (4x)</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <button id="cancel-export" class="px-4 py-2 bg-slate-700 text-gray-300 rounded-md hover:bg-slate-600">
                    Cancel
                </button>
                <button id="confirm-export" class="px-4 py-2 bg-[#3498DB] text-white rounded-md hover:bg-[#2980B9]">
                    Export
                </button>
            </div>
        </div>
    </div>
    
    @vite(['resources/js/editor-canvas.js'])

    <script>
        // Use window.onload instead of DOMContentLoaded to ensure consistent initialization
        window.addEventListener('load', function() {
            // No need for additional event listeners here since they're handled in editor-canvas.js
            // and were causing duplicate processing
        });
    </script>
</div>
