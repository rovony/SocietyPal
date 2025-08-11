<?php

namespace App\Models;

use App\Traits\HasSociety;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory, HasSociety;

    protected $table = 'events';

    protected $fillable = [
        'society_id',
        'event_name',
        'where',
        'description',
        'start_date_time',
        'end_date_time',
    ];

    protected $casts = [
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
    ];

    public function eventRoles()
    {
        return $this->hasMany(EventRole::class, 'event_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'event_role', 'event_id', 'role_id');
    }

    public function attendee()
    {
        return $this->hasMany(EventAttendee::class);
    }

}
