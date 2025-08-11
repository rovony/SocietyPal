<?php

namespace App\Livewire\Parkings;

use Livewire\Component;
use Livewire\Attributes\On;

class ParkingManagmentList extends Component
{

    public $search;
    public $showAddParkingModal = false;
    public $showFilterButton = true;


    #[On('hideAddParking')]
    public function hideAddParking()
    {
        $this->showAddParkingModal = false;
        $this->js('window.location.reload()');
    }

    #[On('clearParkingFilter')]
    public function clearParkingFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('Parking')]
    public function ParkingBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.parkings.parking-managment-list');
    }
}
