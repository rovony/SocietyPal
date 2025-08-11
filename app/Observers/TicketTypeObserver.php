<?php

namespace App\Observers;

use App\Models\TicketTypeSetting;

class TicketTypeObserver
{
    public function creating(TicketTypeSetting $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
