<?php

namespace App\Observers;

use App\Models\FileStorage;

class FileStorageObserver
{

    public function creating(FileStorage $model)
    {
        if (society()) {
            $model->society_id = society()->id;
        }
    }

}
