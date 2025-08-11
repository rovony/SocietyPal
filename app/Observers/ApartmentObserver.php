<?php

namespace App\Observers;

use App\Models\Apartment;

class ApartmentObserver
{
    public function creating(Apartment $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
