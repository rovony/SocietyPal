<?php

namespace App\Livewire\Forms;

use App\Models\Event;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowEvent extends Component
{
    use LivewireAlert;

    public $event;
    public $eventId;
    
    public function mount()
    {
        $this->eventId = $this->event->id;
        $this->event = Event::with('attendee')->findOrFail($this->eventId);
    }

    public function setEventStatus(string $status, int $id)
    {
        $event = Event::find($id);
        if($event) {
            $event->status = $status;
            $event->save();
            $this->event = $event;
        }
        $this->alert('success', __('messages.savedSuccessfully'));
    }


    public function render()
    {
        return view('livewire.forms.show-event');
    }
}
