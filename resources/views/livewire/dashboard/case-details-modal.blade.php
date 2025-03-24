<div>
    <x-modal wire:model.defer="show" max-width="5xl">
        @if($case)
            <x-slot name="title">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center">
                        <span class="text-xl font-semibold text-gray-800">Case Details</span>
                        <x-badge 
                            :label="ucfirst($case->status)" 
                            :color="$case->status === 'active' ? 'positive' : ($case->status === 'pending' ? 'warning' : 'negative')"
                            class="ml-3"
                        />
                    </div>
                    <x-button icon="x" wire:click="close" flat rounded class="!p-1.5" />
                </div>
            </x-slot>
            
            <div class="space-y-6">
                <!-- Case Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-800 mb-3">{{ $case->title }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 font-medium">Reference Number:</span>
                            <span class="text-gray-800 ml-1">#{{ $case->reference_number }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-medium">Status:</span>
                            <span class="text-gray-800 ml-1">{{ ucfirst($case->status) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-medium">Incident Type:</span>
                            <span class="text-gray-800 ml-1">{{ $case->incident_type }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-medium">Incident Date:</span>
                            <span class="text-gray-800 ml-1">{{ $case->incident_date->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-medium">Location:</span>
                            <span class="text-gray-800 ml-1">{{ $case->location }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-medium">Created On:</span>
                            <span class="text-gray-800 ml-1">{{ $case->created_at->format('M d, Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 font-medium">Last Updated:</span>
                            <span class="text-gray-800 ml-1">{{ $case->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Case Description -->
                <div>
                    <h4 class="text-gray-700 font-medium mb-2">Description</h4>
                    <div class="bg-gray-50 rounded-lg p-4 text-gray-700">
                        {{ $case->description ?? 'No description available.' }}
                    </div>
                </div>
                
                <!-- Tabs -->
                <x-tabs>
                    <!-- Composites Tab -->
                    <x-tab name="composites" label="Composites" icon="photo">
                        <div class="py-4">
                            @if($case->composites->isEmpty())
                                <div class="text-center py-8">
                                    <x-icon name="photo" class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                                    <p class="text-gray-500">No composites found for this case</p>
                                    <x-button flat icon="plus" label="Create Composite" class="mt-3" />
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($case->composites as $composite)
                                        <div class="bg-white rounded-lg shadow p-3">
                                            <div class="relative pb-2/3 w-full bg-gray-100 rounded mb-2 overflow-hidden">
                                                @if($composite->image_path)
                                                    <img
                                                        src="{{ Storage::url($composite->image_path) }}"
                                                        alt="{{ $composite->title }}"
                                                        class="absolute h-full w-full object-cover"
                                                    />
                                                @else
                                                    <div class="absolute inset-0 flex items-center justify-center">
                                                        <x-icon name="photo" class="h-8 w-8 text-gray-300" />
                                                    </div>
                                                @endif
                                            </div>
                                            <h5 class="font-medium">{{ $composite->title }}</h5>
                                            <p class="text-xs text-gray-500">Created: {{ $composite->created_at->format('M d, Y') }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </x-tab>
                    
                    <!-- Witnesses Tab -->
                    <x-tab name="witnesses" label="Witnesses" icon="users">
                        <div class="py-4">
                            @if($case->witnesses->isEmpty())
                                <div class="text-center py-8">
                                    <x-icon name="users" class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                                    <p class="text-gray-500">No witnesses added to this case</p>
                                    <x-button flat icon="plus" label="Add Witness" class="mt-3" />
                                </div>
                            @else
                                <div class="bg-white rounded-lg overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($case->witnesses as $witness)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">{{ $witness->name }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-500">{{ $witness->contact_info }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm text-gray-500">{{ $witness->created_at->format('M d, Y') }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <div class="flex space-x-2">
                                                            <x-button icon="eye" sm flat rounded class="!p-1.5" />
                                                            <x-button icon="pencil" sm flat rounded class="!p-1.5" />
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </x-tab>
                    
                    <!-- Notes & Documents Tab -->
                    <x-tab name="documents" label="Notes & Documents" icon="document-text">
                        <div class="py-4">
                            <div class="mb-6">
                                <h4 class="text-gray-700 font-medium mb-2">Case Notes</h4>
                                @if($case->notes && $case->notes->isNotEmpty())
                                    <div class="space-y-3">
                                        @foreach($case->notes as $note)
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <div class="flex justify-between items-start mb-2">
                                                    <span class="font-medium">{{ $note->title }}</span>
                                                    <span class="text-xs text-gray-500">{{ $note->created_at->format('M d, Y') }}</span>
                                                </div>
                                                <p class="text-sm text-gray-700">{{ $note->content }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-6 bg-gray-50 rounded-lg">
                                        <p class="text-gray-500">No notes available</p>
                                        <x-button flat icon="plus" label="Add Note" class="mt-2" />
                                    </div>
                                @endif
                            </div>
                            
                            <div>
                                <h4 class="text-gray-700 font-medium mb-2">Documents</h4>
                                @if($case->documents && $case->documents->isNotEmpty())
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($case->documents as $document)
                                            <div class="flex items-center bg-gray-50 rounded-lg p-3">
                                                <x-icon name="document" class="h-8 w-8 text-gray-400 mr-3" />
                                                <div class="flex-1">
                                                    <span class="font-medium text-sm">{{ $document->title }}</span>
                                                    <p class="text-xs text-gray-500">{{ $document->created_at->format('M d, Y') }}</p>
                                                </div>
                                                <x-button icon="arrow-down-tray" sm flat rounded class="!p-1.5" />
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-6 bg-gray-50 rounded-lg">
                                        <p class="text-gray-500">No documents attached</p>
                                        <x-button flat icon="arrow-up-tray" label="Upload Document" class="mt-2" />
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-tab>
                </x-tabs>
            </div>
            
            <x-slot name="footer">
                <div class="flex justify-between items-center">
                    <x-button flat label="Close" wire:click="close" />
                    <div class="flex space-x-3">
                        <x-button negative icon="trash" label="Delete Case" />
                        <x-button positive icon="pencil" label="Edit Case" />
                    </div>
                </div>
            </x-slot>
        @else
            <div class="py-8 text-center">
                <x-icon name="exclamation-circle" class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                <p class="text-gray-500">Case not found or has been deleted.</p>
                <x-button flat label="Close" wire:click="close" class="mt-3" />
            </div>
        @endif
    </x-modal>
</div>
