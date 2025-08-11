<?php

namespace App\Livewire\Dashboard;

use App\Models\Society;
use Livewire\Component;

class TotalPaidSocietyCount extends Component
{
    public $orderCount;
    public $percentChange;
    
    public function mount()
    {
        $this->orderCount = Society::where('license_type', 'paid')->count();
    }

    public function render()
    {
        return view('livewire.dashboard.total-paid-society-count');
    }
}
