<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\ApartmentManagement;

class TotalUnsoldApartmentCount extends Component
{
    public $unsoldApartmentCount;

    public function mount()
    {
        $this->unsoldApartmentCount = ApartmentManagement::where('status','not_sold')->count();
    }

    public function render()
    {
        return view('livewire.dashboard.total-unsold-apartment-count');
    }
}
