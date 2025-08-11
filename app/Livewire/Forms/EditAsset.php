<?php

namespace App\Livewire\Forms;

use Carbon\Carbon;
use App\Models\User;
use App\Helper\Files;
use App\Models\Tower;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\AssetsCategory;
use App\Models\AssetManagement;
use App\Models\ApartmentManagement;
use App\Models\Floor;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditAsset extends Component
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
    public $asset;
    public $purchaseDate;
    public $maintenanceSchedule;
    public $apartmentId ;
    public $apartments;
    public $towers;
    public $towerName;
    public $floors = [];
    public $selectedTower = null;
    public $selectedFloor = null;
    public $hasPermission = false;

    public function mount()
    {
        $this->asset = AssetManagement::findOrFail($this->asset->id);
        $this->owners = User::where('role_id', 3)->select('id', 'name')->get();
        $this->tenants = User::where('role_id', 4)->select('id', 'name')->get();
        $this->categories = AssetsCategory::get();
        $this->apartments = ApartmentManagement::all();
        $this->name = $this->asset->name;
        $this->category = $this->asset->category_id;
        $this->location = $this->asset->location;
        $this->condition = $this->asset->condition;
        $this->owner_id = $this->asset->owner_id;
        $this->tenant_id = $this->asset->tenant_id;
        $this->documentPath = $this->asset->file_path;
        $this->purchaseDate = $this->asset->purchase_date;
        $this->maintenanceSchedule = $this->asset->maintenance_schedule;
        $this->apartmentId = $this->asset->apartment_id;
        $this->towerName = $this->asset->tower_id;
        $this->selectedTower = $this->asset->tower_id;
        $this->selectedFloor = $this->asset->floor_id;

        if (user_can('Update Assets')) {
            $this->hasPermission = true;
            $this->apartments = ApartmentManagement::all();
            $this->towers = Tower::all();
            $this->categories = AssetsCategory::get();
            $this->floors = Floor::all();
            if ($this->selectedTower) {
                $this->floors = Floor::where('tower_id', $this->selectedTower)->get();
                $this->apartments = ApartmentManagement::where('tower_id', $this->selectedTower)->get();
            }

            if ($this->selectedFloor) {
                $this->apartments = ApartmentManagement::where('floor_id', $this->selectedFloor)->get();
            }
        } else {
            $this->hasPermission = false;

            $userTowerIds = ApartmentManagement::whereHas('apartments', function ($query) {
                $query->where('user_id', user()->id);
            })->pluck('tower_id')->unique();

            $userFloorIds = ApartmentManagement::whereHas('apartments', function ($query) {
                $query->where('user_id', user()->id);
            })->pluck('floor_id')->unique();
            $userTowerIds->push($this->asset->tower_id)->unique();
            $userFloorIds->push($this->asset->floor_id)->unique();

            $this->towers = Tower::whereIn('id', $userTowerIds)->get();
            $this->floors = Floor::whereIn('id', $userFloorIds)->get();

            $this->apartments = ApartmentManagement::whereHas('apartments', function ($query) {
                $query->where('user_id', user()->id);
            })->orWhere('id', $this->asset->apartment_id)->get();
           $this->categories = AssetsCategory::get();
        }



    }

    public function submitForm()
    {

        $this->validate([
            'name' => 'required|string|max:255',
            'category' => 'required',
            'selectedTower' => 'required|exists:towers,id',
            'condition' => 'required|string|max:255',

        ]);

        $this->asset->name = $this->name;
        $this->asset->category_id = $this->category;
        $this->asset->location = $this->location;
        $this->asset->condition = $this->condition;
        $this->asset->tower_id = $this->selectedTower !== '' ? $this->selectedTower : null;
        $this->asset->floor_id = $this->selectedFloor !== '' ? $this->selectedFloor : null;
        $this->asset->apartment_id =  $this->apartmentId !== '' ? $this->apartmentId : null;
        $this->asset->purchase_date = Carbon::parse($this->purchaseDate)->format('Y-m-d');
        if ($this->documentPath && $this->documentPath !== $this->asset->file_path) {
            $filename = Files::uploadLocalOrS3($this->documentPath, AssetManagement::FILE_PATH . '/');
            $this->asset->file_path = $filename;
        }

        $this->asset->save();
        $this->alert('success', __('messages.AssetUpdated'));
        $this->dispatch('hideEditAsset');
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


    public function removeFile()
    {

        $this->documentPath = null;
        $this->dispatch('photo-removed');
    }

    public function render()
    {
        return view('livewire.forms.edit-asset');
    }
}
