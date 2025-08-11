<?php

namespace App\Livewire\Settings;

use DateTimeZone;
use App\Models\Country;
use Livewire\Component;
use App\Models\Currency;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\GlobalSetting;

class TimezoneSettings extends Component
{
    use LivewireAlert;

    public $settings;
    public $societyCountry;
    public $societyTimezone;
    public $societyCurrency;
    public $countries;
    public $timezones;
    public $currencies;
    public bool $showLogoText;
    public bool $pwaAlertShow;

    public function mount()
    {
        $this->societyCountry = $this->settings->country_id;
        $this->societyTimezone = $this->settings->timezone;
        $this->societyCurrency = $this->settings->currency_id;
        $this->showLogoText = $this->settings->show_logo_text;
        $this->countries = Country::all();
        $this->currencies = Currency::all();
        $this->timezones = DateTimeZone::listIdentifiers();
        $this->pwaAlertShow = $this->settings->is_pwa_install_alert_show;
    }

    public function submitForm()
    {
        $this->validate([
            'societyCountry' => 'required',
            'societyCurrency' => 'required',
            'societyTimezone' => 'required',
        ]);

        $this->settings->timezone = $this->societyTimezone;
        $this->settings->country_id = (int) $this->societyCountry;
        $this->settings->currency_id = $this->societyCurrency;
        $this->settings->show_logo_text = $this->showLogoText;
        $this->settings->is_pwa_install_alert_show = $this->pwaAlertShow;
        $this->settings->save();

        session()->forget('society');

        $this->dispatch('settingsUpdated');

        $this->alert('success', __('messages.settingsUpdated'));

        session()->forget(['society', 'timezone', 'currency', 'user']);
        $this->redirect(route('settings.index'), navigate: true);
    }
    public function render()
    {
        return view('livewire.settings.timezone-settings');
    }
}
