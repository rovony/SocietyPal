<?php

namespace App\Observers;

use App\Models\Amenities;

class AmenitiesObserver
{
    public function creating(Amenities $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
