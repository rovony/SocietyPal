<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Livewire\Component;

class TotalOwnerCount extends Component
{
    public $ownerCount;

    public function mount()
    {
        $this->ownerCount = User::where('owner', 1)->where('status', 'active')->count();
    }

    public function render()
    {
        return view('livewire.dashboard.total-owner-count');
    }
}
