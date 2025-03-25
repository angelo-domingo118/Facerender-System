<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class DeleteModalManager extends Component
{
    public $showModal = false;
    public $title = 'Delete Item';
    public $message = 'Are you sure you want to delete this item?';
    public $confirmText = 'Delete';
    public $targetComponent = null;
    public $targetId = null;
    public $targetType = null;
    
    #[On('show-delete-modal')]
    public function showDeleteModal($data)
    {
        $this->showModal = true;
        $this->title = $data['title'] ?? 'Delete Item';
        $this->message = $data['message'] ?? 'Are you sure you want to delete this item?';
        $this->confirmText = $data['confirmText'] ?? 'Delete';
        $this->targetComponent = $data['id'] ?? null;
        $this->targetId = $data['targetId'] ?? null;
        $this->targetType = $data['type'] ?? null;
    }
    
    public function cancel()
    {
        $this->showModal = false;
        
        $this->dispatch('reset-modal-state')->self();
    }
    
    #[On('reset-modal-state')]
    public function resetModalState()
    {
        $this->reset(['title', 'message', 'confirmText', 'targetComponent', 'targetId', 'targetType']);
    }
    
    public function confirm()
    {
        $this->dispatch('confirm-delete', [
            'id' => $this->targetComponent,
            'targetId' => $this->targetId,
            'type' => $this->targetType
        ]);
        
        $this->showModal = false;
        
        $this->dispatch('reset-modal-state')->self();
    }
    
    public function render()
    {
        return view('livewire.delete-modal-manager');
    }
}
