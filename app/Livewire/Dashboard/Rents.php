<?php

namespace App\Livewire\Dashboard;

use App\Models\ApartmentManagement;
use App\Models\Rent;
use App\Models\Tenant;
use Livewire\Attributes\On;
use Livewire\Component;

class Rents extends Component
{
    public $showRent;
    public $showRentModal = false;
    public $rents;

    public function showRentDetails($id)
    {
        $this->showRent = Rent::findOrFail($id);
        $this->showRentModal = true;
    }

    #[On('hideRentDetail')]
    public function hideRentDetail()
    {
        $this->showRentModal = false;
    }

    public function mount()
    {
        if (user_can('Show Rent')) {
            $this->rents = Rent::with('tenant', 'apartment')->where('status', 'unpaid')->get();
        } else {
            if (isRole() == 'Tenant') {
                $tenant = Tenant::where('user_id', user()->id)->first();
                if ($tenant) {
                    $this->rents = Rent::with('tenant.user','apartment')->where('status', 'unpaid')->where('tenant_id', $tenant->id)->get();
                }
            } elseif (isRole() == 'Owner') {
                $apartmentManagementIds = ApartmentManagement::where('user_id', user()->id)->pluck('id');
                $tenantIds = Tenant::whereHas('apartments', function ($query) use ($apartmentManagementIds) {
                    $query->whereIn('apartment_tenant.apartment_id', $apartmentManagementIds);
                })->pluck('id');
                $this->rents = Rent::with('tenant.user','apartment')->where('status', 'unpaid')->whereIn('tenant_id', $tenantIds)
                    ->get();
            }
        }
    }

    public function render()
    {
        return view('livewire.dashboard.rents');
    }
}
