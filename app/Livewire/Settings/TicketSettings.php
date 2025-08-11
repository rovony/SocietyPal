<?php

namespace App\Livewire\Settings;

use App\Models\TicketTypeSetting;
use Livewire\Component;

class TicketSettings extends Component
{
    public $ticketSetting;
    public $ticketAgent;
    public $tabs =[];
    public $activeTab;
    protected $queryString = ['activeTab'];

    public function mount()
    {
        $this->tabs=['Ticket Type','Ticket Agent'];
        if (!in_array($this->activeTab, $this->tabs)) {
            $this->activeTab = $this->tabs[0];
        }
    }

    public function showTab($type)
    {
        $this->activeTab = $type;
    }

    public function render()
    {

        return view('livewire.settings.ticket-settings');
    }
}
