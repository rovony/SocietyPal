<?php

namespace App\Livewire\Event;

use App\Models\Society;
use Livewire\Component;
use Livewire\Attributes\On;

class Master extends Component
{

    public $maintenanceSetting;
    public $activeSetting = 'table';

    public function mount()
    {
        $this->activeSetting = request('tab') ?: 'table';
    }

   
    public function render()
    {
        return view('livewire.event.master');
    }
}
