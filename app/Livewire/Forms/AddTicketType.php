<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use App\Models\TicketTypeSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddTicketType extends Component
{
    use LivewireAlert;

    public $ticketTypeName;

    public function submitForm()
    {
        $this->validate([
            'ticketTypeName' => 'required|unique:ticket_type_settings,type_name,NULL,id,society_id,' . society()->id,
        ]);

        $ticketType = new TicketTypeSetting();
        $ticketType->type_name = $this->ticketTypeName;
        $ticketType->save();

        $this->alert('success', __('messages.ticketAdded'));

        $this->dispatch('hideAddTicketType');
    }

    public function render()
    {
        return view('livewire.forms.add-ticket-type');
    }
}
