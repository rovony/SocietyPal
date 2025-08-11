<?php

namespace App\Livewire\Parkings;

use App\Exports\ParkingExport;
use App\Models\ApartmentManagement;
use App\Models\ParkingManagementSetting;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ParkingManagement extends Component
{
    use LivewireAlert;

    protected $listeners = ['refreshParkings' => 'mount'];

    public $parkings;
    public $parkingDelete;
    public $parkingEdit;
    public $apartments;
    public $search;
    public $showEditParkingModal = false;
    public $showAddParkingModal = false;
    public $confirmDeleteParkingModal = false;
    public $confirmSelectedDeleteParkingModal = false;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $showFilters = false;
    public $clearFilterButton = false;
    public $filterApartments = [];
    public $filterStatuses = [];

    public function mount()
    {
        $this->apartments = ApartmentManagement::get();
        $this->parkings = ParkingManagementSetting::get();
    }

    public function showAddParking()
    {
        $this->dispatch('resetForm');
        $this->showAddParkingModal = true;
    }

    public function showEditParking($id)
    {
        $this->parkingEdit = ParkingManagementSetting::with(['parkingCode', 'apartmentParking.apartmentManagement'])
            ->findOrFail($id);
        $this->showEditParkingModal = true;
    }

    public function showDeleteParking($id)
    {
        $this->parkingDelete = ParkingManagementSetting::findOrFail($id);
        $this->confirmDeleteParkingModal = true;
    }

    public function deleteParking($id)
    {
        ParkingManagementSetting::destroy($id);

        $this->confirmDeleteParkingModal = false;

        $this->parkingDelete= '';
        $this->dispatch('refreshParkings');

        $this->alert('success', __('messages.parkingDeleted'));
    }

    #[On('hideEditParking')]
    public function hideEditParking()
    {
        $this->showEditParkingModal = false;
        $this->js('window.location.reload()');
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->parkings->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;

    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function showSelectedDelete()
    {
        $this->confirmSelectedDeleteParkingModal = true;
    }

    public function deleteSelected()
    {
        ParkingManagementSetting::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->confirmSelectedDeleteParkingModal = false;
        $this->alert('success', __('messages.parkingDeleted'));

    }

    #[On('showParkingFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterStatuses = [];
        $this->filterApartments = [];
    }

    #[On('exportParking')]
    public function exportParking()
    {
        return (new ParkingExport($this->search, $this->filterApartments, $this->filterStatuses))->download('parking-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;
        $query = ParkingManagementSetting::query()->with(['apartmentParking.apartmentManagement','apartmentManagement']);

        $loggedInUser = user()->id;
        if (! user_can('Show Parking')) {
            $query->where(function ($q) use ($loggedInUser) {
                $q->whereHas('apartmentManagement', function ($q) use ($loggedInUser) {
                    $q->where('user_id', $loggedInUser);
                })
                ->orWhereHas('apartmentManagement.tenants', function ($q) use ($loggedInUser) {
                    $q->where('user_id', $loggedInUser)
                      ->where('apartment_tenant.status', 'current_resident');
                });
            });
        }

        if (!empty($this->search)) {
            $query = $query = $query->with('apartmentManagement')
            ->orWhereHas('apartmentManagement', function ($query) {
                $query->where('apartment_number', 'like', '%'.$this->search.'%');
            })
            ->Orwhere('parking_code', 'like', '%' . $this->search . '%');
        }

        if (!empty($this->filterApartments)) {
            $query->whereHas('apartmentParking', function ($q) {
                $q->whereIn('apartment_management_id', $this->filterApartments);
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterStatuses)) {
            $query->whereIn('status', $this->filterStatuses);
            $this->clearFilterButton = true;
        }
        $parkingData = $query->paginate(10);

        return view('livewire.parkings.parking-management', [
            'parkingData' => $parkingData,
        ]);
    }
}
