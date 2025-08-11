<?php

namespace App\Livewire\BookAmenity;

use Livewire\Attributes\On;
use Livewire\Component;

class BookAmenityList extends Component
{
    public $search;
    public $showAddBookAmenity = false;
    public $showFilterButton = true;

    #[On('hideAddBookAmenity')]
    public function hideAddBookAmenity()
    {
        $this->showAddBookAmenity = false;
        $this->js('window.location.reload()');
    }

    #[On('clearBookAmenityFilter')]
    public function clearBookAmenityFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('hideBookAmenityFilters')]
    public function hideBookAmenityFiltersBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.book-amenity.book-amenity-list');
    }
}
