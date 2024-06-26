<?php

namespace App\Livewire\Admin\ModalSelect;

use Livewire\Component;
use Livewire\WithFileUploads;
use Danir\MediaLib\Models\Picture;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModalSelect extends Component
{
    use WithFileUploads;
    
    public $isOpen = false;
    public $selectedPictureUrl;
    public $instanceId; // Define the instanceId variable

    protected $listeners = ['openModal' => 'showModal', 'pictureSelected' => 'storeSelectedPicture'];

    public function mount()
    {
        $this->instanceId = Str::random(8); // Assign a unique identifier
    }

    public function showModal()
    {
        $this->isOpen = true;
    }

    public function storeSelectedPicture($imageUrl)
    {
        $this->selectedPictureUrl = $imageUrl;
    }

    public function render()
    {
        $pictures = Picture::paginate(30);
        return view('livewire.admin.modalselect.modalselect', compact('pictures'))->layout('components.admin');
    }
}
