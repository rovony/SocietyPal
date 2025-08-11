<?php

namespace App\Observers;

use App\Models\Role;

class RoleObserver
{

    public function creating(Role $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }

}
