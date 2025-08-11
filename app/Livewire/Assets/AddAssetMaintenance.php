<?php

namespace App\Livewire\Assets;

use Carbon\Carbon;
use App\Models\Floor;
use App\Models\Tower;
use Livewire\Component;
use App\Models\Apartment;
use App\Models\AssetManagement;
use App\Models\AssetMaintenance;
use App\Models\ServiceManagement;
use Illuminate\Support\Facades\Request;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddAssetMaintenance extends Component
{
    use LivewireAlert;

    public $assets;
    public $assetId;
    public $maintenance_date;
    public $schedule;
    public $status;
    public $amount;
    public $serviceManagement;
    public $serviceId;
    public $notes;
    public $towers;
    public $apartment;
    public $floors = [];
    public $selectedTower = null;
    public $selectedFloor = null;
    public $assetsApartment = [];
    public $reminder = true;



    public function mount()
    {
        $this->assetId = $this->assetId;
        $this->assets = AssetManagement::all();
        $this->serviceManagement =ServiceManagement::all();
        $this->towers = Tower::all();
        $this->apartment = Apartment::all();
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

    public function updatedSelectedTower($towerId)
    {
        $this->floors = Floor::where('tower_id', $towerId)->get();
        $this->selectedFloor = null;
    }

    public function saveMaintenance()
    {
        $this->validate([
            'assetId' => 'required|exists:asset_managements,id',
            'maintenance_date' => 'required|date',
            'schedule' => 'required|in:weekly,biweekly,monthly,half-year,yearly',
            'status' => 'required|in:pending,completed,overdue',
        ]);
        $assetMaintenance = new AssetMaintenance();
        $assetMaintenance->asset_id = $this->assetId;
        $assetMaintenance->maintenance_date = Carbon::parse($this->maintenance_date)->format('Y-m-d');
        $assetMaintenance->schedule = $this->schedule;
        $assetMaintenance->status = $this->status;
        $assetMaintenance->amount = $this->amount;
        $assetMaintenance->service_management_id = $this->serviceId;
        $assetMaintenance->notes = $this->notes;
        $assetMaintenance->reminder = $this->reminder;
        $assetMaintenance->save();

        $this->resetForm();
        $this->alert('success', __('messages.AssetAdded'));
        if (Request::is('assets*')) {
            return redirect()->route('assets.show', ['asset' => $this->assetId]);
        } else {
            return redirect()->route('asset-maintenance.index');
        }
    }

    public function resetForm()
    {

        $this->maintenance_date = '';
        $this->schedule = '';
        $this->status = '';
        $this->amount = '';
    }


    public function render()
    {
        return view('livewire.assets.add-asset-maintenance');
    }
}
