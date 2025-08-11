<?php

namespace App\Livewire\Event;

use App\Exports\EventsExport;
use App\Models\Event;
use App\Models\Role;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\Attributes\On;

class EventTable extends Component
{
    use LivewireAlert;

    public $search;
    public $notice;
    public $roles;
    public $showEditEventModal = false;
    public $showEventDetailModal = false;
    public $clearFilterButton = false;
    public $showFilters = false;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $noticesData;
    public $event;
    public $eventsData;
    public $confirmDeleteEventModal = false;
    public $confirmSelectedDeleteEventModal = false;
    public $editEvent;
    public $showEvent;
    public $deleteEvent;
    public $filterStatuses = [];

    public function mount()
    {
        $this->eventsData = Event::get();
    }

    public function showEditEvent($id)
    {
        $this->editEvent = Event::with('roles')->findOrFail($id);
        $this->showEditEventModal = true;
    }

    #[On('hideEditEvent')]
    public function hideEditEvent()
    {
        $this->showEditEventModal = false;
    }

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

    public function showDeleteEvent($id)
    {
        $this->deleteEvent = Event::findOrFail($id);
        $this->confirmDeleteEventModal = true;
    }

    public function deleteMeetingEvent($id)
    {
        Event::destroy($id);
        $this->confirmDeleteEventModal = false;
        $this->event = '';

        $this->alert('success', __('messages.eventDeleted'));
    }

    public function showSelectedDeleteEvent()
    {
        $this->confirmSelectedDeleteEventModal = true;
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->eventsData->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function deleteSelected()
    {
        Event::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions = false;
        $this->confirmSelectedDeleteEventModal = false;
        $this->alert('success', __('messages.eventDeleted'));
    }


    #[On('showEventFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->filterStatuses = [];
        $this->search = '';
        $this->clearFilterButton = false;
    }

    #[On('exportEvent')]
    public function exportEvent()
    {
        return (new EventsExport($this->search, $this->filterStatuses))->download('events-' . now()->toDateTimeString() . '.xlsx');
    }

    public function setEventStatus(string $status, int $id)
    {
        $event = Event::find($id);
        if($event) {
            $event->status = $status;
            $event->save();
        }
        $this->alert('success', __('messages.savedSuccessfully'));

        $this->redirect(route('events.index'), navigate: true);
    }

    public function render()
    {
        $query = Event::query();

        if ($this->search != '') {
            $query->where(function ($q) {
                $q->where('event_name', 'like', '%' . $this->search . '%')
                ->orWhere('where', 'like', '%' . $this->search . '%');
            });
        }

        $this->clearFilterButton = false;

        if (!empty($this->filterStatuses)) {
            $query = $query->whereIn('status', $this->filterStatuses);
            $this->clearFilterButton = true;
        }

        if (user_can('Show Event')) {
            $events = $query->paginate(10);
        } else {
            $query->whereHas('attendee', function ($q) {
                $q->where('user_id', user()->id);
            });

            $events = $query->paginate(10);
        }

        return view('livewire.event.event-table', [
            'events' => $events
        ]);
    }
}
