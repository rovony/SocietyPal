<?php

namespace App\Livewire\Tenants;

use Livewire\Component;
use Livewire\Attributes\On;


class TenantList extends Component
{
    public $search;
    public $showAddTenant = false;
    public $showFilterButton = true;

    #[On('hideAddTenant')]
    public function hideAddTenant()
    {
        $this->showAddTenant = false;
        $this->js('window.location.reload()');
    }

    #[On('clearTenantFilter')]
    public function clearTenantFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('hideTenantFilters')]
    public function hideTenantFiltersBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.tenants.tenant-list');
    }
}
