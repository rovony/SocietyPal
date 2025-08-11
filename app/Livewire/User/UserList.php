<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\On;

class UserList extends Component
{
    public $search;
    public $showAddUser = false;
    public $showFilterButton = true;

    #[On('hideAddUser')]
    public function hideAddUser()
    {
        $this->showAddUser = false;
        $this->js('window.location.reload()');
    }

    #[On('clearUserFilter')]
    public function clearUserFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('hideUserFilters')]
    public function hideUserFiltersBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.user.user-list');
    }
}
