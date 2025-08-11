<?php

namespace App\Livewire\Forms;

use App\Livewire\Settings\TicketAgentSettings;
use App\Models\User;
use Livewire\Component;
use App\Models\TicketTypeSetting;
use App\Models\TicketAgentSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditTicketAgent extends Component
{
    use LivewireAlert;

    public $selectedAgents;
    public $selectedAgent;
    public $ticketTypes;
    public $ticketEditAgents;
    public $users = [];
    public $isOpen = false;
    public $isOpenTicket = false;
    public $selectedTicketTypes = [];
    public $ticketAgentId;

    public function mount()
    {
        $this->selectedAgents = $this->ticketEditAgents->ticket_agent_id;

        $this->ticketTypes = TicketTypeSetting::all();
        $this->users = User::get();
        $this->selectedTicketTypes = TicketAgentSetting::where('ticket_agent_id', $this->ticketEditAgents->ticket_agent_id)
            ->pluck('ticket_type_id')
            ->toArray();
    }

    public function submitForm()
    {
        $this->validate([
            'selectedAgents' => 'required',
            'selectedTicketTypes' => 'required|array|min:1',
        ]);

        $existingTicketTypes = TicketAgentSetting::where('ticket_agent_id', $this->selectedAgents)
            ->pluck('ticket_type_id')
            ->toArray();

        $ticketTypesToAdd = array_diff($this->selectedTicketTypes, $existingTicketTypes);
        $ticketTypesToRemove = array_diff($existingTicketTypes, $this->selectedTicketTypes);

        foreach ($ticketTypesToAdd as $ticketTypeId) {
            TicketAgentSetting::create([
                'ticket_agent_id' => $this->selectedAgents,
                'ticket_type_id' => $ticketTypeId,
            ]);
        }

        TicketAgentSetting::where('ticket_agent_id', $this->selectedAgents)
            ->whereIn('ticket_type_id', $ticketTypesToRemove)
            ->delete();

        $this->dispatch('hideEditTicketAgent');
        $this->dispatch('refreshAgents');
        $this->alert('success', __('messages.ticketAgentUpdated'));
    }


    public function toggleSelectType($ticketType)
    {
        if (!is_array($this->selectedTicketTypes)) {
            $this->selectedTicketTypes = [];
        }

        if (in_array($ticketType['id'], $this->selectedTicketTypes)) {
            $this->selectedTicketTypes = array_filter(
                $this->selectedTicketTypes,
                fn($id) => $id !== $ticketType['id']
            );
        } else {
            $this->selectedTicketTypes[] = $ticketType['id'];
        }

        $this->selectedTicketTypes = array_values($this->selectedTicketTypes);
    }

    public function render()
    {
        return view('livewire.forms.edit-ticket-agent');
    }
}
