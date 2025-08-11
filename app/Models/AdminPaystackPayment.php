<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminPaystackPayment extends Model
{
    use HasFactory;
    protected $table = 'paystack_payments';
    protected $guarded = ['id'];

    public function maintenanceApartment(): BelongsTo
    {
        return $this->belongsTo(MaintenanceApartment::class);
    }

}

