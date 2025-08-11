<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use App\Models\ApartmentManagement;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Role;
use App\Models\User;
use App\Models\Country;

class EditTenant extends Component
{
    use LivewireAlert, WithFileUploads;

    public $roles;
    public $role;
    public $name;
    public $email;
    public $phone;
    public $status;
    public $profilePhoto;
    public $tenant;
    public $user;
    public $userId;
    public $countries = [];
    public $countryCode;
    public $selectedCountry;
    public $countryName;

    public function mount()
    {
        if ($this->tenant && $this->tenant->user) {
            $this->userId = $this->tenant->user->id;
            $this->name = $this->tenant->user->name;
            $this->email = $this->tenant->user->email;
            $this->phone = $this->tenant->user->phone_number;
            $this->role = $this->tenant->user->role_id;
            $this->countryCode = $this->tenant->user->country_phonecode;
            $this->selectedCountry = Country::where('phonecode', $this->tenant->user->country_phonecode)->first();
            $this->countries = Country::all();
        }
        $this->roles = Role::where('name', 'Tenant_' . society()->id)->first();
    }
    public function updateCountryName()
    {
        $country = Country::where('phonecode', $this->countryCode)->first();
        $this->countryName = $country ? $country->countries_name : '';
        $this->selectedCountry = $country;
    }

    public function updateTenant()
    {

        // Validate the input data
        $this->validate([
            'name' => 'required',
            'email' => 'required|email:rfc,strict|unique:users,email,' . $this->userId,
            'phone' => 'nullable|string|max:20',
        ]);

        $this->tenant->user->name = $this->name;
        $this->tenant->user->email = $this->email;
        $this->tenant->user->phone_number = $this->phone;
        $this->tenant->user->role_id = $this->roles->id;
        $this->tenant->user->country_phonecode = $this->countryCode ?: null;

        if ($this->profilePhoto) {
            $filename = Files::uploadLocalOrS3($this->profilePhoto, User::FILE_PATH . '/', width: 150, height: 150);
            $this->tenant->user->profile_photo_path = $filename;
        }

        $this->tenant->user->save();

        $role = Role::find($this->roles->id);
        $this->tenant->user->syncRoles([$role->name]);

        $this->dispatch('hideEditTenant');

        $this->alert('success', __('messages.tenantUpdated'));
    }

    public function removeProfilePhoto()
    {
        $this->profilePhoto = null;
        $this->tenant->user->profile_photo_path = null;
        $this->tenant->user->save();
        $this->dispatch('photo-removed');
    }

    public function render()
    {
        return view('livewire.forms.edit-tenant');
    }
}
