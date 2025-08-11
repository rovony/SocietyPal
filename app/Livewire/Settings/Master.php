<?php

namespace App\Livewire\Settings;

use App\Models\Maintenance;
use Livewire\Attributes\On;

use App\Models\Society;
use Livewire\Component;

class Master extends Component
{
    public $settings;
    public $maintenanceSetting;
    public $activeSetting;

    public function mount()
    {
        $this->settings = Society::find(society()->id);
        $this->maintenanceSetting = Maintenance::first();
        $this->activeSetting = request('tab') ?: 'app';
    }

    #[On('settingsUpdated')]
    public function refreshSettings()
    {
        session()->forget(['society', 'timezone', 'currency', 'billType', 'maintenance']);
        $this->settings->fresh();
    }

    public function render()
    {
        return view('livewire.settings.master');
    }
}
