<?php

namespace App\Livewire\Forms;

use App\Models\Country;
use App\Models\Currency;
use Spatie\Permission\Models\Role;
use App\Models\Society;
use App\Models\User;
use App\Notifications\WelcomeSocietyEmail;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Spatie\Permission\Models\Permission;

class AddSociety extends Component
{
    use LivewireAlert;

    public $SocietyName;
    public $sub_domain;
    public $email;
    public $contactName;
    public $password;
    public $phoneNumber;
    public $currencies;
    public $currency_id;
    public $countries;
    public $country;
    public $address;
    public $facebook;
    public $instagram;
    public $twitter;
    public $status = 1;
    public $licenseType = 'free';
    public $domain;

    public function mount()
    {
        $this->countries = Country::all();
        $this->domain = '.' . getDomain();

        $ipCountry = (new User)->getCountryFromIp();

        $defaultCountry = Country::where('countries_code', $ipCountry)->first();
        $this->country = $defaultCountry->id;
    }

    public function submitForm()
    {
        if (!$this->validateSubdomain()) {
            return;
        }

        $timezone = (new User)->getTimezoneFromIp();

        $this->validate([
            'SocietyName' => 'required',
            'contactName' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'address' => 'required',
        ]);

        $society = new Society();

        if (module_enabled('Subdomain')) {
            $society->sub_domain = strtolower($this->sub_domain . $this->domain);
        }
        $society->name = $this->SocietyName;
        $society->hash = md5(microtime() . rand(1, 99999999));
        $society->address = $this->address;
        $society->timezone = $timezone ?? 'UTC';
        $society->theme_hex = global_setting()->theme_hex;
        $society->theme_rgb = global_setting()->theme_rgb;
        $society->email = $this->email;
        $society->country_id = $this->country;
        $society->phone_number = $this->phoneNumber;
        $society->license_type = $this->licenseType;
        $society->is_active = (bool) $this->status;
        $society->is_active = true;
        $society->facebook_link = $this->facebook;
        $society->instagram_link = $this->instagram;
        $society->twitter_link = $this->twitter;
        $society->save();

        $adminRole = Role::where('name', 'Admin_' . $society->id)->first();

        $user = new User();
        $user->name = $this->contactName;
        $user->email = $this->email;
        $user->password = bcrypt(123456);
        $user->society_id = $society->id;


        $adminRole = Role::create(['name' => 'Admin_' . $society->id, 'display_name' => 'Admin', 'guard_name' => 'web', 'society_id' => $society->id]);
        $managerRole = Role::create(['name' => 'Manager_' . $society->id, 'display_name' => 'Manager', 'guard_name' => 'web', 'society_id' => $society->id]);
        $ownerRole = Role::create(['name' => 'Owner_' . $society->id, 'display_name' => 'Owner', 'guard_name' => 'web', 'society_id' => $society->id]);
        $tenantRole = Role::create(['name' => 'Tenant_' . $society->id, 'display_name' => 'Tenant', 'guard_name' => 'web', 'society_id' => $society->id]);
        $guardRole = Role::create(['name' => 'Guard_' . $society->id, 'display_name' => 'Guard', 'guard_name' => 'web', 'society_id' => $society->id]);

        $allPermissions = Permission::get()->pluck('name')->toArray();
        $adminRole->syncPermissions($allPermissions);

        $managerPermissions = Permission::get()->pluck('name')->toArray();
        $managerRole->syncPermissions($managerPermissions);

        $ownerPermissions = Permission::whereIn('name', ['Create Book Amenity', 'Create Tickets'])->get();
        $ownerRole->syncPermissions($ownerPermissions);

        $tenantPermissions = Permission::whereIn('name', ['Create Book Amenity', 'Create Tickets'])->get();
        $tenantRole->syncPermissions($tenantPermissions);

        $guardPermissions = Permission::whereIn('name', ['Create Visitors', 'Create Tickets', 'Show Parking', 'Show Apartment', 'Update Visitors'])->get();
        $guardRole->syncPermissions($guardPermissions);

        $user->role_id = $adminRole->id;
        $user->save();
        $user->assignRole('Admin_' . $society->id);

        try {
            $user->notify(new WelcomeSocietyEmail($society));
            $this->js('window.location.reload()');
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.forms.add-society');
    }

    /**
     * Validate the subdomain input
     *
     * @return bool Returns true if validation passes, false otherwise
     */
    private function validateSubdomain()
    {
        // Skip validation if Subdomain module is not enabled
        if (!module_enabled('Subdomain')) {
            return true;
        }


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


        // Check if subdomain is already in use
        $fullSubdomain = strtolower($this->sub_domain . $this->domain);
        if (Society::where('sub_domain', $fullSubdomain)->exists()) {
            $this->addError('sub_domain', __('subdomain::app.messages.subdomainAlreadyExists'));
            return false;
        }

        return true;
    }
}
