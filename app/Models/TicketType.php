<?php

namespace App\Models;

use App\Traits\HasSociety;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory, HasSociety;

    public function ticketAgentSettings()
    {
        return $this->hasMany(TicketAgentSetting::class, 'ticket_type_id');
    }
    
}
