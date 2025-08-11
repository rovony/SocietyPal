<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\AssetsCategory;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AssetCategory extends Component
{
    use LivewireAlert;

    protected $listeners = ['refreshAssetCategories' => 'mount'];

    public $assetCategories;
    public $assetCategory;
    public $showEditAssetCategoryModal = false;
    public $showAddAssetCategoryModal = false;
    public $confirmDeleteAssetCategoryModal = false;

    public function mount()
    {
        $this->assetCategories = AssetsCategory::get();
    }

    public function showAddAssetCategory()
    {
        $this->showAddAssetCategoryModal = true;
    }

    public function showEditAssetCategory($id)
    {
        $this->assetCategory = AssetsCategory::findOrFail($id);
        $this->showEditAssetCategoryModal = true;
    }

    public function showDeleteAssetCategory($id)
    {
        $this->assetCategory = AssetsCategory::findOrFail($id);
        $this->confirmDeleteAssetCategoryModal = true;
    }

    public function deleteAssetCategory($id)
    {
        AssetsCategory::destroy($id);

        $this->confirmDeleteAssetCategoryModal = false;
        $this->assetCategory = '';
        $this->dispatch('refreshAssetCategories');

        $this->alert('success', __('messages.assetCategoryDeleted'));
    }

    #[On('hideEditAssetCategory')]
    public function hideEditAssetCategory()
    {
        $this->showEditAssetCategoryModal = false;
        $this->dispatch('refreshAssetCategories');
    }

    #[On('hideAddAssetCategory')]
    public function hideAddAssetCategory()
    {
        $this->showAddAssetCategoryModal = false;
        $this->dispatch('refreshAssetCategories');
    }

    public function render()
    {
        return view('livewire.settings.asset-category');
    }
}

