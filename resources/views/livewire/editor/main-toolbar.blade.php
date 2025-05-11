<div class="bg-[#2C3E50]/80 backdrop-blur-sm p-2 shadow-md border-b border-slate-700">
    <div class="flex justify-between items-center">
        <!-- Left side - Compact back button and feature library -->
        <div class="flex items-center space-x-3">
            <!-- Toggle Left Sidebar Button (moved before Dashboard) -->
            <button 
                wire:click="$parent.toggleLeftSidebar"
                class="p-1.5 rounded-md text-gray-300 hover:bg-slate-700 hover:text-white transition-colors duration-200 flex items-center"
                x-tooltip="'Toggle Feature Library'"
            >
                <!-- Bars-3 Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="text-sm">Feature Library</span>
            </button>
            
            <!-- Back button (now after Feature Library) -->
            <a href="{{ route('dashboard') }}" class="p-1.5 rounded-md text-gray-300 hover:bg-slate-700 hover:text-white transition-colors duration-200 flex items-center" x-tooltip="'Return to Dashboard'">
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
                    wire:loading.attr="disabled"
                    wire:target="saveCompositeFeatures"
                    class="p-1.5 rounded-md text-gray-300 hover:bg-slate-700 hover:text-white transition-colors duration-200 flex items-center" 
                    x-tooltip="'Save (Ctrl+S)'"
                >
                    <!-- Document Arrow Down Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" wire:loading.class.remove="text-gray-500" wire:loading.class="text-green-500" wire:target="saveCompositeFeatures">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm" wire:loading.remove wire:target="saveCompositeFeatures">Save</span>
                    <span class="text-sm text-green-500" wire:loading wire:target="saveCompositeFeatures">Saving...</span>
                </button>
                
                <button class="p-1.5 rounded-md text-gray-300 hover:bg-slate-700 hover:text-white transition-colors duration-200 flex items-center" x-tooltip="'Print (Ctrl+P)'" id="print-canvas-btn" data-action="print-canvas">
                    <!-- Printer Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span class="text-sm">Print</span>
                </button>
                <button class="p-1.5 rounded-md text-gray-300 hover:bg-slate-700 hover:text-white transition-colors duration-200 flex items-center" x-tooltip="'Download'" id="download-canvas-btn" data-action="download-canvas">
                    <!-- Arrow Down Tray Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span class="text-sm">Download</span>
                </button>
            </div>
            
            <div class="h-6 border-r border-slate-600 mx-2"></div>
            
            <!-- Composite Title with vertically centered ID -->
            <div class="flex items-center">
                <h2 class="text-lg font-medium text-white truncate max-w-md" title="{{ isset($composite) ? $composite->title : 'Composite Editor' }}">{{ isset($composite) ? $composite->title : 'Composite Editor' }}</h2>
                <div class="flex-shrink-0 flex items-center ml-2">
                    <span class="text-xs bg-slate-700 px-2 py-1 rounded-full text-gray-300">ID: {{ $compositeId }}</span>
                </div>
            </div>
        </div>
        
        <!-- Right side - Properties Panel Button -->
        <div class="flex items-center space-x-3">
            <!-- Toggle Right Sidebar Button -->
            <button 
                wire:click="$parent.toggleRightSidebar"
                class="p-1.5 rounded-md text-gray-300 hover:bg-slate-700 hover:text-white transition-colors duration-200 flex items-center"
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
