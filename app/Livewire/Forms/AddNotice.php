<?php

namespace App\Livewire\Forms;

use App\Models\Notice;
use App\Models\User;
use App\Notifications\NoticeBoardNotification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Role;
use Livewire\Component;

class AddNotice extends Component
{
    use LivewireAlert;

    public $title;
    public $description;
    public $roles = [];
    public $selectedRoles = [];

    public function mount()
    {
        $this->roles = Role::where('name', '<>', 'Super Admin')->get();
    }

    public function submitForm()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'selectedRoles' => 'required|array|min:1',
        ]);

        $notice = new Notice();
        $notice->title = $this->title;
        $notice->description = $this->description;
        $notice->save();
        $notice->roles()->sync($this->selectedRoles);

        try {
            $usersToNotify = User::whereHas('roles', function ($query) {
                $query->whereIn('id', $this->selectedRoles);
            })->get();

            foreach ($usersToNotify as $user) {
                $user->notify(new NoticeBoardNotification($notice));
            }

            $this->resetForm();
            $this->dispatch('hideAddNotice');

            $this->alert('success', __('messages.noticeAdded'));
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end'
            ]);
        }
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->selectedRoles = [];
    }

    public function render()
    {
        return view('livewire.forms.add-notice');
    }
}
