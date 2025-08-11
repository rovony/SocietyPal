<?php

namespace App\Observers;

use App\Models\ServiceType;

class ServiceTypeObserver
{
    public function creating(ServiceType $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
