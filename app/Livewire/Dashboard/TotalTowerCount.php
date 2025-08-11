<?php

namespace App\Livewire\Dashboard;

use App\Models\Tower;
use Livewire\Component;

class TotalTowerCount extends Component
{
    public $towerCount;

    public function mount()
    {
        $this->towerCount = Tower::count();
    }

    public function render()
    {
        return view('livewire.dashboard.total-tower-count');
    }
}
