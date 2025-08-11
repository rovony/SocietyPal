<?php

namespace App\Observers;

use App\Models\Tower;

class TowerObserver
{
    public function creating(Tower $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
