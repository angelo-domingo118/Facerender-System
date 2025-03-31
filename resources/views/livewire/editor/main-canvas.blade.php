<div class="h-full flex flex-col bg-gray-100 overflow-hidden">
    <!-- Selection Tools Toolbar -->
    <div class="bg-white border-b border-gray-200 p-2 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <button class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 bg-gray-100 flex items-center" x-tooltip="'Move Tool (V)'">
                <!-- Cursor Arrow Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                </svg>
                <span class="text-xs">Move</span>
            </button>
            <button class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Scale Tool (S)'">
                <!-- Arrows Pointing Out Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" />
                </svg>
                <span class="text-xs">Scale</span>
            </button>
            <button class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Rotate Tool (R)'">
                <!-- Arrow Path Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="text-xs">Rotate</span>
            </button>
        </div>
        
        <div class="flex items-center space-x-3">
            <button class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Fit to Canvas'">
                <!-- Viewfinder Circle Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span class="text-xs">Fit</span>
            </button>
            <button class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Zoom In (+)'">
                <!-- Plus Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="text-xs">Zoom In</span>
            </button>
            <button class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition-colors duration-200 flex items-center" x-tooltip="'Zoom Out (-)'">
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
        <!-- Canvas background with grid -->
        <div class="relative bg-white shadow-md" style="width: 600px; height: 600px;">
            <!-- Grid pattern overlay -->
            <div class="absolute inset-0 bg-grid opacity-10"></div>
            
            <!-- Canvas content -->
            <div class="absolute inset-0">
                <!-- Blank canvas - no placeholders -->
            </div>
        </div>
    </div>
    
    <style>
        .bg-grid {
            background-image: 
                linear-gradient(to right, #e5e7eb 1px, transparent 1px),
                linear-gradient(to bottom, #e5e7eb 1px, transparent 1px);
            background-size: 20px 20px;
        }
    </style>
</div>
