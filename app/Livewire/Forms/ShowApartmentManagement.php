<?php

namespace App\Livewire\Forms;

use App\Models\Rent;
use App\Models\Society;
use Livewire\Component;
use App\Models\Currency;
use App\Models\ApartmentManagement;
use App\Models\MaintenanceApartment;
use App\Models\UtilityBillManagement;

class ShowApartmentManagement extends Component
{

    public $apartmentId;
    public $currencySymbol;

    public function mount($apartmentId)
    {
        $this->apartmentId = $apartmentId;
    }

    public function render()
    {
        $apartment = ApartmentManagement::with(['parkingCodes.apartmentParking','user', 'tenants', 'tenants.user', 'towers', 'floors'])
        ->findOrFail($this->apartmentId);

        $totalPaidAmount = UtilityBillManagement::where('apartment_id', $this->apartmentId)->where('status', 'paid')->sum('bill_amount');
        $totalUnpaidAmount = UtilityBillManagement::where('apartment_id', $this->apartmentId)->where('status', 'unpaid')->sum('bill_amount');

        $totalPaidRent = Rent::whereHas('apartment', function ($query) {
            $query->where('apartment_id', $this->apartmentId);
        })->where('status', 'paid')->sum('rent_amount');

        $totalUnpaidRent = Rent::whereHas('apartment', function ($query) {
            $query->where('apartment_id', $this->apartmentId);
        })->where('status', 'unpaid')->sum('rent_amount');

        $totalPaidMaintenance = MaintenanceApartment::whereHas('apartment', function ($query) {
            $query->where('apartment_management_id', $this->apartmentId);
        })->where('paid_status','paid')->sum('cost');

        $totalUnpaidMaintenance = MaintenanceApartment::whereHas('apartment', function ($query) {
            $query->where('apartment_management_id', $this->apartmentId);
        })->where('paid_status','unpaid')->sum('cost');

        return view('livewire.forms.show-apartment-management', [
            'apartment' => $apartment,
            'totalPaidAmount' => $totalPaidAmount,
            'totalUnpaidAmount' => $totalUnpaidAmount,
            'totalPaidRent' => $totalPaidRent,
            'totalUnpaidRent' => $totalUnpaidRent,
            'totalPaidMaintenance' => $totalPaidMaintenance,
            'totalUnpaidMaintenance' => $totalUnpaidMaintenance,
        ]);
    }

}
