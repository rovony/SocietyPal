<?php

namespace App\Observers;

use App\Models\Forum;

class ForumObserver
{

    public function creating(Forum $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }
    }
}
