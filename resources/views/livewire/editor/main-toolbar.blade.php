<div class="bg-white p-2 shadow-sm">
    <div class="flex justify-between items-center">
        <!-- Left side - Compact back button and feature library -->
        <div class="flex items-center space-x-3">
            <!-- Toggle Left Sidebar Button (moved before Dashboard) -->
            <button 
                wire:click="$parent.toggleLeftSidebar"
                class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center"
                x-tooltip="'Toggle Feature Library'"
            >
                <!-- Bars-3 Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="text-sm">Feature Library</span>
            </button>
            
            <!-- Back button (now after Feature Library) -->
            <a href="{{ route('dashboard') }}" class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Return to Dashboard'">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span class="text-sm">Dashboard</span>
            </a>
        </div>
        
        <!-- Center section - File operations and title -->
        <div class="flex items-center space-x-4">
            <!-- File Operations -->
            <div class="flex items-center space-x-2">
                <button 
                    wire:click="saveCompositeFeatures"
                    class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" 
                    x-tooltip="'Save (Ctrl+S)'"
                >
                    <!-- Document Arrow Down Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm">Save</span>
                </button>
                
                <!-- Force Reload Button -->
                <button 
                    wire:click="$parent.forceReloadFeatures"
                    class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" 
                    x-tooltip="'Force Reload Features'"
                >
                    <!-- Refresh Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span class="text-sm">Reload</span>
                </button>
                
                <button class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Print (Ctrl+P)'" id="print-canvas-btn" data-action="print-canvas">
                    <!-- Printer Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span class="text-sm">Print</span>
                </button>
                <button class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Download'" id="download-canvas-btn" data-action="download-canvas">
                    <!-- Arrow Down Tray Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span class="text-sm">Download</span>
                </button>
            </div>
            
            <div class="h-6 border-r border-gray-300 mx-2"></div>
            
            <!-- Composite Title with vertically centered ID -->
            <div class="flex items-center">
                <h2 class="text-lg font-medium text-[#2C3E50] truncate max-w-md" title="{{ isset($composite) ? $composite->title : 'Composite Editor' }}">{{ isset($composite) ? $composite->title : 'Composite Editor' }}</h2>
                <div class="flex-shrink-0 flex items-center ml-2">
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded-full text-gray-600">ID: {{ $compositeId }}</span>
                </div>
            </div>
        </div>
        
        <!-- Right side - Edit Operations -->
        <div class="flex items-center space-x-3">
            <!-- Edit Operations -->
            <div class="flex items-center space-x-2">
                <button class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Undo (Ctrl+Z)'">
                    <!-- Updated Undo Icon -->
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1">
                        <path d="M20 7H9.00001C7.13077 7 6.19615 7 5.5 7.40193C5.04395 7.66523 4.66524 8.04394 4.40193 8.49999C4 9.19615 4 10.1308 4 12C4 13.8692 4 14.8038 4.40192 15.5C4.66523 15.9561 5.04394 16.3348 5.5 16.5981C6.19615 17 7.13077 17 9 17H16M20 7L17 4M20 7L17 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-sm">Undo</span>
                </button>
                <button class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Redo (Ctrl+Y)'">
                    <!-- Updated Redo Icon (flipped version) -->
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1">
                        <path d="M4 7H15C16.8692 7 17.8038 7 18.5 7.40193C18.9561 7.66523 19.3348 8.04394 19.5981 8.49999C20 9.19615 20 10.1308 20 12C20 13.8692 20 14.8038 19.5981 15.5C19.3348 15.9561 18.9561 16.3348 18.5 16.5981C17.8038 17 16.8692 17 15 17H8M4 7L7 4M4 7L7 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-sm">Redo</span>
                </button>
            </div>
            
            <!-- Toggle Right Sidebar Button -->
            <button 
                wire:click="$parent.toggleRightSidebar"
                class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center"
                x-tooltip="'Toggle Properties Panel'"
            >
                <!-- Adjustments Horizontal Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                <span class="text-sm">Properties</span>
            </button>
        </div>
    </div>
</div>
