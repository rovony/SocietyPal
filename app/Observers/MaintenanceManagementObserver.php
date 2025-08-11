<?php

namespace App\Observers;

use App\Models\ApartmentManagement;
use App\Models\Maintenance;
use App\Models\MaintenanceManagement;

class MaintenanceManagementObserver
{
    public function creating(MaintenanceManagement $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }

    public function created(MaintenanceManagement $maintenance)
    {
        $this->distributeCosts($maintenance);
    }

    public function updated(MaintenanceManagement $maintenance)
    {
        $this->distributeCosts($maintenance);
    }

    protected function distributeCosts(MaintenanceManagement $maintenance)
    {
        $maintenanceType = Maintenance::where('society_id', $maintenance->society_id)->first();
        $apartments = ApartmentManagement::with('apartments')->where('society_id', $maintenance->society_id)->get();
        $totalAdditionalCost = json_decode($maintenance->additional_details, true);
        $totalAdditionalCost = array_sum(array_column($totalAdditionalCost, 'cost'));

        foreach ($apartments as $apartment) {
            $apartmentCost = 0;

            if ($maintenanceType->cost_type == 'unitType') {
                $apartmentArea = $apartment->apartment_area ?? 0;
                $apartmentCost = ($maintenanceType->set_value * $apartmentArea) + $totalAdditionalCost;
            } elseif ($maintenanceType->cost_type == 'fixedValue') {
                $maintenanceValue = $apartment->apartments?->maintenance_value ?? 0;
                $apartmentCost = $maintenanceValue + $totalAdditionalCost;
            }

            $maintenance->maintenanceApartments()->updateOrCreate(
                ['apartment_management_id' => $apartment->id],
                [
                    'cost' => $apartmentCost,
                    'paid_status' => 'unpaid',
                    'payment_date' => null,
                    'payment_proof' => null,
                ]
            );
        }
    }
}
