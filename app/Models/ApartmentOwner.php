<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApartmentOwner extends Model
{
    protected $table = 'apartment_owner';

    protected $fillable = [
        'user_id',
        'apartment_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apartment()
    {
        return $this->belongsTo(ApartmentManagement::class, 'apartment_id');
    }
}
