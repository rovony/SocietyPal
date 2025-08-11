<?php

namespace App\Livewire\Apartments;

use Livewire\Component;
use Livewire\Attributes\On;

class ApartmentManagmentList extends Component
{

    public $search;
    public $showAddApartmentManagementModal = false;
    public $showFilterButton = true;

    #[On('hideAddApartmentManagement')]
    public function hideAddApartmentManagement()
    {
        $this->showAddApartmentManagementModal = false;
        $this->js('window.location.reload()');
    }

    public function showAddApartmentManagement()
    {
        $this->dispatch('resetForm');
        $this->showAddApartmentManagementModal = true;
    }

    #[On('clearUtilityBillFilter')]
    public function clearUtilityBillFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('UtilityBill')]
    public function UtilityBillBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.apartments.apartment-managment-list');
    }
}
