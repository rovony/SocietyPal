<?php

namespace App\Livewire\Forms;

use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;

class EditEvent extends Component
{
    use LivewireAlert, WithFileUploads;

    public $event;
    public $event_name;
    public $event_date;
    public $role;
    public $statuses;
    public $status;
    public $users = [];
    public $user_id = [];
    public $roles = [];
    public $selectedRoles = [];
    public $start_on_time;
    public $where;
    public $description;
    public $start_on_date;
    public $end_on_date;
    public $end_on_time;
    public $selectedUserNames = [];
    public $isOpen = false;
    public $userRoleWise = 'role-wise';
    public $userscollect;
    public $eventId;

    public function mount()
    {
        $this->statuses = [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $this->roles = Role::get();
        $this->event_name = $this->event->event_name;
        $this->where = $this->event->where;
        $this->description = $this->event->description;
        $this->status = $this->event->status;
        $this->eventId = $this->event->id;

        $this->start_on_date = Carbon::parse($this->event->start_date_time)->format('Y-m-d');
        $this->start_on_time = Carbon::parse($this->event->start_date_time)->timezone(timezone())->format('H:i');

        $this->end_on_date = Carbon::parse($this->event->end_date_time)->format('Y-m-d');
        $this->end_on_time = Carbon::parse($this->event->end_date_time)->timezone(timezone())->format('H:i');

        $this->users = User::get();

        $this->selectedRoles = $this->event->roles()->pluck('roles.id')->toArray();

        $this->selectedUserNames = $this->event->attendee()->pluck('user_id')->toArray();

        if (empty($this->selectedRoles) && !empty($this->selectedUserNames)) {
            $this->userRoleWise = 'user-wise';
        }
    }

    public function submitForm()
    {
        $rules = [
            'event_name' => 'required|string|max:255',
            'where' => 'required|string|max:255',
            'description' => 'required|string',
            'start_on_date' => 'required|date',
            'start_on_time' => 'required',
            'end_on_date' => 'required|date|after_or_equal:start_on_date',
            'end_on_time' => 'required',
            'status' => 'required',
            'selectedRoles' => $this->userRoleWise === 'role-wise' ? 'required|array|min:1' : 'nullable',
            'selectedUserNames' => $this->userRoleWise === 'user-wise' ? 'required|array|min:1' : 'nullable',
        ];
    
        if ($this->start_on_date === $this->end_on_date) {
            $rules['end_on_time'] = 'required|after:start_on_time';
        }
    
        $messages = [
            'end_on_time.after' => __('messages.endTimeAfterStartTime'),
        ];
    
        $this->validate($rules, $messages);

        $this->event->event_name = $this->event_name;
        $this->event->where = $this->where;
        $this->event->description = $this->description;
        
        $this->event->start_date_time = Carbon::parse($this->start_on_date . ' ' . $this->start_on_time, timezone())->setTimezone('UTC');
        $this->event->end_date_time = Carbon::parse($this->end_on_date . ' ' . $this->end_on_time, timezone())->setTimezone('UTC');
        $this->event->status = $this->status;
        $this->event->save();

        if ($this->userRoleWise === 'role-wise') {

            $this->event->roles()->sync($this->selectedRoles);

            $this->event->attendee()->delete();

            $usersInRoles = User::whereIn('role_id', $this->selectedRoles)->pluck('id');
            foreach ($usersInRoles as $userId) {
                EventAttendee::create([
                    'user_id' => $userId,
                    'event_id' => $this->event->id,
                ]);
            }

        } elseif ($this->userRoleWise === 'user-wise') {
            $this->event->roles()->detach();

            $this->event->attendee()->delete();

            foreach ($this->selectedUserNames as $userId) {
                EventAttendee::create([
                    'user_id' => $userId,
                    'event_id' => $this->event->id,
                ]);
            }
        }

        $this->dispatch('hideEditEvent');
        $this->alert('success', 'Event updated successfully!');
    }

    public function toggleSelectType($users)
    {
        if (in_array($users['id'], $this->selectedUserNames)) {
            $this->selectedUserNames = array_filter($this->selectedUserNames, fn($id) => $id !== $users['id']);
        } else {
            $this->selectedUserNames[] = $users['id'];
        }
        $this->selectedUserNames = array_values($this->selectedUserNames);
    }

    public function render()
    {
        return view('livewire.forms.edit-event');
    }
}
