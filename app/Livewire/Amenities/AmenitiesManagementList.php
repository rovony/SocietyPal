<?php

namespace App\Livewire\Amenities;

use Livewire\Component;
use Livewire\Attributes\On;

class AmenitiesManagementList extends Component
{
    public $search;
    public $showAddAmenitiesModal = false;
    public $showFilterButton = true;

    #[On('hideAddAmenities')]
    public function hideAddAmenities()
    {
        $this->showAddAmenitiesModal = false;
        $this->js('window.location.reload()');
    }

    #[On('clearAmenitiesFilter')]
    public function clearAmenitiesFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }
    #[On('Amenities')]
    public function AmenitiesBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.amenities.amenities-management-list');
    }
}
