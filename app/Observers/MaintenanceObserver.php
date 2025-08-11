<?php

namespace App\Observers;

use App\Models\Maintenance;
use Illuminate\Database\Eloquent\Model;

class MaintenanceObserver
{
    public function creating(Maintenance $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
