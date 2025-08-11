<?php

namespace App\Livewire\Maintenance;

use Livewire\Attributes\On;
use Livewire\Component;

class MaintenanceList extends Component
{
    public $search;
    public $showAddMaintenance = false;
    public $showFilterButton = true;

    #[On('hideAddMaintenance')]
    public function hideAddMaintenance()
    {
        $this->showAddMaintenance = false;
        $this->js('window.location.reload()');
    }

    #[On('clearMaintenanceFilter')]
    public function clearMaintenanceFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }
 
    #[On('hideMaintenanceFilters')]
    public function hideMaintenanceFiltersBtn()
    {
        $this->showFilterButton = true;
    }
    
    public function render()
    {
        return view('livewire.maintenance.maintenance-list');
    }
}
