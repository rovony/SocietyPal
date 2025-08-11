<?php

namespace App\Observers;

use App\Models\BookAmenity;

class BookAmenityObserver
{
    public function creating(BookAmenity $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
