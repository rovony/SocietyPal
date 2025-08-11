<?php

namespace App\Observers;

use App\Models\ServiceManagement;

class ServiceManagementObserver
{
    public function creating(ServiceManagement $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
