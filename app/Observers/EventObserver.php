<?php

namespace App\Observers;

use App\Models\Event;

class EventObserver
{
    public function creating(Event $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
