<?php

namespace App\Observers;

use App\Models\OfflinePaymentMethod;
use App\Models\User;

class OfflinePaymentMethodObserver
{

    public function creating(OfflinePaymentMethod $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }

}
