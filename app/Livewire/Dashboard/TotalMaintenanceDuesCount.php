<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\MaintenanceApartment;
use App\Models\Tenant;

class TotalMaintenanceDuesCount extends Component
{
    public $maintenanceDues;

    public function mount()
    {
        $userId = user()->id;
        $societyId = user()->society_id;

        if (user_can('Show Maintenance')) {
            $this->maintenanceDues = MaintenanceApartment::where('paid_status', 'unpaid')
            ->whereHas('apartment', function ($query) use ($societyId) {
                $query->where('society_id', $societyId);
            })
            ->count();
        } else {    
            $this->maintenanceDues = MaintenanceApartment::query()
            ->with(['maintenanceManagement', 'apartment', 'tenants']);  
            if (isRole() == 'Owner') {
                $query = $this->maintenanceDues->whereHas('apartment', function ($subQuery) use ($userId, $societyId) {
                    $subQuery->where('user_id', $userId)->where('society_id', $societyId);
                });
            } 
            if (isRole() == 'Tenant') {
                $tenant = Tenant::where('user_id', $userId)->first(); 
                if ($tenant) {
                    $query = $this->maintenanceDues->join('apartment_tenant', 'apartment_tenant.apartment_id', 'maintenance_apartment.apartment_management_id')
                        ->where('apartment_tenant.tenant_id', $tenant->id)->whereHas('apartment', function ($subQuery) use ($societyId) {
                            $subQuery->where('society_id', $societyId);
                        })
                        ->select('maintenance_apartment.*');
                }
            }
            $this->maintenanceDues = $query->whereHas('maintenanceManagement', function ($subQuery) {
                $subQuery->where('status', 'published');
            })->where('paid_status','unpaid')->count();
        }   
    }

    public function render()
    {
        return view('livewire.dashboard.total-maintenance-dues-count');
    }
}
