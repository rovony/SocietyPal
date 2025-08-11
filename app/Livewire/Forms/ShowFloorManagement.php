<?php

namespace App\Livewire\Forms;

use App\Models\Floor;
use Livewire\Component;

class ShowFloorManagement extends Component
{
    public $floorId;

    public function mount($floorId)
    {
        $this->floorId = $floorId;
    }

    public function render()
    {
        $floors = Floor::with(['apartmentManagement', 'tower'])
        ->findOrFail($this->floorId);
        $unsoldApartmentsCount = $floors->apartmentManagement->where('status', 'not_sold')->count();
        $occupiedApartmentsCount = $floors->apartmentManagement->where('status', 'occupied')->count();
        return view('livewire.forms.show-floor-management',[
            'floors' => $floors,
            'unsoldApartmentsCount' => $unsoldApartmentsCount,
            'occupiedApartmentsCount' => $occupiedApartmentsCount,
    ]);
    }
}
