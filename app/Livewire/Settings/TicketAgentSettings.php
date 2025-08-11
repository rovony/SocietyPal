<?php

namespace App\Livewire\Settings;

use App\Models\TicketAgentSetting;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\TicketTypeSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class TicketAgentSettings extends Component
{

    use LivewireAlert;

    protected $listeners = ['refreshTicketAgent' => 'mount'];

    public $ticketTypes;
    public $ticketAgents;
    public $ticketAgent;
    public $user;
    public $ticketEditAgents;
    public $ticketTypeIDs;
    public $showAddTicketAgentModal = false;
    public $confirmDeleteTicketAgentModal = false;
    public $showEditTicketAgentModal = false;

    public function mount()
    {
        $this->ticketTypes = TicketTypeSetting::get();
        $this->ticketAgents = $this->refreshAgents();
    }

    public function showAddTicketAgent()
    {
        $this->dispatch('resetForm');
        $this->showAddTicketAgentModal = true;

    }

    public function showEditTicketAgent($id)
    {
        $this->ticketEditAgents = TicketAgentSetting::where('ticket_agent_id' , $id)->first();
        $this->showEditTicketAgentModal = true;

    }

    public function showDeleteTicketAgent($id)
    {
        $this->ticketAgent = TicketAgentSetting::where('ticket_agent_id', $id)->first();
        $this->confirmDeleteTicketAgentModal = true;
    }

    public function deleteTicketAgent($id)
    {
        $ticketSetting = TicketAgentSetting::findOrfail($id);

        TicketAgentSetting::where('ticket_agent_id', $ticketSetting->ticket_agent_id)->delete();

        $this->confirmDeleteTicketAgentModal = false;

        $this->ticketAgent= '';
        $this->dispatch('refreshTicketAgent');
        $this->dispatch('refreshAgents');

        $this->alert('success', __('messages.ticketAgentDeleted'));
    }

    #[On('hideAddTicketAgent')]
    public function hideAddTicketAgent()
    {
        $this->showAddTicketAgentModal = false;
        $this->dispatch('refreshTicketAgent');
    }

    #[On('hideEditTicketAgent')]
    public function hideEditTicketAgent()
    {
        $this->ticketEditAgents = '';
        $this->showEditTicketAgentModal = false;
        $this->dispatch('refreshTicketAgent');
    }

    public function refreshAgents()
    {
        $ticketAgents = User::select( 'users.id', 'users.name', DB::raw('GROUP_CONCAT(ticket_type_settings.type_name SEPARATOR ", ") as ticket_types'))
            ->join('ticket_agent_settings', 'users.id', '=', 'ticket_agent_settings.ticket_agent_id')
            ->join('ticket_type_settings', 'ticket_agent_settings.ticket_type_id', '=', 'ticket_type_settings.id')
            ->groupBy('users.id', 'users.name')
            ->get();
        return $ticketAgents;
    }

    public function render()
    {
        $this->ticketAgents = $this->refreshAgents();
        return view('livewire.settings.ticket-agent-settings');
    }

}
