<?php

namespace App\Observers;

use App\Models\TicketAgentSetting;

class TicketAgentObserver
{
    public function creating(TicketAgentSetting $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
