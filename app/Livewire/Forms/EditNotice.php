<?php

namespace App\Livewire\Forms;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Role;

class EditNotice extends Component
{
    use LivewireAlert;

    public $notice;
    public $title;
    public $description;
    public $roles = [];
    public $selectedRoles = [];

    public function mount()
    {
        $this->title = $this->notice->title;
        $this->description = $this->notice->description;
        $this->selectedRoles = $this->notice->roles->pluck('id')->toArray();
        $this->roles = Role::where('name', '<>', 'Super Admin')->get();
    }

    public function submitForm()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'selectedRoles' => 'required|array|min:1',
        ]);

        $this->notice->update([
            'title' => $this->title,
            'description' => $this->description,
        ]);

        $this->notice->roles()->sync($this->selectedRoles);

        $this->dispatch('hideEditNotice');
        $this->alert('success', __('messages.noticeUpdated'));
    }

    public function render()
    {
        return view('livewire.forms.edit-notice');
    }
}
