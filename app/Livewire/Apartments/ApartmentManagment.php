<?php

namespace App\Livewire\Apartments;

use App\Models\Floor;
use App\Models\Tower;
use Livewire\Component;
use App\Models\Apartment;
use Livewire\Attributes\On;
use App\Exports\ApartmentExport;
use App\Models\ApartmentManagement;
use App\Models\ApartmentParking;
use App\Models\ParkingManagementSetting;
use App\Models\Tenant;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ApartmentManagment extends Component
{

    use LivewireAlert;

    protected $listeners = ['refreshApartments' => 'mount'];

    public $apartmentManagments;
    public $apartmentManagment;
    public $apartments;
    public $floor;
    public $tower;
    public $search;
    public $showEditApartmentManagementModal = false;
    public $showAddApartmentManagementModal = false;
    public $confirmDeleteApartmentManagementModal = false;
    public $confirmSelectedDeleteApartmentManagementModal = false;
    public $showApartmentManagementModal = false;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $showFilters = false;
    public $clearFilterButton = false;
    public $startDate;
    public $endDate;
    public $filterApartmentsType = [];
    public $filterTower = [];
    public $filterFloor = [];
    public $filterStatuses = [];

    public function mount()
    {
        $this->apartmentManagments = ApartmentManagement::with('apartments', 'floors', 'towers')->get();
        $this->apartments = Apartment::get();
        $this->floor = Floor::get();
        $this->tower = Tower::get();

        if (request()->has('status')) {
            $this->filterStatuses[] = request('status');
            $this->clearFilterButton = true;
            $this->showFilters = true;
        }
    }

    public function showEditApartmentManagement($id)
    {
        $this->apartmentManagment = ApartmentManagement::findOrFail($id);
        $this->showEditApartmentManagementModal = true;
    }

    public function showDeleteApartmentManagement($id)
    {
        $this->apartmentManagment = ApartmentManagement::findOrFail($id);
        $this->confirmDeleteApartmentManagementModal = true;
    }

    public function deleteApartmentManagement($id)
    {
        $parkingData = ApartmentParking::where('apartment_management_id', $id)->get();
        $parkingIds = $parkingData->pluck('parking_id')->toArray();

        ParkingManagementSetting::whereIn('id', $parkingIds)
            ->update(['status' => 'available']);

        ApartmentParking::where('apartment_management_id', $id)->delete();

        ApartmentManagement::destroy($id);


        $this->confirmDeleteApartmentManagementModal = false;

        $this->apartmentManagment = '';
        $this->dispatch('refreshApartments');

        $this->alert('success', __('messages.apartmentManagementDeleted'));
    }

    public function showUtilityBill($id)
    {
        $this->apartmentManagment = ApartmentManagement::findOrFail($id);
        $this->showApartmentManagementModal = true;
    }

    #[On('hideApartmentManagementModal')]
    public function hideApartmentManagement()
    {
        $this->apartmentManagment = "";
        $this->showApartmentManagementModal = false;
    }

    #[On('hideEditApartmentManagement')]
    public function hideEditApartmentManagement()
    {
        $this->showEditApartmentManagementModal = false;
        $this->js('window.location.reload()');
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->apartmentManagments->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function showSelectedDelete()
    {
        $this->confirmSelectedDeleteApartmentManagementModal = true;
    }

    public function deleteSelected()
    {
        $parkingData = ApartmentParking::whereIn('apartment_management_id', $this->selected)->get();
        $parkingIds = $parkingData->pluck('parking_id')->toArray();

        ParkingManagementSetting::whereIn('id', $parkingIds)
            ->update(['status' => 'available']);

        ApartmentParking::whereIn('apartment_management_id', $this->selected)->delete();

        ApartmentManagement::whereIn('id', $this->selected)->delete();

        $this->selected = [];
        $this->selectAll = false;
        $this->showActions = false;
        $this->confirmSelectedDeleteApartmentManagementModal = false;
        $this->dispatch('refreshApartments');

        $this->alert('success', __('messages.apartmentManagementDeleted'));
    }

    #[On('showUtilityBillsFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->startDate = '';
        $this->endDate = '';
        $this->search = '';
        $this->filterApartmentsType = [];
        $this->filterTower = [];
        $this->filterFloor = [];
        $this->filterStatuses = [];
        $this->clearFilterButton = false;
    }

    #[On('exportApartments')]
    public function exportApartments()
    {
        return (new ApartmentExport($this->search, $this->filterApartmentsType, $this->filterTower, $this->filterFloor, $this->filterStatuses))->download('apartment-' . now()->toDateTimeString() . '.xlsx');
    }

    public function render()
    {
        $query = ApartmentManagement::query();

        if (!user_can('Show Apartment')) {
            $query->where(function ($query) {
                if (isRole() == 'Owner') {
                    $query->where('user_id', user()->id);
                } elseif (isRole() == 'Tenant') {
                    $tenant = Tenant::where('user_id', user()->id)->first();
                    if ($tenant) {
                        $query->whereHas('apartmentTenants', function ($query) use ($tenant) {
                            $query->where('tenant_id', $tenant->id);
                        });
                    }
                }
            });
        }

        if (!empty($this->search)) {
            $query = $query->with('towers', 'floors', 'apartments')
                ->orWhereHas('towers', function ($query) {
                    $query->where('tower_name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('floors', function ($query) {
                    $query->where('floor_name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('apartments', function ($query) {
                    $query->where('apartment_type', 'like', '%' . $this->search . '%');
                })
                ->orwhere('apartment_number', 'like', '%' . $this->search . '%')
                ->orWhere('apartment_area', 'like', '%' . $this->search . '%',)
                ->orWhere('status', 'like', '%' . $this->search . '%',);
        }

        if (!empty($this->filterApartmentsType)) {
            $query->whereHas('apartments', function ($query) {
                $query->whereIn('apartment_type', $this->filterApartmentsType);
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterTower)) {
            $query->whereHas('towers', function ($query) {
                $query->whereIn('tower_name', $this->filterTower);
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterFloor)) {
            $query->whereHas('floors', function ($query) {
                $query->whereIn('floor_name', $this->filterFloor);
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterStatuses)) {
            $this->clearFilterButton = true;
            $query->whereIn('status', $this->filterStatuses);
        }

        $apartmentManagmentsData = $query->paginate(10);

        return view('livewire.apartments.apartment-managment', [
            'apartmentManagmentsData' => $apartmentManagmentsData,
        ]);
    }
}
