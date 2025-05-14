<div class="h-full flex flex-col bg-slate-800/60">
    <!-- Main Content Area with Scrolling -->
    <div class="flex-1 overflow-y-auto p-3 space-y-5">
        <!-- Basic Info Section -->
        <div class="bg-slate-700/70 rounded-lg shadow-lg border border-slate-600">
            <div class="px-4 py-3 bg-slate-800/60 border-b border-slate-600 rounded-t-lg">
                <h4 class="text-sm font-semibold text-slate-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Basic Information
                </h4>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-200 mb-1">Title</label>
                    <div class="relative">
                        <input
                            id="title"
                            type="text"
                            wire:model.live="title"
                            placeholder="Enter composite title"
                            class="w-full bg-slate-600 border border-slate-500 text-slate-200 placeholder-slate-400 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500"
                        />
                    </div>
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-200 mb-1">Description</label>
                    <div class="relative">
                        <textarea
                            id="description"
                            wire:model.live="description"
                            placeholder="Enter description"
                            rows="3"
                            class="w-full bg-slate-600 border border-slate-500 text-slate-200 placeholder-slate-400 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500"
                        ></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Witness Information Section -->
        <div class="bg-slate-700/70 rounded-lg shadow-lg border border-slate-600">
            <div class="px-4 py-3 bg-slate-800/60 border-b border-slate-600 rounded-t-lg">
                <h4 class="text-sm font-semibold text-slate-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Witness Information
                </h4>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label for="witness" class="block text-sm font-medium text-slate-200 mb-1">Witness</label>
                    <select
                        id="witness"
                        wire:model.live="witnessId"
                        class="w-full bg-slate-600 border border-slate-500 text-slate-200 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 appearance-none"
                        style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3E%3Cpath stroke=\'%2394a3b8\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/%3E%3C/svg%3E'); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                    >
                        <option value="">Select witness</option>
                        @foreach($witnesses->map(function($witness) { return ['name' => $witness->name, 'value' => $witness->id]; }) as $witness)
                            <option value="{{ $witness['value'] }}">{{ $witness['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Suspect Details Section -->
        <div class="bg-slate-700/70 rounded-lg shadow-lg border border-slate-600">
            <div class="px-4 py-3 bg-slate-800/60 border-b border-slate-600 rounded-t-lg">
                <h4 class="text-sm font-semibold text-slate-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Suspect Details
                </h4>
            </div>
            <div class="p-4 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="gender" class="block text-sm font-medium text-slate-200 mb-1">Gender</label>
                        <select
                            id="gender"
                            wire:model.live="suspectGender"
                            class="w-full bg-slate-600 border border-slate-500 text-slate-200 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 appearance-none"
                            style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3E%3Cpath stroke=\'%2394a3b8\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/%3E%3C/svg%3E'); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                        >
                            <option value="">Select gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Unknown">Unknown</option>
                        </select>
                    </div>
                    <div>
                        <label for="ethnicity" class="block text-sm font-medium text-slate-200 mb-1">Ethnicity</label>
                        <input
                            id="ethnicity"
                            type="text"
                            wire:model.live="suspectEthnicity"
                            placeholder="Ethnicity"
                            class="w-full bg-slate-600 border border-slate-500 text-slate-200 placeholder-slate-400 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500"
                        />
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="age-range" class="block text-sm font-medium text-slate-200 mb-1">Age Range</label>
                        <input
                            id="age-range"
                            type="text"
                            wire:model.live="suspectAgeRange"
                            placeholder="25-35"
                            class="w-full bg-slate-600 border border-slate-500 text-slate-200 placeholder-slate-400 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500"
                        />
                    </div>
                    <div>
                        <label for="height" class="block text-sm font-medium text-slate-200 mb-1">Height</label>
                        <input
                            id="height"
                            type="text"
                            wire:model.live="suspectHeight"
                            placeholder="5'10\""
                            class="w-full bg-slate-600 border border-slate-500 text-slate-200 placeholder-slate-400 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500"
                        />
                    </div>
                </div>
                
                <div>
                    <label for="body-build" class="block text-sm font-medium text-slate-200 mb-1">Body Build</label>
                    <select
                        id="body-build"
                        wire:model.live="suspectBodyBuild"
                        class="w-full bg-slate-600 border border-slate-500 text-slate-200 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 appearance-none"
                        style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3E%3Cpath stroke=\'%2394a3b8\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M6 8l4 4 4-4\'/%3E%3C/svg%3E'); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                    >
                        <option value="">Select body build</option>
                        <option value="Slim">Slim</option>
                        <option value="Average">Average</option>
                        <option value="Athletic">Athletic</option>
                        <option value="Large">Large</option>
                        <option value="Muscular">Muscular</option>
                    </select>
                </div>
                
                <div>
                    <label for="additional-notes" class="block text-sm font-medium text-slate-200 mb-1">Additional Notes</label>
                    <textarea
                        id="additional-notes"
                        wire:model.live="suspectAdditionalNotes"
                        placeholder="Enter any additional details about the suspect"
                        rows="3"
                        class="w-full bg-slate-600 border border-slate-500 text-slate-200 placeholder-slate-400 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500"
                    ></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="p-3 border-t border-slate-600 bg-slate-800/50 backdrop-blur-sm">
        <div class="flex justify-end space-x-2">
            <button 
                wire:click="resetForm" 
                class="px-4 py-2 text-sm font-medium text-slate-300 bg-slate-700 hover:bg-slate-600 active:bg-slate-500 border border-slate-600 rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 focus:ring-offset-slate-800"
            >
                Reset
            </button>
            <button 
                wire:click="updateDetails" 
                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 active:bg-blue-700 border border-blue-600 rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 focus:ring-offset-slate-800"
            >
                <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Update Details
            </button>
        </div>
    </div>
    
    <style>
        /* Just basic scrollbar styling */
        .overflow-y-auto::-webkit-scrollbar {
            width: 8px;
        }
        .overflow-y-auto::-webkit-scrollbar-track {
            background-color: #1e293b; /* slate-800 */
        }
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background-color: #475569; /* slate-600 */
            border-radius: 4px;
            border: 2px solid #1e293b; /* slate-800 */
        }
        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background-color: #64748b; /* slate-500 */
        }
        .overflow-y-auto {
            scrollbar-width: thin;
            scrollbar-color: #475569 #1e293b; /* thumb track */
        }
    </style>
</div>
