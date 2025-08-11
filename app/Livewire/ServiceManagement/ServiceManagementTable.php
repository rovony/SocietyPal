<?php

namespace App\Livewire\ServiceManagement;

use App\Exports\ServiceManagementExport;
use App\Models\ApartmentManagement;
use App\Models\ServiceManagement;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class ServiceManagementTable extends Component
{ 
    use LivewireAlert;

    protected $listeners = ['refreshServiceManagement' => 'mount'];

    public $serviceManagement;
    public $serviceManangements;
    public $serviceManagementEdit;
    public $serviceManagementShow;
    public $search;
    public $showEditServiceManagementModal = false;
    public $confirmDeleteServiceManagementModal = false;
    public $showServiceManagementModal = false;
    public $confirmSelectedDeleteServiceManagementModal = false;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $showFilters = false;
    public $filterStatuses = [];
    public $filterApartments = [];
    public $clearFilterButton = false;
    public $serviceId; 
    public $apartments;

    public function mount()
    {
        $this->serviceManangements = ServiceManagement::get();
        $this->apartments = ApartmentManagement::get();
    }

    public function showEditServiceManagement($id)
    {
        $this->serviceManagementEdit = ServiceManagement::with('apartmentManagements')->findOrFail($id);
        $this->showEditServiceManagementModal = true;
    }

    #[On('hideEditServiceManagement')]
    public function hideEditServiceManagement()
    {
        $this->showEditServiceManagementModal = false;
        $this->js('window.location.reload()');
    }

    public function showServiceManagement($id)
    {
        $this->serviceManagementShow = ServiceManagement::with('apartmentManagements')->findOrFail($id);
        $this->showServiceManagementModal = true;
    }

    #[On('hideShowServiceManagement')]
    public function hideShowServiceManagement()
    {
        $this->showServiceManagementModal = false;
        $this->js('window.location.reload()');
    }

    public function showDeleteServiceManagement($id)
    {
        $this->serviceManagement = ServiceManagement::findOrFail($id);
        $this->confirmDeleteServiceManagementModal = true;
    }

    public function deleteServiceManagement($id)
    {
        ServiceManagement::destroy($id);
        $this->confirmDeleteServiceManagementModal = false;
        $this->serviceManagement= '';

        $this->alert('success', __('messages.serviceDeleted'));
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->serviceManangements->where('service_type_id', $this->serviceId)->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function showSelectedDelete()
    {
        $this->confirmSelectedDeleteServiceManagementModal = true;
    }

    public function deleteSelected()
    {
        ServiceManagement::whereIn('id', $this->selected)->delete();
        $this->alert('success', __('messages.serviceDeleted'));
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->confirmSelectedDeleteServiceManagementModal = false;
    }

    #[On('showServiceFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }
    
    public function clearFilters()
    {
        $this->filterStatuses = [];
        $this->filterApartments = [];
        $this->search = '';
    }

    #[On('exportService')]
    public function exportService()
    {
        return (new ServiceManagementExport($this->search, $this->serviceId, $this->filterStatuses, $this->filterApartments))->download('service-management-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;

        $query = ServiceManagement::query()->with('serviceType', 'apartmentManagements')->when($this->serviceId, function ($query) {
            $query->where('service_type_id', $this->serviceId);
        });
        if (!empty($this->search)) {
            $this->clearFilterButton = true;
            $query->where(function ($q) {
                $q->where('contact_person_name', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterStatuses)) {
            $this->clearFilterButton = true;
            $query->whereIn('status', $this->filterStatuses);
        }

        if (!empty($this->filterApartments)) {
            $query->whereHas('apartmentManagements', function ($q) {
                $q->whereIn('apartment_management_id', $this->filterApartments);
            });
            $this->clearFilterButton = true;
        }
        
        $loggedInUser = user()->id;

        if ((!user_can('Show Service Provider'))) {
            $query->where(function ($subQuery) use ($loggedInUser) {
                $subQuery->whereHas('apartmentManagements', function ($q) use ($loggedInUser) {
                    $q->where('user_id', $loggedInUser);
                })
                ->orWhereHas('apartmentManagements', function ($q) use ($loggedInUser) {
                    $q->whereHas('tenants', function ($q) use ($loggedInUser) {
                        $q->where('user_id', $loggedInUser)->where('apartment_tenant.status', 'current_resident');
                    });
                })
                ->orWhereDoesntHave('apartmentManagements');
            });
        }

        $serviceData = $query->paginate(10);

        return view('livewire.service-management.service-management-table', [
            'serviceData' => $serviceData,
        ]);
    }
}
