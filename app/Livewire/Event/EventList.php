<?php

namespace App\Livewire\Event;

use Livewire\Component;
use Livewire\Attributes\On;

class EventList extends Component
{
    public $search;
    public $showAddEvent = false;
    public $showFilterButton = true;

    #[On('hideAddEvent')]
    public function hideAddEvent()
    {
        $this->showAddEvent = false;
        $this->js('window.location.reload()');
    }

    #[On('clearEventFilter')]
    public function clearEventFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('hideEventFilters')]
    public function hideEventFiltersBtn()
    {
        $this->showFilterButton = true;
    }

    public function render()
    {
        return view('livewire.event.event-list');
    }
}
