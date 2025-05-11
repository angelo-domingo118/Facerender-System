<div class="h-full flex flex-col">
    <!-- Main Toolbar -->
    <div class="border-b border-slate-700">
        @livewire('editor.main-toolbar', ['compositeId' => $compositeId])
    </div>
    
    <!-- Main Content Area -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Left Sidebar - Feature Library -->
        <div 
            x-data="{ expanded: @entangle('leftSidebarExpanded') }"
            :class="expanded ? 'w-80' : 'w-0'"
            class="transition-all duration-300 flex-none border-r border-slate-700 overflow-hidden bg-slate-800/30 backdrop-blur-sm"
        >
            <div class="h-full" x-show="expanded" x-transition>
                @livewire('editor.feature-library')
            </div>
        </div>
        
        <!-- Main Canvas Area -->
        <div class="flex-1 overflow-hidden">
            @livewire('editor.main-canvas', ['compositeId' => $compositeId])
        </div>
        
        <!-- Right Sidebar - Layers, Adjustments, Details -->
        <div 
            x-data="{ expanded: @entangle('rightSidebarExpanded') }"
            :class="expanded ? 'w-96' : 'w-0'"
            class="transition-all duration-300 flex-none border-l border-slate-700 overflow-hidden bg-slate-800/30 backdrop-blur-sm"
        >
            <div class="h-full flex flex-col" x-show="expanded" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <!-- Tabs -->
                    <div class="flex border-b border-slate-600">
                        <button 
                            wire:click="setActiveRightTab('layers')" 
                            class="flex-1 px-4 py-2 font-medium text-sm text-center {{ $activeRightTab === 'layers' ? 'text-[#3498DB] border-b-2 border-[#3498DB]' : 'text-gray-400 hover:text-white' }}"
                        >
                            Layers
                        </button>
                        <button 
                            wire:click="setActiveRightTab('transforms')" 
                            class="flex-1 px-4 py-2 font-medium text-sm text-center {{ $activeRightTab === 'transforms' ? 'text-[#3498DB] border-b-2 border-[#3498DB]' : 'text-gray-400 hover:text-white' }}"
                        >
                            Transforms
                        </button>
                        <button 
                            wire:click="setActiveRightTab('adjustments')" 
                            class="flex-1 px-4 py-2 font-medium text-sm text-center {{ $activeRightTab === 'adjustments' ? 'text-[#3498DB] border-b-2 border-[#3498DB]' : 'text-gray-400 hover:text-white' }}"
                        >
                            Adjustments
                        </button>
                        <button 
                            wire:click="setActiveRightTab('details')" 
                            class="flex-1 px-4 py-2 font-medium text-sm text-center {{ $activeRightTab === 'details' ? 'text-[#3498DB] border-b-2 border-[#3498DB]' : 'text-gray-400 hover:text-white' }}"
                        >
                            Details
                        </button>
                    </div>
                    
                    <!-- Tab Content -->
                    <div class="flex-1 overflow-hidden flex flex-col">
                        <div style="{{ $activeRightTab === 'layers' ? '' : 'display: none;' }}" class="h-full flex flex-col">
                            @livewire('editor.layer-panel', ['compositeId' => $compositeId], key('layer-panel-' . $compositeId))
                        </div>
                        <div style="{{ $activeRightTab === 'transforms' ? '' : 'display: none;' }}" class="h-full">
                            @livewire('editor.transform-panel', key('transform-panel-' . $compositeId))
                        </div>
                        <div style="{{ $activeRightTab === 'adjustments' ? '' : 'display: none;' }}" class="h-full">
                            @livewire('editor.feature-adjustment-panel', key('adjustment-panel-' . $compositeId))
                        </div>
                        <div style="{{ $activeRightTab === 'details' ? '' : 'display: none;' }}" class="h-full">
                            @livewire('editor.composite-details-panel', ['compositeId' => $compositeId], key('details-panel-' . $compositeId))
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
