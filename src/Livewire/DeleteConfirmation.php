<?php
namespace Danir\MediaLib\Livewire; 

use Livewire\Component;

class DeleteConfirmation extends Component
{
    public $isOpen = false;
    public $itemToDelete = null;

    protected $listeners = ['showDeleteModal' => 'openModal'];

    public function openModal($itemIds)
    {
        $this->itemToDelete = $itemIds;
        $this->isOpen = true;
    }
    
    

    public function deleteItem()
    {
        \Log::info("deleteItem called with ID: {$this->itemToDelete}"); // Log the item ID
    
        // Send an event with the ID of the item to be deleted
        $this->dispatch('deleteItemConfirmed', ['itemId' => $this->itemToDelete]);
        \Log::info("deleteItemConfirmed event dispatched with ID: {$this->itemToDelete}");
    
        $this->isOpen = false;
    }
    
    
    

    public function render()
    {
        return view('livewire.delete-confirmation');
    }
}
