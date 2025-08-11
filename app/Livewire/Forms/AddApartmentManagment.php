<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Models\Floor;
use App\Models\Tower;
use Livewire\Component;
use App\Models\Apartment;
use App\Models\Maintenance;
use Livewire\Attributes\On;
use App\Models\ApartmentManagement;
use App\Models\ParkingManagementSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddApartmentManagment extends Component
{
    use LivewireAlert;

    public $floorId;
    public $apartmentId;
    public $apartmentManagementId;
    public $userId;
    public $parking;
    public $apartmentNumber;
    public $apartmentArea;
    public $floor;
    public $user;
    public $tenant;
    public $floors = [];
    public $apartment;
    public $towers;
    public $towerName;
    public $status;
    public $unitType;
    public $parkingCodes;
    public $parkingCode;
    public $selectedTower = null;
    public $selectedParkingCode = null;
    public $selectedFloor = null;
    public $showAddParkingModal = false;
    public $showParkingModal = false;
    public $selectedParkingCodes = [];

    protected $listeners = ['refreshParkingCodes' => 'refreshParkingCodes'];


    public function mount()
    {
        $this->floor = Floor::all();
        $this->towers = Tower::all();
        $this->apartment = Apartment::all();
        $this->unitType = Maintenance::first()->unit_name ?? null;
        $this->user = User::whereHas('role', function ($q) {
            $q->where('display_name', 'Owner');
        })->get();
        $this->refreshParkingCodes();
    }

    public function updatedSelectedTower($towerId)
    {
        $this->floors = Floor::where('tower_id', $towerId)->get();
        $this->selectedFloor = null;
    }

    public function submitForm()
    {
        $validationRules = [
            'selectedFloor' => 'required',
            'apartmentNumber' => 'required|unique:apartment_managements,apartment_number,NULL,id,society_id,' . society()->id . ',tower_id,' . $this->selectedTower,
            'apartmentArea' => 'required|integer',
            'apartmentId' => 'required',
            'status' => 'required',
            'selectedTower' => 'required',
        ];

        if ($this->status === 'occupied') {
            $validationRules['userId'] = 'required';
        }

        $messages = [
            'userId' => __('messages.userIdValidationMessage'),
        ];

        $this->validate($validationRules,$messages);

        $apartmentManagement = new ApartmentManagement();
        $apartmentManagement->floor_id = $this->selectedFloor;
        $apartmentManagement->tower_id = $this->selectedTower;
        $apartmentManagement->apartment_number = $this->apartmentNumber;
        $apartmentManagement->apartment_area = $this->apartmentArea;
        $apartmentManagement->apartment_area_unit = $this->unitType;
        $apartmentManagement->apartment_id = $this->apartmentId;
        $apartmentManagement->status = $this->status;
        $apartmentManagement->user_id = $this->userId;
        $apartmentManagement->id = $this->apartmentManagementId;
        $apartmentManagement->save();

        $apartmentManagement->parkingCodes()->sync($this->selectedParkingCodes);

        ParkingManagementSetting::whereIn('id', $this->selectedParkingCodes)
            ->update(['status' => "not_available"]);

        $this->dispatch('hideAddApartmentManagement');
        $this->dispatch('apartmentAdded');

        $this->alert('success', __('messages.apartmentManagementAdded'));

        $this->resetForm();
    }

    public function toggleSelectType($parkingCodes)
    {
        if (in_array($parkingCodes['id'], $this->selectedParkingCodes)) {
            $this->selectedParkingCodes = array_filter($this->selectedParkingCodes, fn($id) => $id !== $parkingCodes['id']);
        } else {
            $this->selectedParkingCodes[] = $parkingCodes['id'];
        }
        $this->selectedParkingCodes = array_values($this->selectedParkingCodes);

    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['selectedFloor', 'apartmentNumber', 'apartmentArea', 'apartmentId', 'status', 'userId', 'selectedTower', 'selectedParkingCode']);
    }


    public function hideAddParking()
    {
        $this->showAddParkingModal = false;
    }

    public function refreshParkingCodes()
    {
        $this->parkingCodes = ParkingManagementSetting::whereNotIn('id', function($query) {
            $query->select('parking_id')
                ->from('apartment_parking');
        })->get();
    }

    public function showAddParking()
    {
        $this->showAddParkingModal = true;
    }

    public function addModalParking()
    {
        $this->validate([
            'parkingCode' => 'required|unique:parking_managements,parking_code',
        ]);
            $parking = new ParkingManagementSetting();
        $parking->parking_code = $this->parkingCode;

        if ($parking->apartment_id) {
            $parking->status = "not_available";
        } else {
            $parking->status = "available";
        }
        $parking->save();
        $this->alert('success', __('messages.parkingAdded'));
        $this->hideAddParking();
        $this->refreshParkingCodes();
    }


    public function render()
    {
        return view('livewire.forms.add-apartment-managment');
    }
}
