<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Models\Floor;
use App\Models\Tower;
use Livewire\Component;
use App\Models\Apartment;
use App\Models\Maintenance;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use App\Models\ApartmentParking;
use App\Models\ApartmentManagement;
use App\Models\ParkingManagementSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditApartmentManagement extends Component
{
    use LivewireAlert;

    public $floor;
    public $floors;
    public $towers;
    public $user;
    public $apartment;
    public $apartmentManagment;
    public $floorId;
    public $apartmentNumber;
    public $apartmentManagementId;
    public $apartmentArea;
    public $apartmentId;
    public $status;
    public $unitType;
    public $selectedTower = null;
    public $selectedFloor = null;
    public $userId;
    public $selectedParkingCode = null;
    public $parkingCodes;
    public $parkingCode;
    public $apartmentManagement;
    public $isOpen = false;
    public $showAddParkingModal = false;
    public $selectedParkingCodes = [];
    protected $listeners = ['refreshParkingCodes' => 'refreshParkingCodes'];

    public function mount()
    {
        $this->apartment = Apartment::all();
        $this->towers = Tower::all();
        $this->user = User::whereHas('role', function($q) { $q->where('display_name', 'Owner'); })->get();
        $this->unitType = Maintenance::first()->unit_name;
        $this->apartmentNumber = $this->apartmentManagment->apartment_number;
        $this->apartmentArea = $this->apartmentManagment->apartment_area;
        $this->selectedFloor = $this->apartmentManagment->floor_id;
        $this->selectedTower = $this->apartmentManagment->tower_id;
        $this->floors = Floor::where('tower_id', $this->selectedTower)->get();
        $this->apartmentId = $this->apartmentManagment->apartment_id;
        $this->userId = $this->apartmentManagment->user_id;
        $this->apartmentManagementId = $this->apartmentManagment->id;
        $this->status = $this->apartmentManagment->status;
        $this->refreshParkingCodes();
        $this->selectedParkingCodes = ApartmentParking::where('apartment_management_id', $this->apartmentManagment->id)
            ->pluck('parking_id')
            ->toArray();
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
            'apartmentNumber' => [
                'required',
                Rule::unique('apartment_managements', 'apartment_number')
                    ->where(function ($query) {
                        $query->where('society_id', society()->id)
                              ->where('tower_id', $this->selectedTower);
                    })
                    ->ignore($this->apartmentManagementId),
            ],
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

        $apartmentManagement = ApartmentManagement::findOrFail($this->apartmentManagementId);

        $oldParkingCodes = $apartmentManagement->parkingCodes->pluck('id')->toArray();

        $apartmentManagement->floor_id = $this->selectedFloor;
        $apartmentManagement->tower_id = $this->selectedTower;
        $apartmentManagement->apartment_number = $this->apartmentNumber;
        $apartmentManagement->apartment_area = $this->apartmentArea;
        $apartmentManagement->apartment_area_unit = $this->unitType;
        $apartmentManagement->apartment_id = $this->apartmentId;
        $apartmentManagement->status = $this->status;

        if ($this->status == "occupied") {
            $apartmentManagement->user_id = $this->userId;
        } elseif ($this->status == "available_for_rent") {
            $apartmentManagement->user_id = $this->userId ?: null;
        } else {
            $apartmentManagement->user_id = null;
        }

        $apartmentManagement->save();

        $apartmentManagement->parkingCodes()->sync($this->selectedParkingCodes);

        ParkingManagementSetting::whereIn('id', $this->selectedParkingCodes)
            ->update(['status' => "not_available"]);

        $parkingCodesToRelease = array_diff($oldParkingCodes, $this->selectedParkingCodes);
        ParkingManagementSetting::whereIn('id', $parkingCodesToRelease)
            ->update(['status' => 'available']);

        $this->alert('success', __('messages.apartmentManagementUpdated'));
        $this->dispatch('hideEditApartmentManagement');
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

    #[On('hideAddParking')]
    public function hideAddParking()
    {
        $this->showAddParkingModal = false;
        $this->js('window.location.reload()');
    }

    public function refreshParkingCodes()
    {
        $this->parkingCodes = ParkingManagementSetting::whereNotIn('id', function($query) {
            $query->select('parking_id')
                ->from('apartment_parking');
        })->orWhereHas('apartmentParking', function($query) {
            $query->where('apartment_management_id', $this->apartmentManagment->id);
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
        $this->hideAddParking();
        $this->alert('success', __('messages.parkingAdded'));
        $this->refreshParkingCodes();
    }

    public function render()
    {
        return view('livewire.forms.edit-apartment-management');
    }
}
