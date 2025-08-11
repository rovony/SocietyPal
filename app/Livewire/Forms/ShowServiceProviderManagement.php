<?php

namespace App\Livewire\Forms;

use App\Models\Society;
use Livewire\Component;
use App\Models\Currency;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShowServiceProviderManagement extends Component
{

    public $serviceManagementShow;

    public function render()
    {
        return view('livewire.forms.show-service-provider-management');
    }
}
