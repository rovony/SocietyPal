<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\AssetsCategory;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddAssetCategory extends Component
{
    use LivewireAlert;

    public $name;

    public function submitForm()
    {
        $this->validate([
            'name' => 'required|unique:asset_categories,name,NULL,id,society_id,' . society()->id,
        ]);
        $apartment = new AssetsCategory();
        $apartment->name = $this->name;

        $apartment->save();
        $this->alert('success', __('messages.AssetCategoryAdded'));
        $this->dispatch('hideAddAssetCategory');
        $this->resetForm();
    }

   public function resetForm()
    {
        $this->name = '';
    }
    public function render()
    {
        return view('livewire.forms.add-asset-category');
    }
}
