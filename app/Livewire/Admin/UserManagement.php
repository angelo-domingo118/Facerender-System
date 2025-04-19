<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

#[Layout('layouts.app')]
class UserManagement extends Component
{
    use WireUiActions;
    use WithPagination;

    public ?User $selectedUser = null;
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showPasswordModal = false;

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

    public function saveUser(): void
    {
        $validated = $this->validate();
        
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => $validated['is_admin'],
            'email_verified_at' => now(), // Or handle verification differently
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
        logger()->info('editUser called for user ID: ' . $user->id);
        $this->selectedUser = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_admin = (bool) $user->is_admin;
        $this->password = ''; // Clear password fields
        $this->password_confirmation = '';
        $this->resetErrorBag();
        $this->showEditModal = true;
        logger()->info('showEditModal set to: ' . ($this->showEditModal ? 'true' : 'false'));
    }

    public function updateUser(): void
    {
        logger()->info('updateUser method called.');
        if (!$this->selectedUser) {
            logger()->warning('updateUser called but selectedUser is null.');
            return;
        }

        // Use the specific edit rules
        $validated = $this->validate($this->editUserRules()); 
        logger()->info('Validation passed in updateUser.', $validated);

        // UpdateData now only includes fields present in the edit form
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_admin' => $validated['is_admin'],
        ];

        // Remove the check for password as it's not part of the edit form
        // if (!empty($validated['password'])) {
        //     $updateData['password'] = Hash::make($validated['password']);
        // }

        $this->selectedUser->update($updateData);

        $this->showEditModal = false;
        $this->notification()->success(
            title: 'User Updated',
            description: 'User details were successfully updated.'
        );
        $this->resetCreateForm();
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
        if (!$this->selectedUser) {
            return;
        }

        $validated = $this->validate($this->passwordModalRules());

        $this->selectedUser->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->showPasswordModal = false;
        $this->notification()->success(
            title: 'Password Updated',
            description: 'User password was successfully updated.'
        );
        $this->resetCreateForm();
    }

    public function confirmDeleteUser(int $userId): void
    {
        // Remove Temporary Debugging
        // logger()->info('confirmDeleteUser called with ID: ' . $userId);
        // $this->dialog()->success('Test Dialog', 'Confirm delete was called!'); 
        // End Temporary Debugging
        
        // Prevent deleting the currently logged-in user
        if (auth()->user()?->id === $userId) {
            $this->notification()->error(
                title: 'Cannot Delete Self',
                description: 'You cannot delete your own account.'
            );
            return;
        }
        
        // Fetch the user temporarily just to get the name/email for the message
        $user = User::find($userId);
        if (!$user) { 
            $this->notification()->error('User not found');
            return;
        }
        
        $this->dialog()->confirm([
            'title'       => 'Are you Sure?',
            'description' => 'Do you want to delete the user '.$user->name.' ('.$user->email.')?',
            'icon'        => 'warning',
            'accept'      => [
                'label'  => 'Yes, delete it',
                'method' => 'deleteUser',
                'params' => $userId,
            ],
            'reject' => [
                'label'  => 'No, cancel',
            ],
        ]);
    }

    public function deleteUser(int $userId): void
    {
        // Extra check to prevent deleting self
        if (auth()->user()?->id === $userId) {
            $this->notification()->error(
                title: 'Cannot Delete Self',
                description: 'You cannot delete your own account.'
            );
            return;
        }
        
        $user = User::findOrFail($userId);
        $user->delete();

        $this->notification()->success(
            title: 'User Deleted',
            description: 'The user was successfully deleted.'
        );
    }

    public function resetCreateForm(): void
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'is_admin', 'selectedUser']);
        $this->resetErrorBag();
    }

    public function render()
    {
        $users = User::paginate(10);
        return view('livewire.admin.user-management', [
            'users' => $users,
        ]);
    }
}
