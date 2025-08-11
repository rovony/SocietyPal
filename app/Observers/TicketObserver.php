<?php

namespace App\Observers;

use App\Models\Ticket;

class TicketObserver
{

    public function creating(Ticket $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }

}
