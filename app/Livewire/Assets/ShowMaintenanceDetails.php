<?php

namespace App\Livewire\Assets;

use Livewire\Component;
use App\Models\AssetMaintenance;
use App\Models\ServiceManagement;
use Aws\Api\Service;

class ShowMaintenanceDetails extends Component
{

    public $maintenanceId;
    public $maintenance_date;
    public $schedule;
    public $status;
    public $assetId;
    public $amount;
    public $notes;
    public $serviceProvider;
    public $serviceProviderName;
    public $assetName;

    public function mount()
    {
        $assetMaintenance = AssetMaintenance::findOrFail($this->maintenanceId);
        $this->serviceProvider =  ServiceManagement::find($assetMaintenance->service_management_id);
        $this->serviceProviderName = $this->serviceProvider ? $this->serviceProvider->contact_person_name : null;
        $this->maintenanceId = $assetMaintenance->id;
        $this->maintenance_date = $assetMaintenance->maintenance_date;
        $this->schedule = $assetMaintenance->schedule;
        $this->status = $assetMaintenance->status;
        $this->assetId = $assetMaintenance->asset_id;
        $this->amount = $assetMaintenance->amount;
        $this->notes = $assetMaintenance->notes;
        $this->assetName = $assetMaintenance->asset->name;
    }


    public function render()
    {
        return view('livewire.assets.show-maintenance-details');
    }
}
