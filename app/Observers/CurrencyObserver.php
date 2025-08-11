<?php

namespace App\Observers;

use App\Models\Currency;

class CurrencyObserver
{
    public function creating(Currency $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
