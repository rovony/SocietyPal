<?php

namespace App\Livewire\Assets;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\AssetManagement;
use App\Models\AssetMaintenance;
use App\Models\ServiceManagement;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditAssetMaintenance extends Component
{
    use LivewireAlert;


    public $maintenance_id;
    public $maintenance_date;
    public $schedule;
    public $status;
    public $amount;
    public $serviceId;
    public $notes;
    public $serviceManagement;
    public $assetId;
    public $maintenanceId;
    public $assetsApartment = [];
    public $assets;
    public $reminder;



    public function mount()
    {
        $this->serviceManagement = ServiceManagement::all();


        $maintenance = AssetMaintenance::findOrFail($this->maintenanceId);
        $this->maintenance_id = $maintenance->id;
        $this->maintenance_date = Carbon::parse($maintenance->maintenance_date)->format('Y-m-d');
        $this->schedule = $maintenance->schedule;
        $this->status = $maintenance->status;
        $this->amount = $maintenance->amount;
        $this->serviceId = $maintenance->service_management_id;
        $this->notes = $maintenance->notes;
        $this->assetId = $maintenance->asset_id;
        $this->reminder = (bool) $maintenance->reminder;
        $loggedInUser = user()->id;
        if (user_can('Create Assets')) {
            $this->assetsApartment = AssetManagement::all();
        } else {
            $this->assetsApartment = AssetManagement::whereHas('apartments', function ($query) use ($loggedInUser) {
                $query->where('user_id', $loggedInUser);
            })->orWhereHas('apartments', function ($query) use ($loggedInUser) {
                $query->where('status', 'rented')
                    ->whereHas('tenants', function ($q) use ($loggedInUser) {
                        $q->where('user_id', $loggedInUser);
                    });
            })->get();
        }


    }


    public function updateMaintenance()
    {
        $this->validate([
            'maintenance_date' => 'required',
            'schedule' => 'required',
            'status' => 'required',
        ]);

        $maintenance = AssetMaintenance::findOrFail($this->maintenance_id);
        $maintenance->maintenance_date = Carbon::parse($this->maintenance_date)->format('Y-m-d');
        $maintenance->schedule = $this->schedule;
        $maintenance->status = $this->status;
        $maintenance->amount = $this->amount;
        $maintenance->service_management_id = $this->serviceId;
        $maintenance->notes = $this->notes;
        $maintenance->reminder = $this->reminder;
        $maintenance->save();

        $this->alert('success', __('messages.AssetUpdated'));
        $this->dispatch('hideEditMaintenance');

    }
    public function render()
    {
        return view('livewire.assets.edit-asset-maintenance');
    }
}
