<div class="h-full flex flex-col bg-gray-50">
    <!-- Removed the redundant "Feature Library" heading -->
    
    <!-- Feature Category Selection -->
    <div class="p-3 border-b border-gray-200 bg-white">
        <x-native-select
            label="Feature Category"
            :options="[
                ['name' => 'Eyes', 'value' => 'eyes'],
                ['name' => 'Nose', 'value' => 'nose'],
                ['name' => 'Mouth', 'value' => 'mouth'],
                ['name' => 'Ears', 'value' => 'ears'],
                ['name' => 'Hair', 'value' => 'hair'],
                ['name' => 'Face Shape', 'value' => 'face'],
                ['name' => 'Accessories', 'value' => 'accessories'],
            ]"
            option-label="name"
            option-value="value"
            placeholder="Select a category"
            wire:model.live="selectedCategory"
        />
        
        <!-- Search input -->
        <div class="mt-3">
            <x-input placeholder="Search features..." wire:model.live="search" />
        </div>
    </div>
    
    <!-- Feature Grid -->
    <div class="flex-1 overflow-y-auto p-3">
        <div class="grid grid-cols-2 gap-3">
            <!-- Sample feature items - in a real app, these would be dynamic -->
            @for($i = 1; $i <= 12; $i++)
                <div class="bg-white border border-gray-200 rounded-md overflow-hidden hover:border-[#2C3E50] transition-colors duration-200 cursor-pointer group">
                    <div class="aspect-square bg-gray-100 relative overflow-hidden">
                        <!-- Placeholder image -->
                        <div class="absolute inset-0 flex items-center justify-center bg-gray-200 text-gray-400 text-xs">
                            Feature {{ $i }}
                        </div>
                        
                        <!-- Hover overlay -->
                        <div class="absolute inset-0 bg-[#2C3E50]/60 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                            <button class="bg-white text-[#2C3E50] px-2 py-1 rounded-md text-xs font-medium">
                                Preview
                            </button>
                        </div>
                    </div>
                    <div class="p-2">
                        <p class="text-xs font-medium truncate">Feature {{ $i }}</p>
                        <p class="text-xs text-gray-500 truncate">Category</p>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
