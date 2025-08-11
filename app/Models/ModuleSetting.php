<?php

namespace App\Models;

use App\Traits\HasSociety;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class ModuleSetting extends Model
{
    use HasFactory, HasSociety;

    protected $fillable = [
        'society_id',
        'module_name',
        'status',
        'type',
    ];

    /**
     * Get the society that owns the module setting.
     */
    public function society()
    {
        return $this->belongsTo(Society::class);
    }
}
