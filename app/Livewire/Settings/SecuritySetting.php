<?php

namespace App\Livewire\Settings;

use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class SecuritySetting extends Component
{
    use LivewireAlert;
    public $site_key;
    public $secret_key;
    public ?string $captchaToken = null;


    public function mount(): void
    {
        $settings = GlobalSetting::first();
        $this->site_key = $settings->google_recaptcha_v3_site_key;
        $this->secret_key = $settings->google_recaptcha_v3_secret_key;
    }



    public function submit(): void
    {
        $this->validate([
            'site_key' => 'required|string',
            'secret_key' => 'required|string',
            'captchaToken' => 'required|string',
        ]);

        $query = http_build_query([
            'secret' => $this->secret_key,
            'response' => $this->captchaToken,
        ]);
        $response = Http::post('https://www.google.com/recaptcha/api/siteverify?' . $query);
        $captchaLevel = $response->json('score');


        throw_if($captchaLevel <= 0.5, ValidationException::withMessages([
            'captchaToken' => __('Error on captcha verification. Please, refresh the page and try again.')
        ]));

        $settings = GlobalSetting::first();
        $settings->google_recaptcha_v3_site_key = $this->site_key;
        $settings->google_recaptcha_v3_secret_key = $this->secret_key;
        $settings->google_recaptcha_v3_status = 'active';
        $settings->google_recaptcha_status = 'active';

        $settings->save();
        $this->alert('success', 'saved successfully!');

    }




    public function render()
    {
        return view('livewire.settings.security-setting', [
            'site_key' => $this->site_key,
        ]);
    }
}
