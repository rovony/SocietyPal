<?php

namespace App\Livewire\Floors;

use Livewire\Component;
use Livewire\Attributes\On;

class FloorManagmentList extends Component
{
    public $search;
    public $showAddFloorModal = false;
    public $showFilterButton = true;

    #[On('hideAddFloor')]
    public function hideAddFloor()
    {
        $this->showAddFloorModal = false;
        $this->js('window.location.reload()');
    }

    #[On('clearFloorFilter')]
    public function clearFloorFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('Floor')]
    public function FloorBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.floors.floor-managment-list');
    }
}
