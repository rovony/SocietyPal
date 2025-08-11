<?php

namespace App\Observers;

use App\Models\Rent;

class RentObserver
{

    public function creating(Rent $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }

}
