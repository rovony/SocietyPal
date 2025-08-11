<?php

namespace App\Livewire\Dashboard;

use App\Models\Tenant;
use App\Models\UtilityBillManagement;
use Livewire\Attributes\On;
use Livewire\Component;

class UtilityBills extends Component
{
    public $utilityBills;
    public $selectedUtilityBills;
    public $showUtilityBillModal;

    public function showUtilityBill($id)
    {
        $this->selectedUtilityBills = UtilityBillManagement::findOrFail($id);
        $this->showUtilityBillModal = true;
    }

    #[On('hideUtilityBillModal')]
    public function hideUtilityBillModal()
    {
        $this->showUtilityBillModal = false;
    }

    public function mount()
    {        
        $loggedInUser = user()->id;
        if (user_can('Show Utility Bills')) {
            $this->utilityBills = UtilityBillManagement::where('status','unpaid')->get();
        } else {
            if(isRole() == 'Tenant'){
                $tenant = Tenant::where('user_id', $loggedInUser)->first();
                $apartmentIds = $tenant->apartments()->pluck('apartment_tenant.apartment_id');
                $this->utilityBills = UtilityBillManagement::where('status', 'unpaid')
                    ->whereIn('apartment_id', $apartmentIds)
                    ->get();
            }
            elseif (isRole() == 'Owner'){
                $this->utilityBills = UtilityBillManagement::where('status', 'unpaid')
                ->whereHas('apartment', function ($q) use ($loggedInUser) {
                    $q->where('user_id', $loggedInUser);
                })->get();
           }
        }
    }

    public function render()
    {
        return view('livewire.dashboard.utility-bills');
    }
}
