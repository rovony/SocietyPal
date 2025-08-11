<?php

namespace App\Livewire\AssetMaintenances;

use Livewire\Component;
use Livewire\Attributes\On;

class AssetMaintenanceList extends Component
{

    public $search;
    public $showAddMaintenance = false;
    public $showFilterButton = true;

    #[On('hideAddMaintenance')]
    public function hideAddMaintenance()
    {
        $this->showAddMaintenance = false;
        $this->js('window.location.reload()');
    }
    public function hideAddAsset()
    {
        $this->showAddMaintenance = false;
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
        return view('livewire.asset-maintenances.asset-maintenance-list');
    }
}
