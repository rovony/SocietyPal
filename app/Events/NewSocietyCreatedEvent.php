<?php

namespace App\Events;

use App\Models\Society;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSocietyCreatedEvent
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $society;

    public function __construct(Society $society)
    {
        $this->society = $society;
    }
}
