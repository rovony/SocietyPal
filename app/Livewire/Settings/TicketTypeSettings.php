<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\TicketTypeSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class TicketTypeSettings extends Component
{
    use LivewireAlert;

    protected $listeners = ['refreshTicketTypes' => 'mount'];

    public $ticketTypes;
    public $ticketType;
    public $showEditTicketTypeModal = false;
    public $showAddTicketTypeModal = false;
    public $confirmDeleteTicketTypeModal = false;

    public function mount()
    {
        $this->ticketTypes = TicketTypeSetting::get();
    }

    public function showAddTicketType()
    {
        $this->showAddTicketTypeModal = true;
    }

    public function showEditTicketType($id)
    {
        $this->ticketType = TicketTypeSetting::findOrFail($id);
        $this->showEditTicketTypeModal = true;
    }

    public function showDeleteTicketType($id)
    {
        $this->ticketType = TicketTypeSetting::findOrFail($id);
        $this->confirmDeleteTicketTypeModal = true;
    }

    public function deleteTicketType($id)
    {
        TicketTypeSetting::destroy($id);

        $this->confirmDeleteTicketTypeModal = false;

        $this->ticketType= '';
        $this->dispatch('refreshTicketTypes');

        $this->alert('success', __('messages.ticketTypeDeleted'));
    }

    #[On('hideEditTicketType')]
    public function hideEditTicketType()
    {
        $this->showEditTicketTypeModal = false;
        $this->dispatch('refreshTicketTypes');
    }

    #[On('hideAddTicketType')]
    public function hideAddTicketType()
    {
        $this->showAddTicketTypeModal = false;
        $this->dispatch('refreshTicketTypes');
    }

    public function render()
    {
        return view('livewire.settings.ticket-type-settings');
    }
}
