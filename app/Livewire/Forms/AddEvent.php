<?php

namespace App\Livewire\Forms;

use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\Role;
use App\Models\User;
use App\Notifications\EventNotification;
use Carbon\Carbon;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;


class AddEvent extends Component
{
    use LivewireAlert, WithFileUploads;

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


    public function mount()
    {
        $this->statuses = [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        $this->start_on_date = Carbon::now()->timezone(timezone())->format('Y-m-d');
        $this->start_on_time = Carbon::now()->timezone(timezone())->format('H:i');
        $this->roles = Role::get();
        $this->users = User::get();
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

        $event = new Event();
        $event->event_name = $this->event_name;
        $event->where = $this->where;
        $event->description = $this->description;

        $event->start_date_time = Carbon::parse($this->start_on_date . ' ' . $this->start_on_time, timezone())->setTimezone('UTC');
        $event->end_date_time = Carbon::parse($this->end_on_date . ' ' . $this->end_on_time, timezone())->setTimezone('UTC');

        $event->status = $this->status;
        $event->save();

        if ($this->userRoleWise === 'role-wise') {
            $event->roles()->sync($this->selectedRoles);

            $usersInRoles = User::whereIn('role_id', $this->selectedRoles)->pluck('id');
            foreach ($usersInRoles as $userId) {
                EventAttendee::create([
                    'user_id' => $userId,
                    'event_id' => $event->id,
                ]);
            }
            $event->assign_to = 'role';
            $event->save();

            $attendeeUserIds = EventAttendee::where('event_id', $event->id)->pluck('user_id');
            $usersToNotify = User::whereIn('id', $attendeeUserIds)->get();
            foreach ($usersToNotify as $user) {
                $user->notify(new EventNotification($event));
            }
        } elseif ($this->userRoleWise === 'user-wise') {
            foreach ($this->selectedUserNames as $userId) {
                EventAttendee::create([
                    'user_id' => $userId,
                    'event_id' => $event->id,
                ]);
            }
            $event->assign_to = 'user';
            $event->save();
            $usersToNotify = User::whereIn('id', $this->selectedUserNames)->get();
            foreach ($usersToNotify as $user) {
                $user->notify(new EventNotification($event));
            }
        }

        $this->dispatch('hideAddEvent');
        $this->alert('success', 'Event created successfully!');

        $this->reset(['event_name', 'where', 'description', 'start_on_date', 'start_on_time', 'end_on_date', 'end_on_time', 'status', 'selectedRoles', 'selectedUserNames']);
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
        return view('livewire.forms.add-event');
    }
}
