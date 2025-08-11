<?php

namespace App\Observers;

use App\Models\AssetsCategory;

class AssetCategoryObserver
{
    public function creating(AssetsCategory $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }
}
