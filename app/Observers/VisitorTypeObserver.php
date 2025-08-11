<?php

namespace App\Observers;

use App\Models\VisitorTypeSettingsModel;

class VisitorTypeObserver
{
    public function creating(VisitorTypeSettingsModel $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
