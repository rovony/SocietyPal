<?php

namespace App\Livewire\Forms;

use App\Models\Notice;
use Livewire\Component;

class ShowNotice extends Component
{
    public $notice;
    public $noticeId;
    
    public function mount()
    {
        $this->noticeId = $this->notice->id;
        $this->notice = Notice::with('roles')->findOrFail($this->noticeId);
    }

    public function render()
    {
        return view('livewire.forms.show-notice');
    }
}
