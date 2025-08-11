<?php

namespace App\Observers;

use App\Models\AssetManagement;

class AssetManagementObserver
{
    public function creating(AssetManagement $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
