<?php

namespace App\Observers;

use App\Models\Notice;

class NoticeObserver
{
    public function creating(Notice $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
