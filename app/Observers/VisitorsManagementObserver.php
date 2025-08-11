<?php

namespace App\Observers;

use App\Models\VisitorManagement;

class VisitorsManagementObserver
{
    public function creating(VisitorManagement $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

        if (user()) {
            $model->user_id = user()->id;
        }

    }
}
