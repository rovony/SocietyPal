<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\ApartmentManagement;
use App\Models\Tenant;

class TotalApartmentCount extends Component
{
    public $apartmentCount;

    public function mount()
    {
        if (user_can('Show Apartment')) {
            $this->apartmentCount = ApartmentManagement::count();
        } else {
            if (isRole() == 'Owner'){
                $this->apartmentCount = ApartmentManagement::where('user_id', user()->id)->count();  
            }
            elseif (isRole() == 'Tenant'){   
                $tenant = Tenant::where('user_id', user()->id)->first();
                $this->apartmentCount = $tenant->apartments()->count();
            }          
        }
    }

    public function render()
    {
        return view('livewire.dashboard.total-apartment-count');
    }
}
