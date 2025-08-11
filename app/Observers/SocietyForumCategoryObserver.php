<?php

namespace App\Observers;

use App\Models\SocietyForumCategory;

class SocietyForumCategoryObserver
{

    public function creating(SocietyForumCategory $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }
    }
}
