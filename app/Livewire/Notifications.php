<?php

namespace App\Livewire;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Notifications extends Component
{
    use LivewireAlert;

    public $notifications;
    public $showNotifications = false;
    public $unreadCount;

    protected $listeners = ['refreshNotifications' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = auth()->user()->getUnreadNotifications();
        $this->unreadCount = $this->notifications->count();
    }

    public function markAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
        $this->alert('success', __('messages.markedAsRead'));
    }

    public function toggleDropdown()
    {
        $this->showNotifications = !$this->showNotifications;
    }

    public function render()
    {
        return view('livewire.notifications');
    }
}
