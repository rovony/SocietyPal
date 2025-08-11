<?php

namespace App\Livewire\Dashboard;

use App\Models\Notice;
use Livewire\Attributes\On;
use Livewire\Component;

class NoticeBoard extends Component
{
    public $notices;
    public $showNotice;
    public $showNoticeDetailModal = false;

    public function showNoticeDetail($id)
    {
        $this->showNotice = Notice::findOrFail($id);
        $this->showNoticeDetailModal = true;
    }

    #[On('hideNoticeDetail')]
    public function hideNoticeDetail()
    {
        $this->showNoticeDetailModal = false;
    }

    public function render()
    {
        if (user_can('Show Notice Board')) {
            $this->notices = Notice::orderBy('created_at', 'desc')->take(5)->get();
        } else {
            $userRoles = user()->roles->pluck('id')->toArray();

            $this->notices = Notice::where(function ($q) use ($userRoles) {
                $q->whereHas('roles', function ($query) use ($userRoles) {
                    $query->whereIn('roles.id', $userRoles);
                });
            })->orderBy('created_at', 'desc')->take(5)->get();
        }
        return view('livewire.dashboard.notice-board');
    }
}
