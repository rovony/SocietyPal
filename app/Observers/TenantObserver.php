<?php

namespace App\Observers;

use App\Models\Tenant;

class TenantObserver
{
    public function creating(Tenant $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }

}
