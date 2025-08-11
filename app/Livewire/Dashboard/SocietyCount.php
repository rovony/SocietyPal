<?php

namespace App\Livewire\Dashboard;

use App\Models\Society;
use Livewire\Component;

class SocietyCount extends Component
{
    public $orderCount;
    public $percentChange;
    
    public function mount()
    {
        $this->orderCount = Society::whereDate('societies.created_at', '>=', now()->startOfDay()->toDateTimeString())->whereDate('societies.created_at', '<=', now()->endOfDay()->toDateTimeString())
            ->count();
        
        $yesterdayCount = Society::whereDate('societies.created_at', '>=', now()->subDay()->startOfDay()->toDateTimeString())->whereDate('societies.created_at', '<=', now()->subDay()->endOfDay()->toDateTimeString())
            ->count();

        $orderDifference = ($this->orderCount - $yesterdayCount);

        $this->percentChange  = (($orderDifference / ($yesterdayCount == 0 ? 1 : $yesterdayCount)) * 100);

    }

    public function render()
    {
        return view('livewire.dashboard.society-count');
    }
}
