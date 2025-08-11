<?php

namespace App\Livewire\Settings;

use App\Models\GlobalSetting;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SocietySettings extends Component
{
    use LivewireAlert;

    public $settings;
    public $societyName;
    public $societyAddress;
    public $societyPhoneNumber;
    public $societyEmailAddress;

    public function mount()
    {
        $this->societyName = $this->settings->name;
        $this->societyAddress = $this->settings->address;
        $this->societyEmailAddress = $this->settings->email;
        $this->societyPhoneNumber = $this->settings->phone_number;
    }

    public function submitForm()
    {
        $this->validate([
            'societyName' => 'required',
            'societyEmailAddress' => 'required|email',
            'societyPhoneNumber' => 'required|string|max:20',
            'societyAddress' => 'required',
        ]);

        $this->settings->email = $this->societyEmailAddress;
        $this->settings->name = $this->societyName;
        $this->settings->phone_number = $this->societyPhoneNumber;
        $this->settings->address = $this->societyAddress;
        $this->settings->save();

        cache()->forget('global_setting');

        $this->dispatch('settingsUpdated');

        $this->alert('success', __('modules.settings.settingsUpdated'));
    }

    public function render()
    {
        return view('livewire.settings.society-settings');
    }
}
