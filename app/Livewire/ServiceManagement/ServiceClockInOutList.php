<?php

namespace App\Livewire\ServiceManagement;

use Livewire\Attributes\On;
use Livewire\Component;

class ServiceClockInOutList extends Component
{
    public $search;
    public $showAddAttendance = false;

    #[On('hideAddAttendance')]
    public function hideAddAttendance()
    {
        $this->showAddAttendance = false;
        $this->js('window.location.reload()');
    }

    public function render()
    {
        return view('livewire.service-management.service-clock-in-out-list');
    }
}
