<?php

namespace App\Observers;

use App\Models\BillType;

class BillTypeObserver
{
    public function creating(BillType $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
