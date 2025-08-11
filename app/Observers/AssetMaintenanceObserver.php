<?php

namespace App\Observers;

use App\Models\AssetMaintenance;

class AssetMaintenanceObserver
{
    public function creating(AssetMaintenance $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
