<div> {{-- Actual content starts here --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#2C3E50] border border-[#3498DB]/20 overflow-hidden shadow-xl rounded-lg">
                <div class="p-6 lg:p-8 bg-[#2C3E50]">
                    
                    {{-- Add title and button container --}}
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
                            {{ __('User Management') }}
                        </h2>
                        <x-button wire:click="createUser" primary label="Add New User" icon="plus" />
                    </div>
                    {{-- End title and button container --}}

                    <!-- User Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#34495E]">
                            <thead class="bg-[#243342]">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Role</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Created At</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-[#2C3E50] divide-y divide-[#34495E]">
                                @forelse ($users as $user)
                                    <tr wire:key="{{ $user->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-100">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                            <span @class([
                                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                'bg-green-100 text-green-800' => $user->is_admin,
                                                'bg-blue-100 text-blue-800' => !$user->is_admin,
                                            ])>
                                                {{ $user->is_admin ? 'Admin' : 'User' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <x-button circle wire:click="editUser({{ $user->id }})" info icon="pencil" title="Edit" />
                                            <x-button circle wire:click="changePassword({{ $user->id }})" secondary icon="key" title="Change Password" />
                                            @if(auth()->id() !== $user->id)
                                                <x-button 
                                                    circle 
                                                    wire:click="confirmDeleteUser({{ $user->id }})" 
                                                    negative 
                                                    icon="trash" 
                                                    title="Delete" 
                                                />
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    <x-modal 
        wire:model.defer="showCreateModal" 
        x-on:close="$wire.resetCreateForm()"
        max-width="lg"
        blur
        align="center"
    >
        <x-card title="Create New User">
            <div class="grid grid-cols-1 gap-4">
                <x-input wire:model.live="name" label="Name" placeholder="Enter user name" id="create-name" />
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                
                <x-input wire:model.live="email" label="Email" placeholder="Enter user email" type="email" id="create-email" />
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                
                <x-input wire:model.defer="password" label="Password" placeholder="Enter new password" type="password" id="create-password" />
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                
                <x-input wire:model.defer="password_confirmation" label="Confirm Password" placeholder="Confirm new password" type="password" id="create-password-confirmation" />
                @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                
                <x-checkbox wire:model.live="is_admin" id="create-is-admin" label="Is Admin" />
                @error('is_admin') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <x-slot name="footer">
                <div class="flex justify-between items-center w-full">
                    <div class="text-red-500 text-sm">
                        @if($errors->any())
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="flex gap-x-4">
                        <x-button flat label="Cancel" x-on:click="close" />
                        <x-button 
                            wire:click="manualCreateUser"
                            primary 
                            label="Save User" 
                            wire:loading.attr="disabled" 
                            wire:loading.class="opacity-75"
                            wire:target="manualCreateUser"
                            icon="check"
                        />
                    </div>
                </div>
            </x-slot>
        </x-card>
    </x-modal>
    
    <!-- Edit User Modal -->
    <x-modal 
        wire:model="showEditModal"
        max-width="lg"
        blur
        align="center"
        x-on:close="$wire.$refresh()"
    >
        <x-card title="Edit User">
            <div class="grid grid-cols-1 gap-4">
                <x-input wire:model.live="name" label="Name" placeholder="Enter user name" id="edit-name" />
                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                
                <x-input wire:model.live="email" label="Email" placeholder="Enter user email" type="email" id="edit-email" />
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                
                <x-checkbox wire:model.live="is_admin" id="edit-is-admin" label="Is Admin" /> 
                @error('is_admin') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <x-slot name="footer">
                <div class="flex justify-between items-center w-full">
                    <div class="text-red-500 text-sm">
                        @if($errors->any())
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="flex gap-x-4">
                        <x-button 
                            flat 
                            label="Cancel" 
                            x-on:click="close" 
                        />
                        <x-button 
                            wire:click="manualUpdateUser"
                            primary 
                            label="Update User" 
                            wire:loading.attr="disabled" 
                            wire:loading.class="opacity-75"
                            wire:target="manualUpdateUser"
                            icon="check"
                        />
                    </div>
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <!-- Change Password Modal -->
    <x-modal 
        wire:model="showPasswordModal" 
        max-width="lg"
        blur
        align="center"
        x-on:close="$wire.$refresh()"
    >
        <x-card title="Change Password for {{ $selectedUser?->name }}">
            <div class="grid grid-cols-1 gap-4">
                <x-input wire:model.defer="password" label="New Password" placeholder="Enter new password" type="password" id="change-password" />
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                
                <x-input wire:model.defer="password_confirmation" label="Confirm New Password" placeholder="Confirm new password" type="password" id="change-password-confirmation" />
                @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <x-slot name="footer">
                <div class="flex justify-between items-center w-full">
                    <div class="text-red-500 text-sm">
                        @if($errors->any())
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="flex gap-x-4">
                        <x-button 
                            flat 
                            label="Cancel" 
                            x-on:click="close" 
                        />
                        <x-button 
                            wire:click="manualUpdatePassword"
                            primary 
                            label="Update Password" 
                            wire:loading.attr="disabled" 
                            wire:loading.class="opacity-75"
                            wire:target="manualUpdatePassword"
                            icon="check"
                        />
                    </div>
                </div>
            </x-slot>
        </x-card>
    </x-modal>

    <!-- Delete User Modal -->
    <x-modal 
        wire:model="showDeleteModal" 
        max-width="md"
        blur
        align="center"
    >
        <x-card title="Confirm Delete">
            <div class="p-4 text-center">
                <x-icon name="trash" class="w-12 h-12 mx-auto text-red-500" />
                <h3 class="mt-4 text-lg font-medium text-gray-900">Delete User</h3>
                <p class="mt-2 text-sm text-gray-600">
                    Are you sure you want to delete {{ $selectedUser?->name }}? This action cannot be undone.
                </p>
            </div>
            
            <x-slot name="footer">
                <div class="flex justify-center gap-x-4">
                    <x-button flat label="Cancel" x-on:click="close" />
                    <x-button 
                        wire:click="deleteUser"
                        negative 
                        label="Delete User" 
                        wire:loading.attr="disabled" 
                        wire:loading.class="opacity-75"
                        wire:target="deleteUser"
                        icon="trash"
                    />
                </div>
            </x-slot>
        </x-card>
    </x-modal>
</div>
