<?php

namespace App\Observers;

use App\Models\ServiceClockInOut;

class ServiceClockInOutObserver
{
    public function creating(ServiceClockInOut $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
