<?php

namespace App\Livewire\Forms;

use Carbon\Carbon;
use App\Models\User;
use App\Helper\Files;
use App\Models\Floor;
use App\Models\Tower;
use Livewire\Component;
use App\Models\Apartment;
use Livewire\WithFileUploads;
use App\Models\AssetsCategory;
use App\Models\AssetManagement;
use App\Models\ApartmentManagement;
use Jantinnerezo\LivewireAlert\LivewireAlert;


class AddAsset extends Component
{

    use LivewireAlert, WithFileUploads;
    public $name;
    public $category;
    public $location;
    public $condition;
    public $owner_id;
    public $tenant_id;
    public $documentPath;
    public $owners;
    public $tenants;
    public $categories;
    public $purchaseDate;
    public $maintenanceSchedule;
    public $apartments;
    public $apartmentId;
    public $apartment;
    public $towers;
    public $towerName;
    public $floors = [];
    public $selectedTower = null;
    public $selectedFloor = null;
    public $hasPermission = false;



    public function mount()
    {
        $this->owners = User::where('role_id', 3)->select('id', 'name')->get();
        $this->tenants = User::where('role_id', 4)->select('id', 'name')->get();
        if (user_can('Create Assets')) {
            $this->hasPermission = true;
            $this->apartments = ApartmentManagement::all();
            $this->towers = Tower::all();
            $this->categories = AssetsCategory::get();
        } else {
            $this->hasPermission = false;
            $userTowerIds = ApartmentManagement::whereHas('apartments', function ($query) {
            $query->where('user_id', user()->id);
            })->pluck('tower_id')->unique();

            $userFloorIds = ApartmentManagement::whereHas('apartments', function ($query) {
            $query->where('user_id', user()->id);
            })->pluck('floor_id')->unique();

            $this->towers = Tower::whereIn('id', $userTowerIds)->get();
            $this->floors = Floor::whereIn('id', $userFloorIds)->get();
            $this->apartments = ApartmentManagement::whereHas('apartments', function ($query) {
            $query->where('user_id', user()->id);
            })->get();
            $this->categories = AssetsCategory::get();
        }
        $this->apartment = Apartment::all();
    }

    public function updatedSelectedTower($towerId)
    {
        if (!$this->hasPermission) {
            return;
        }

        $this->floors = Floor::where('tower_id', $towerId)->get();
        $this->selectedFloor = null;
        $this->apartments = ApartmentManagement::where('tower_id', $towerId)->get();
        $this->apartmentId = null;
    }

    public function updatedSelectedFloor($floorId)
    {
        if (!$this->hasPermission) {
            return;
        }

        $this->apartments = ApartmentManagement::where('floor_id', $floorId)->get();
        $this->apartmentId = null;
    }
    public function submitForm()
    {

        $this->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|exists:asset_categories,id',
            'selectedTower' => 'required|exists:towers,id',
            'condition' => 'required|string|max:255',
        ]);

        if ($this->documentPath) {
            $filename = Files::uploadLocalOrS3($this->documentPath, AssetManagement::FILE_PATH . '/');
            $this->documentPath = $filename;
        }
        $asset = new AssetManagement();
        $asset->name = $this->name;
        $asset->category_id = $this->category;
        $asset->location = $this->location;
        $asset->condition = $this->condition;
        $asset->apartment_id = $this->apartmentId ?? null;
        $asset->file_path = $this->documentPath;
        $asset->purchase_date = Carbon::parse($this->purchaseDate)->format('Y-m-d');
        $asset->maintenance_schedule = $this->maintenanceSchedule;
        $asset->tower_id = $this->selectedTower;
        $asset->floor_id = $this->selectedFloor ?? null;


        $asset->save();
        $this->alert('success', __('messages.AssetAdded'));
        $this->dispatch('hideAddAsset');

    }

    public function resetForm()
    {
        $this->name = '';
        $this->category = '';
        $this->location = '';
        $this->condition = '';
        $this->owner_id = '';
        $this->tenant_id = '';
        $this->documentPath = '';
    }
    public function removeFile()
    {
        $this->documentPath = null;
        $this->dispatch('photo-removed');
    }



    public function render()
    {
        return view('livewire.forms.add-asset');
    }
}
