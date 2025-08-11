<?php

namespace App\Livewire\Owner;

use Livewire\Component;
use Livewire\Attributes\On;

class OwnerList extends Component
{
    public $search;
    public $showAddOwner = false;
    public $showFilterButton = true;

    #[On('hideAddOwner')]
    public function hideAddOwner()
    {
        $this->showAddOwner = false;
        $this->js('window.location.reload()');
    }

    #[On('clearOwnerFilter')]
    public function clearOwnerFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('hideOwnerFilters')]
    public function hideOwnerFiltersBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.owner.owner-list');
    }
}
