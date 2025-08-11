<?php

namespace App\Livewire\Event;

use Carbon\Carbon;
use App\Models\Event;
use Livewire\Component;
use Livewire\Attributes\On;

class CalenderView extends Component
{
    public $showEventDetailModal = false;
    public $showEvent;
    public $selectedEventId;
    public $showEventDetail = false;
    protected $listeners = ['showEventDetail'];

   

    public function showEventDetail($id)
    {
        $this->showEvent = Event::findOrFail($id);
        $this->showEventDetailModal = true;
    }

    #[On('hideEventDetail')]
    public function hideEventDetail()
    {
        $this->showEventDetailModal = false;
    }

    public function render()
    {
        return view('livewire.event.calender-view');
    }
}
