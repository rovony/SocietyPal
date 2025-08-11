<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRole extends Model
{
    use HasFactory;
    protected $table = 'event_role';

    protected $fillable = [
        'event_id',
        'role_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
