<?php

namespace App\Models;

use App\Traits\HasSociety;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceManagement extends Model
{
    use HasFactory, HasSociety;

    // Define the table associated with the model
    protected $table = 'maintenance_management';

    // Define the fillable properties (columns you want to mass assign)
    protected $fillable = [
        'society_id',
        'month',
        'year',
        'additional_cost',
        'status',
        'additional_details',
        'total_additional_cost',
        'payment_due_date',
    ];

    /**
     * Get the maintenance type associated with this management record.
     */
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id');
    }

    /**
     * Get the apartments associated with this maintenance management record.
     */
    public function maintenanceApartments()
    {
        return $this->hasMany(MaintenanceApartment::class);
    }
}
