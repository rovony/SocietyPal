<?php

namespace App\Livewire\Forms;

use App\Models\Country;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Society;

class EditSociety extends Component
{
    use LivewireAlert;

    public $societyName;
    public $email;
    public $phone;
    public $address;
    public $country;
    public $facebook;
    public $instagram;
    public $twitter;
    public $countries;
    public $society;
    public $status;
    public $sub_domain;
    public $domain;

    public function mount()
    {
        if (module_enabled('Subdomain')) {
            $this->sub_domain = str_replace('.' . getDomain(), '', $this->society->sub_domain);
            $this->domain = str($this->society->sub_domain)->endsWith(getDomain()) ? '.' . getDomain() : '';
        }
        $this->countries = Country::all();
        $this->societyName = $this->society->name;
        $this->email = $this->society->email;
        $this->phone = $this->society->phone_number;
        $this->address = $this->society->address;
        $this->country = $this->society->country_id;
        $this->facebook = $this->society->facebook_link;
        $this->instagram = $this->society->instagram_link;
        $this->twitter = $this->society->twitter_link;
        $this->status = $this->society->is_active;
    }

    public function submitForm()
    {
        $this->validate([
            'societyName' => 'required',
            'email' => 'required',
            'address' => 'required',
        ]);

        if (module_enabled('Subdomain')) {

            // Validate domain or subdomain based on input
            if (empty($this->domain)) {
                $this->validate([
                    'sub_domain' => 'required|string'
                ]);
                // For custom domains, we don't need to validate the domain field
                // as it's intentionally empty for custom domains
                // Just continue with the subdomain validation below
            } else {
                $this->validate([
                    'sub_domain' => 'required|min:3|max:50|regex:/^[a-z0-9\-_]{2,20}$/|banned_sub_domain',
                ]);
            }

            $society = Society::where('id', '!=', $this->society->id)
                ->where('sub_domain', strtolower($this->sub_domain . $this->domain))
                ->exists();

            if ($society) {
                $this->addError('sub_domain', __('subdomain::app.messages.subdomainAlreadyExists'));
                return;
            }

            $this->society->sub_domain = strtolower($this->sub_domain . $this->domain);
        }

        $this->society->name = $this->societyName;
        $this->society->address = $this->address;
        $this->society->email = $this->email;
        $this->society->phone_number = $this->phone;
        $this->society->country_id = $this->country;
        $this->society->facebook_link = $this->facebook;
        $this->society->instagram_link = $this->instagram;
        $this->society->twitter_link = $this->twitter;
        $this->society->is_active = (bool)$this->status;
        $this->society->save();

        $this->dispatch('hideEditSociety');

        $this->alert('success', __('messages.societyUpdated'));
    }

    public function render()
    {
        return view('livewire.forms.edit-society');
    }
}
