<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\TicketTypeSetting;
use App\Models\TicketAgentSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;


class AddTicketAgent extends Component
{
    use LivewireAlert;

    public $selectedAgents;
    public $selectedAgent;
    public $ticketTypeName;
    public $ticketTypes;
    public $ticketAgent;
    public $users = [];
    public $isOpen = false;
    public $isOpenTicket = false;
    public $selectedTicketTypes = [];

    protected $listeners = ['refreshAgents' => 'mount'];

    public function mount()
    {
        $this->ticketTypes = TicketTypeSetting::all();
        $this->ticketAgent = TicketAgentSetting::pluck('ticket_agent_id')->toArray();
        $this->users = User::whereNotIn('id', $this->ticketAgent)->get();
    }

    public function submitForm()
    {
        $this->validate([
            'selectedAgent' => 'required',
            'selectedTicketTypes' => 'required|array|min:1',
        ]);

        foreach ($this->selectedTicketTypes as $ticketTypeId) {
            TicketAgentSetting::create([
                'ticket_agent_id' => $this->selectedAgent,
                'ticket_type_id' => $ticketTypeId,
            ]);
        }

        $this->alert('success', __('messages.ticketAgentAdded'));
        $this->dispatch('hideAddTicketAgent');
        $this->dispatch('refreshAgents');
    }

    public function toggleSelectType($ticketTypes)
    {
        if (in_array($ticketTypes['id'], $this->selectedTicketTypes)) {
            $this->selectedTicketTypes = array_filter($this->selectedTicketTypes, fn($id) => $id !== $ticketTypes['id']);
        } else {
            $this->selectedTicketTypes[] = $ticketTypes['id'];
        }
        $this->selectedTicketTypes = array_values($this->selectedTicketTypes);

    }


    #[On('resetForm')]
    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['selectedAgent','selectedTicketTypes','ticketTypeName']);
    }

    public function render()
    {

        return view('livewire.forms.add-ticket-agent');
    }
}
