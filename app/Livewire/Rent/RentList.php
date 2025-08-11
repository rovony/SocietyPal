<?php

namespace App\Livewire\Rent;

use Livewire\Attributes\On;
use Livewire\Component;

class RentList extends Component
{
    public $search;
    public $showAddRent = false;
    public $showFilterButton = true;

    #[On('hideAddRent')]
    public function hideAddRent()
    {
        $this->showAddRent = false;
        $this->js('window.location.reload()');
    }

    #[On('clearRentFilter')]
    public function clearRentFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('hideRentFilters')]
    public function hideRentFiltersBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.rent.rent-list');
    }
}
