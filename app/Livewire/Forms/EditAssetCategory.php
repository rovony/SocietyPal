<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditAssetCategory extends Component
{
    use LivewireAlert;

    public $category;
    public $categoryName;
    public $categoryId;

    public function mount()
    {
        $this->categoryName = $this->category->name;
        $this->categoryId = $this->category->id;
    }

    public function submitForm()
    {
        $this->validate([
            'categoryName' => [
                'required',
                Rule::unique('asset_categories', 'name')
                    ->where('society_id', society()->id)
                    ->ignore($this->categoryId)
            ]
        ]);

        $this->category->name = $this->categoryName;
        $this->category->save();

        $this->alert('success', __('messages.AssetCategoryUpdated'));

        $this->dispatch('hideEditAssetCategory');
    }

    public function render()
    {
        return view('livewire.forms.edit-asset-category');
    }
}
