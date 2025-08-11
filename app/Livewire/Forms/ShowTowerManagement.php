<?php

namespace App\Livewire\Forms;

use App\Models\Tower;
use Livewire\Component;

class ShowTowerManagement extends Component
{

    public $towerId;

    public function mount($towerId)
    {
        $this->towerId = $towerId;
    }

    public function render()
    {
        $tower = Tower::with(['apartmentManagement', 'floors'])
        ->findOrFail($this->towerId);
        $unsoldApartmentsCount = $tower->apartmentManagement->where('status', 'not_sold')->count();
        $occupiedApartmentsCount = $tower->apartmentManagement->where('status', 'occupied')->count();
        return view('livewire.forms.show-tower-management',[
            'tower' => $tower,
            'unsoldApartmentsCount' => $unsoldApartmentsCount,
            'occupiedApartmentsCount' => $occupiedApartmentsCount,

        ]);
    }
}
