<?php

namespace App\Observers;

use App\Models\CommonAreaBills;

class CommonBillAreaObserver
{
    public function creating(CommonAreaBills $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }
    }
}
