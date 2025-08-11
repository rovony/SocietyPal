<?php

namespace App\Models;

use App\Traits\HasSociety;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceClockInOut extends Model
{
    use HasFactory, HasSociety;

    protected $table = 'service_clock_in_out';

    protected $fillable = [
        'society_id',
        'service_management_id',
        'added_by',
        'clock_in_date',
        'clock_in_time',
        'clock_out_date',
        'clock_out_time',
        'duration_minutes',
        'status',
    ];

    /**
     * Relationship: Belongs to Service Management (Service Provider)
     */
    public function service()
    {
        return $this->belongsTo(ServiceManagement::class, 'service_management_id');
    }

    /**
     * Relationship: Belongs to User (Guard/Admin who added the record)
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Scope: Get only clocked-in service providers
     */
    public function scopeClockedIn($query)
    {
        return $query->where('status', 'clock_in');
    }

    /**
     * Scope: Get only clocked-out service providers
     */
    public function scopeClockedOut($query)
    {
        return $query->where('status', 'clock_out');
    }

    /**
     * Calculate Duration in Minutes
     */
    public function calculateDuration()
    {
        if ($this->clock_in_date && $this->clock_in_time && $this->clock_out_date && $this->clock_out_time) {
            $clockIn = Carbon::createFromFormat('Y-m-d H:i', "{$this->clock_in_date} {$this->clock_in_time}");
            $clockOut = Carbon::createFromFormat('Y-m-d H:i', "{$this->clock_out_date} {$this->clock_out_time}");

            return $clockIn->diffInMinutes($clockOut);
        }

        return null;
    }

    /**
     * Update Duration Field After Check-out
     */
    public function updateDuration()
    {
        $this->duration_minutes = $this->calculateDuration();
        $this->save();
    }
}
