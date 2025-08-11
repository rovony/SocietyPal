<?php

namespace App\Livewire\Assets;

use Livewire\Component;
use Livewire\Attributes\On;

class AssetList extends Component
{
    public $search;
    public $showAddAsset = false;
    public $showFilterButton = true;

    #[On('hideAddAsset')]
    public function hideAddAsset()
    {
        $this->showAddAsset = false;
        $this->js('window.location.reload()');
    }

    #[On('clearAssetFilter')]
    public function clearAssetFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('hideAssetFilters')]
    public function hideAssetFiltersBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.assets.asset-list');
    }
}
