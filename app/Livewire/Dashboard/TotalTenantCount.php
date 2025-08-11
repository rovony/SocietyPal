<?php

namespace App\Livewire\Dashboard;

use App\Models\ApartmentManagement;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Component;

class TotalTenantCount extends Component
{
    public $tenantCount;

    public function mount()
    {
        if (user_can('Show Tenant')) {
            $this->tenantCount = User::where('tenant', 1)->where('status', 'active')->count();
        } else {
            $this->tenantCount = Tenant::whereHas('apartments', function ($query) {
                $query->where('user_id', user()->id); 
            })->count();
         }

    }
    
    public function render()
    {
        return view('livewire.dashboard.total-tenant-count');
    }
}
