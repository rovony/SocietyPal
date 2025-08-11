<?php

namespace App\Livewire\Ticket;

use App\Exports\TicketExport;
use App\Models\Ticket;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class TicketTable extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;

    protected $listeners = ['refreshTickets' => 'mount'];

    public $selectedTicket;
    public $search;
    public $ticket;
    public $showEditTicketModal = false;
    public $showTicketDetailModal = false;
    public $confirmDeleteTicketModal = false;
    public $ticketToDelete;
    public $showFilters = true;
    public $filterStatuses = ['open', 'pending'];
    public $clearFilterButton = false;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $ticketsData;
    public $confirmSelectedDeleteTicketModal = false;
    public $filterRequesterNames = [];

    public function mount()
    {
        $this->ticketsData = Ticket::get();
    }

    public function showEditTicket($id)
    {
        $this->ticket = Ticket::with('roles')->findOrFail($id);
        $this->showEditTicketModal = true;
    }

    #[On('hideEditTicket')]
    public function hideEditTicket()
    {
        $this->showEditTicketModal = false;
    }

    public function showTicket($id)
    {
        $this->ticket = Ticket::findOrFail($id);
        $this->showTicketDetailModal = true;
    }

    #[On('hideTicketDetail')]
    public function hideTicketDetail()
    {
        $this->showTicketDetailModal = false;
    }

    public function showDeleteTicket($id)
    {
        $this->ticketToDelete = $id;
        $this->confirmDeleteTicketModal = true;
    }

    public function deleteTicket()
    {
        if ($this->ticketToDelete) {
            Ticket::destroy($this->ticketToDelete);
            $this->alert('success', __('messages.ticketDeleted'));
        } else {
            $this->alert('error', __('messages.ticketNotFound'));
        }

        $this->confirmDeleteTicketModal = false;
        $this->ticketToDelete = null;
        $this->dispatch('refreshTickets');
    }

    public function showSelectedDeleteTicket()
    {
        $this->confirmSelectedDeleteTicketModal = true;
    }

    #[On('showTicketFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->filterStatuses = [];
        $this->filterRequesterNames = [];
        $this->search = '';
        $this->clearFilterButton = false;
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->ticketsData->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function deleteSelected()
    {
        Ticket::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->confirmSelectedDeleteTicketModal=false;
        $this->alert('success', __('messages.ticketDeleted'));
    }

    #[On('exportTickets')]
    public function exportTickets()
    {
        return (new TicketExport($this->search, $this->filterStatuses, $this->filterRequesterNames))->download('tickets-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $query = Ticket::with('user', 'ticketType', 'agent');

        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $subQuery->where('subject', 'like', '%' . $this->search . '%')
                ->orWhere('status', 'like', '%' . $this->search . '%')
                ->orWhereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('agent', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('ticketType', function ($typeQuery) {
                    $typeQuery->where('type_name', 'like', '%' . $this->search . '%');
                });
            });
        }

        if (!user_can('Show Tickets')) {
            $query->where(function ($q) {
                $q->where('user_id', user()->id)->orWhere('agent_id', user()->id);
            });
        }


        $query->with('ticketType');

        $this->clearFilterButton = false; // Reset the filter button state

        if (!empty($this->filterStatuses)) {
            $query = $query->whereIn('status', $this->filterStatuses);
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterRequesterNames)) {
            $query->whereHas('user', function ($userQuery) {
                $userQuery->whereIn('id', $this->filterRequesterNames);
            });
            $this->clearFilterButton = true;
        }

        $tickets = $query->paginate(10);

        return view('livewire.ticket.ticket-table', [
            'tickets' => $tickets
        ]);

    }
}
