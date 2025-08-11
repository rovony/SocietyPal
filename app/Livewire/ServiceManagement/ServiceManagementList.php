<?php

namespace App\Livewire\ServiceManagement;

use App\Models\ServiceType;
use Livewire\Attributes\On;
use Livewire\Component;

class ServiceManagementList extends Component
{
    public $search;
    public $activeService;
    public $serviceId = null;
    public $showAddServiceManagementModal = false;
    public $showFilterButton = true;
    protected $queryString = ['serviceId'];

    public function mount()
    {
       if ($this->serviceId) {
            $this->showServiceItems($this->serviceId);
        } else {
            $firstServiceType = ServiceType::first();
            if ($firstServiceType) {
                $this->showServiceItems($firstServiceType->id);
            }
        }
    }

    public function showServiceItems($id)
    {
        $this->activeService = ServiceType::findOrFail($id);
        $this->serviceId = $id;
    }

    #[On('hideAddServiceManagement')]
    public function hideAddServiceManagement()
    {
        $this->showAddServiceManagementModal = false;
        $this->js('window.location.reload()');
    }

    #[On('clearServiceFilter')]
    public function clearServiceFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('Service')]
    public function ServiceBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.service-management.service-management-list', [
            'serviceTypes' => ServiceType::with('serviceManagement')->get(),
        ]);
    }
}
