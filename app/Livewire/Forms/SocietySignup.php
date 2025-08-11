<?php

namespace App\Livewire\Forms;

use App\Models\Role;
use App\Models\User;
use App\Models\Country;
use App\Models\Society;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Notifications\NewSocietySignup;
use App\Providers\RouteServiceProvider;
use Spatie\Permission\Models\Permission;
use App\Notifications\WelcomeSocietyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class SocietySignup extends Component
{
    public $societyName;
    public $sub_domain;
    public $fullName;
    public $email;
    public $password;
    public $address;
    public $country;
    public $countries;
    public $showUserForm = true;
    public $showSocietyForm = false;
    public $captcha = null;
    public $isCaptchaVerified = null;

    public function mount()
    {
        if (user()) {
            return redirect('dashboard');
        }

        $this->countries = Country::all();

        $ipCountry = (new User)->getCountryFromIp();

        $defaultCountry = Country::where('countries_code', $ipCountry)->first();

        $this->country = $defaultCountry->id;
    }

    public function submitForm()
    {

        if (module_enabled('Subdomain')) {

            $this->validate([
                'sub_domain' => module_enabled('Subdomain') ? 'regex:/^[a-z0-9-_]{2,20}$/|required|banned_sub_domain|min:3|max:50' : '',
            ]);

            $society = Society::where('sub_domain', strtolower($this->sub_domain . '.' . getDomain()))->exists();

            if ($society) {
                $this->addError('sub_domain', __('subdomain::app.messages.subdomainAlreadyExists'));
                return;
            }
        }

        $this->validate([
            'societyName' => 'required',
            'fullName' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
        ]);
        $this->showUserForm = false;
        $this->showSocietyForm = true;
    }


    public function submitForm2()
    {

        if (User::where('email', $this->email)->exists()) {
            $this->addError('email', 'This email is already registered.');
            return;
        }

        $timezone = (new User)->getTimezoneFromIp();

        $this->validate([
            'address' => 'required',
            'isCaptchaVerified' => 'required',
        ],[
            'isCaptchaVerified.required' => __('messages.captchaRequired'),
        ]);

        $requiresApproval = global_setting()->requires_approval_after_signup;
        $society = new Society();
        $society->name = $this->societyName;

        if (module_enabled('Subdomain')) {
            $society->sub_domain = strtolower(trim($this->sub_domain, '.') . '.' . getDomain());
        }

        $society->hash = md5(microtime() . rand(1, 99999999));
        $society->address = $this->address;
        $society->timezone = $timezone ?? 'UTC';
        $society->theme_hex = global_setting()->theme_hex;
        $society->theme_rgb = global_setting()->theme_rgb;
        $society->email = $this->email;
        $society->approval_status = $requiresApproval ? 'Pending' : 'Approved';
        $society->country_id = $this->country;
        $society->about_us = Society::ABOUT_US_DEFAULT_TEXT;
        $society->save();

        $user = new User();
        $user->name = $this->fullName;
        $user->email = $this->email;
        $user->password = bcrypt($this->password);
        $user->society_id = $society->id;

        $adminRole = Role::create(['name' => 'Admin_'.$society->id, 'display_name' => 'Admin', 'guard_name' => 'web', 'society_id' => $society->id]);
        $managerRole = Role::create(['name' => 'Manager_'.$society->id, 'display_name' => 'Manager', 'guard_name' => 'web', 'society_id' => $society->id]);
        $ownerRole = Role::create(['name' => 'Owner_'.$society->id, 'display_name' => 'Owner', 'guard_name' => 'web', 'society_id' => $society->id]);
        $tenantRole = Role::create(['name' => 'Tenant_'.$society->id, 'display_name' => 'Tenant', 'guard_name' => 'web', 'society_id' => $society->id]);
        $guardRole = Role::create(['name' => 'Guard_'.$society->id, 'display_name' => 'Guard', 'guard_name' => 'web', 'society_id' => $society->id]);

        $allPermissions = Permission::get()->pluck('name')->toArray();
        $adminRole->syncPermissions($allPermissions);

        $managerPermissions = Permission::get()->pluck('name')->toArray();
        $managerRole->syncPermissions($managerPermissions);

        $ownerPermissions = Permission::whereIn('name', ['Create Book Amenity' ,'Create Tickets'])->get();
        $ownerRole->syncPermissions($ownerPermissions);

        $tenantPermissions = Permission::whereIn('name', ['Create Book Amenity' ,'Create Tickets'])->get();
        $tenantRole->syncPermissions($tenantPermissions);

        $guardPermissions = Permission::whereIn('name', ['Create Visitors', 'Create Tickets', 'Show Parking','Show Apartment','Update Visitors'])->get();
        $guardRole->syncPermissions($guardPermissions);

        $user->role_id = $adminRole->id;
        $user->save();

        $user->assignRole('Admin_'.$society->id);

        $user->notify(new WelcomeSocietyEmail($society));

        $superadmins = User::withoutGlobalScopes()->role('Super Admin')->get();
        Notification::send($superadmins, new NewSocietySignup($society));

        if (module_enabled('Subdomain')) {
            $hash = encrypt($user->id);
            cache(['quick_login_' . $user->id => $hash], now()->addMinutes(2));
            return redirect('https://' . $society->sub_domain . '/quick-login/' . $hash);
        }

        $this->authLogin($user);

        return redirect(RouteServiceProvider::ONBOARDING_STEPS);
    }

    public function updatedCaptcha($token)
    {
        $response = Http::post(
            'https://www.google.com/recaptcha/api/siteverify?secret='.
            global_setting()->google_recaptcha_v3_secret_key.
            '&response='.$token
        );

        $success = $response->json()['success'];

        if (! $success) {
            throw ValidationException::withMessages([
                'isCaptchaVerified' => __('Google thinks, you are a bot, please refresh and try again!'),
            ]);
        } else {
            $this->isCaptchaVerified = true;
        }
    }

    public function render()
    {
        return view('livewire.forms.society-signup');
    }


    public function authLogin($user)
    {
        Auth::loginUsingId($user->id);

        $society = $user->society;

        session(['user' => auth()->user()]);
        session(['society' => $society->fresh()]);
    }
}
