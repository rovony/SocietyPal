<?php

namespace App\Livewire\Ticket;

use App\Models\Ticket;
use Livewire\Attributes\On;
use Livewire\Component;

class TicketList extends Component
{
    public $search;
    public $showAddTicket = false;
    public $showFilterButton = true;
    public $totalTickets;
    public $openTickets;
    public $pendingTickets;
    public $resolvedTickets;
    public $closedTickets;

    #[On('hideAddTicket')]
    public function hideAddTicket()
    {
        $this->showAddTicket = false;
        $this->js('window.location.reload()');
    }

    #[On('clearTicketFilter')]
    public function clearTicketFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('hideTicketFilters')]
    public function hideTicketFiltersBtn()
    {
        $this->showFilterButton = true;
    }

    public function mount()
    {
        if (user_can('Show Tickets')) {
            $this->totalTickets = Ticket::count();
            $this->openTickets = Ticket::where('status', 'open')->count();
            $this->pendingTickets = Ticket::where('status', 'pending')->count();
            $this->resolvedTickets = Ticket::where('status', 'resolved')->count();
            $this->closedTickets = Ticket::where('status', 'closed')->count();
        } else {
            $this->totalTickets = Ticket::where('user_id', user()->id)->count();
            $this->openTickets = Ticket::where('status', 'open')->where('user_id', user()->id)->count();
            $this->pendingTickets = Ticket::where('status', 'pending')->where('user_id', user()->id)->count();
            $this->resolvedTickets = Ticket::where('status', 'resolved')->where('user_id', user()->id)->count();
            $this->closedTickets = Ticket::where('status', 'closed')->where('user_id', user()->id)->count();
        }
    }

    public function render()
    {
        return view('livewire.ticket.ticket-list');
    }
}
    