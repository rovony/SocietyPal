<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{

    public function creating(User $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }
        $model->locale = global_setting()->locale;

    }
}
