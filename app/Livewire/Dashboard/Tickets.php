<?php

namespace App\Livewire\Dashboard;

use App\Models\Ticket;
use Livewire\Component;

class Tickets extends Component
{
    public $tickets;

    public function mount()
    {
        if (user_can('Show Tickets')) {
            $this->tickets = Ticket::with('user')
                ->whereIn('status', ['open', 'pending'])
                ->get();
        } else {
            $this->tickets = Ticket::with('user')
                ->where('user_id', user()->id)
                ->whereIn('status', ['open', 'pending'])
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.dashboard.tickets');
    }
}
