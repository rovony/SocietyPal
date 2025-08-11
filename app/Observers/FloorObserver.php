<?php

namespace App\Observers;

use App\Models\Floor;

class FloorObserver
{
    public function creating(Floor $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
