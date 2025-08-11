<?php

namespace App\Observers;

use App\Models\PushNotification;

class PushNotificationObserver
{

    public function creating(PushNotification $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }
    }
}
