<?php

namespace App\Observers;

use App\Models\UtilityBillManagement;

class UtilityBillManagementObserver
{
    public function creating(UtilityBillManagement $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
