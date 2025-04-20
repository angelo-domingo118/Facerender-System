<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

#[Layout('layouts.app')]
class UserManagement extends Component
{
    use WireUiActions;
    use WithPagination;

    public ?User $selectedUser = null;
    
    #[Locked] 
    public ?int $selectedUserId = null; // Changed from protected to public for wire:model binding
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showPasswordModal = false;
    public bool $showDeleteModal = false;

    // Form state for creating/editing
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $is_admin = false;

    protected function rules(): array
    {
        $userId = $this->selectedUser?->id;

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'is_admin' => 'boolean',
            'password' => [$userId ? 'nullable' : 'required', 'string', Password::defaults(), 'confirmed'],
        ];
    }

    // Add specific rules for editing (without password)
    protected function editUserRules(): array
    {
        $userId = $this->selectedUser?->id;
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'is_admin' => 'boolean',
        ];
    }

    protected function passwordModalRules(): array
    {
        return [
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ];
    }

    public function createUser(): void
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function manualCreateUser(): void
    {
        try {
            // Validate inputs
            $validated = $this->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
                'is_admin' => 'boolean',
            ]);
            
            // Create the user
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_admin' => $validated['is_admin'],
                'email_verified_at' => now(),
            ]);
            
            // Success notification
            $this->notification()->success(
                title: 'Success',
                description: 'User created successfully'
            );
            
            // Reset form and close modal
            $this->showCreateModal = false;
            $this->resetCreateForm();
            
        } catch (Exception $e) {
            $this->notification()->error(
                title: 'Creation Failed',
                description: 'Error: ' . $e->getMessage()
            );
        }
    }

    public function saveUser(): void
    {
        $validated = $this->validate();
        
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => $validated['is_admin'],
            'email_verified_at' => now(),
        ]);

        $this->showCreateModal = false;
        $this->notification()->success(
            title: 'User Created',
            description: 'The new user was successfully created.'
        );
        $this->resetCreateForm();
    }

    public function editUser(User $user): void
    {
        // Store user ID first to ensure it's set before anything else
        $this->selectedUserId = $user->id;
        
        // Set the rest of the properties
        $this->selectedUser = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_admin = (bool) $user->is_admin;
        $this->password = ''; // Clear password fields
        $this->password_confirmation = '';
        $this->resetErrorBag();
        
        $this->showEditModal = true;
    }

    public function updateUser(): void
    {
        logger()->info('updateUser method called.');
        
        if (!$this->selectedUser) {
            logger()->warning('updateUser called but selectedUser is null.');
            $this->notification()->error(
                title: 'Error',
                description: 'No user selected for update.'
            );
            return;
        }

        logger()->info('Validating user data for update, user ID: ' . $this->selectedUser->id);
        
        try {
            // Use the specific edit rules
            $validated = $this->validate($this->editUserRules());
            logger()->info('Validation passed in updateUser.', $validated);

            // UpdateData now only includes fields present in the edit form
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'is_admin' => $validated['is_admin'],
            ];

            logger()->info('Attempting to update user ID: ' . $this->selectedUser->id, $updateData);
            
            $this->selectedUser->update($updateData);
            logger()->info('User ID: ' . $this->selectedUser->id . ' updated successfully.');
            
            // Success notification
            $this->notification()->success(
                title: 'User Updated',
                description: 'User details were successfully updated.'
            );
            
            // Reset form and close modal
            $this->resetCreateForm();
            $this->showEditModal = false;
        } 
        catch (Exception $e) {
            logger()->error('Error updating user ID: ' . $this->selectedUser->id . ' - ' . $e->getMessage());
            $this->notification()->error(
                title: 'Update Failed',
                description: 'An error occurred while updating the user: ' . $e->getMessage()
            );
        }
    }

    public function changePassword(User $user): void
    {
        $this->selectedUser = $user;
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetErrorBag();
        $this->showPasswordModal = true;
    }

    public function updatePassword(): void
    {
        // Check for valid user
        if (!$this->selectedUser) {
            $this->notification()->error(
                title: 'Error',
                description: 'No user selected for password update. Please try again.'
            );
            return;
        }
        
        try {
            // Validate the password
            $validated = $this->validate($this->passwordModalRules());
            
            // Update the user's password
            $this->selectedUser->update([
                'password' => Hash::make($validated['password']),
            ]);
            
            // Show success notification and close modal
            $this->notification()->success(
                title: 'Success',
                description: 'Password was updated successfully.'
            );
            
            // Reset form and close modal
            $this->showPasswordModal = false;
            $this->reset(['password', 'password_confirmation', 'selectedUser', 'selectedUserId']);
            $this->resetErrorBag();
            
        } catch (Exception $e) {
            $this->notification()->error(
                title: 'Password Update Failed',
                description: 'Error: ' . $e->getMessage()
            );
        }
    }

    public function confirmDeleteUser(int $userId): void
    {
        // Prevent deleting the currently logged-in user
        if (Auth::id() === $userId) {
            $this->notification()->error(
                title: 'Cannot Delete Self',
                description: 'You cannot delete your own account.'
            );
            return;
        }
        
        // Find the user and set as selected user
        $user = User::find($userId);
        if (!$user) { 
            $this->notification()->error(
                title: 'Error',
                description: 'User not found.'
            );
            return;
        }

        // Set the selected user and show the modal
        $this->selectedUser = $user;
        $this->selectedUserId = $userId;
        $this->showDeleteModal = true;
    }

    public function deleteUser(): void
    {
        // Check if we have a selected user
        if (!$this->selectedUserId) {
            $this->notification()->error(
                title: 'Error',
                description: 'No user selected for deletion.'
            );
            return;
        }
        
        $userId = $this->selectedUserId;
        
        // Extra check to prevent deleting self
        if (Auth::id() === $userId) {
            $this->notification()->error(
                title: 'Cannot Delete Self',
                description: 'You cannot delete your own account.'
            );
            $this->showDeleteModal = false;
            return;
        }
        
        try {
            $user = User::findOrFail($userId);
            $user->delete();

            $this->notification()->success(
                title: 'User Deleted',
                description: 'The user was successfully deleted.'
            );
            
            // Close the modal and reset
            $this->showDeleteModal = false;
            $this->reset(['selectedUser', 'selectedUserId']);
        } catch (Exception $e) {
            $this->notification()->error(
                title: 'Delete Failed',
                description: 'Error: ' . $e->getMessage()
            );
        }
    }

    public function resetCreateForm(): void
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'is_admin', 'selectedUser', 'selectedUserId']);
        $this->resetErrorBag();
    }

    public function manualUpdateUser(): void
    {
        // Check for valid user ID
        if ($this->selectedUserId === null) {
            $this->notification()->error(
                title: 'Error',
                description: 'No user ID found for update. Please try again.'
            );
            return;
        }
        
        // Get the ID we're working with
        $userId = $this->selectedUserId;
        
        // Try to find the user
        $user = User::find($userId);
        if (!$user) {
            $this->notification()->error(
                title: 'Error',
                description: "User not found. Please refresh and try again."
            );
            return;
        }
        
        // Validate inputs
        try {
            $validated = $this->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
                'is_admin' => 'boolean',
            ]);
            
            // Update the user
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->is_admin = $validated['is_admin'];
            $user->save();
            
            // Notify success and close modal
            $this->notification()->success(
                title: 'Success',
                description: "User updated successfully"
            );
            
            // Reset and close modal
            $this->showEditModal = false;
            $this->reset(['name', 'email', 'is_admin', 'selectedUser', 'selectedUserId']);
            $this->resetErrorBag();
            
        } catch (Exception $e) {
            $this->notification()->error(
                title: 'Update Failed',
                description: 'Error: ' . $e->getMessage()
            );
        }
    }

    public function manualUpdatePassword(): void
    {
        // Check for valid user ID
        if ($this->selectedUserId === null && $this->selectedUser === null) {
            $this->notification()->error(
                title: 'Error',
                description: 'No user selected for password update. Please try again.'
            );
            return;
        }
        
        // Get the user directly if possible
        $userId = $this->selectedUserId ?? $this->selectedUser->id;
        $user = User::find($userId);
        
        if (!$user) {
            $this->notification()->error(
                title: 'Error',
                description: 'User not found. Please refresh and try again.'
            );
            return;
        }
        
        try {
            // Validate password
            $validated = $this->validate([
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
            
            // Update password directly
            $user->password = Hash::make($validated['password']);
            $user->save();
            
            // Success notification
            $this->notification()->success(
                title: 'Success',
                description: 'Password updated successfully'
            );
            
            // Reset and close modal
            $this->showPasswordModal = false;
            $this->reset(['password', 'password_confirmation', 'selectedUser', 'selectedUserId']);
            $this->resetErrorBag();
            
        } catch (Exception $e) {
            $this->notification()->error(
                title: 'Password Update Failed',
                description: 'Error: ' . $e->getMessage()
            );
        }
    }

    public function render()
    {
        $users = User::paginate(10);
        return view('livewire.admin.user-management', [
            'users' => $users,
        ]);
    }
}
