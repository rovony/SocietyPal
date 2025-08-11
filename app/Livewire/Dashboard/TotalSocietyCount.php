<?php

namespace App\Livewire\Dashboard;

use App\Models\Society;
use Livewire\Component;

class TotalSocietyCount extends Component
{
    public $orderCount;
    public $percentChange;
    
    public function mount()
    {
        $this->orderCount = Society::count();
    }

    public function render()
    {
        return view('livewire.dashboard.total-society-count');
    }
}
