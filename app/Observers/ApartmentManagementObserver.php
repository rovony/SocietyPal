<?php

namespace App\Observers;

use App\Models\ApartmentManagement;

class ApartmentManagementObserver
{
    public function creating(ApartmentManagement $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
