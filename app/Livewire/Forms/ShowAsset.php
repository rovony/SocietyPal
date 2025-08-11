<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowAsset extends Component
{
    use LivewireAlert, WithFileUploads;

    public $asset;
    public $name;
    public $category;
    public $location;
    public $condition;
    public $owner_id;
    public $tenant_id;
    public $documentPath;
    public $purchaseDate;




    public function mount()
    {
        $this->asset = $this->asset;
        $this->name = $this->asset->name;
        $this->category = $this->asset->category->name;
        $this->location = $this->asset->location;
        $this->condition = $this->asset->condition;
        $this->documentPath = $this->asset->photo_url;
        $this->purchaseDate = $this->asset->purchase_date;

    }
    


    public function render()
    {
        return view('livewire.forms.show-asset');
    }
}
