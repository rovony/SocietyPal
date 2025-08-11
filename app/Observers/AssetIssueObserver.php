<?php

namespace App\Observers;

use App\Models\AssetIssue;

class AssetIssueObserver
{
    public function creating(AssetIssue $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
