<?php

namespace App\Models;

use App\Traits\HasSociety;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceManagementApartment extends Model
{
    use HasFactory;

    protected $table = 'service_management_apartment';

    protected $fillable = [
        'service_management_id', 'apartment_management_id'
    ];

    /**
     * Get the related ServiceManagement entries.
     */
    public function serviceManagement()
    {
        return $this->belongsTo(ServiceManagement::class, 'service_management_id');
    }

    /**
     * Get the apartment management associated with this pivot table.
     */
    public function apartmentManagement()
    {
        return $this->belongsTo(ApartmentManagement::class, 'apartment_management_id');
    }

}
