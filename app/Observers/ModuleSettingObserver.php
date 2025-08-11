<?php

namespace App\Observers;

use App\Models\ModuleSetting;

class ModuleSettingObserver
{

    public function creating(ModuleSetting $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }

    }

}
