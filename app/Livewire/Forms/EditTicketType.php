<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditTicketType extends Component
{
    use LivewireAlert;

    public $ticketType;
    public $ticketTypeName;
    public $ticketId;

    public function mount()
    {
        $this->ticketTypeName = $this->ticketType->type_name;
        $this->ticketId = $this->ticketType->id;
    }

    public function submitForm()
    {
        $this->validate([
            'ticketTypeName' => [
                'required',
                Rule::unique('ticket_type_settings', 'type_name')
                    ->where('society_id', society()->id)
                    ->ignore($this->ticketId)
            ]

        ]);

        $this->ticketType->type_name = $this->ticketTypeName;
        $this->ticketType->save();

        $this->alert('success', __('messages.ticketUpdated'));

        $this->dispatch('hideEditTicketType');
    }

    public function render()
    {
        return view('livewire.forms.edit-ticket-type');
    }
}
