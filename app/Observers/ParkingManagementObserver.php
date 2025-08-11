<?php

namespace App\Observers;

use App\Models\ParkingManagementSetting;

class ParkingManagementObserver
{
    public function creating(ParkingManagementSetting $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
