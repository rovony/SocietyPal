<?php

namespace App\Livewire\Assets;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\AssetMaintenance;

class ShowMaintenance extends Component
{
    public $assetId;
    public $showAddMaintenance = false;
    public $showMaintenanceDetailsModal = false;
    public $showDeleteMaintenanceModal = false;
    public $selectedMaintenance;
    public $maintenances;
    public $showMaintenancesModal = false;
    public $MaintenanceId;

    public function mount()
    {
        $this->assetId = $this->assetId;
        $this->maintenances = AssetMaintenance::where('asset_id', $this->assetId)->get();
    }

    #[On('hideAddApartmentTenant')]
    public function hideAddApartmentTenant()
    {
        $this->showAddMaintenance = false;
        $this->js('window.location.reload()');
    }


    public function showEditMaintenance($id)
    {
        $this->selectedMaintenance = $id;
        $this->showMaintenanceDetailsModal = true;
    }

    #[On('hideEditMaintenance')]
    public function hideEditMaintenance()
    {
        $this->showMaintenanceDetailsModal = false;
        $this->selectedMaintenance = null;
        $this->js('window.location.reload()');
    }

    public function showMaintenances($id)
    {
        $this->MaintenanceId = $id;
        $this->showMaintenancesModal = true;
    }

    public function render()
    {
        return view('livewire.assets.show-maintenance');
    }
}
