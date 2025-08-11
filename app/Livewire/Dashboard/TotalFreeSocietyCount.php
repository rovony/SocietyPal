<?php

namespace App\Livewire\Dashboard;

use App\Models\Society;
use Livewire\Component;

class TotalFreeSocietyCount extends Component
{
    public $orderCount;
    public $percentChange;
    
    public function mount()
    {
        $this->orderCount = Society::where('license_type', 'free')->count();
    }

    public function render()
    {
        return view('livewire.dashboard.total-free-society-count');
    }
}
