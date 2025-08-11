<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use App\Models\User;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Role;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Models\Country;

class EditUser extends Component
{
    use LivewireAlert, WithFileUploads;

    public $roles;
    public $name;
    public $email;
    public $phone;
    public $role;
    public $status;
    public $profilePhoto;
    public $hasNewPhoto = false;
    public $userId;
    public $user;
    public $firstUserId;
    public $countries = [];
    public $countryCode;
    public $countryName;
    public $selectedCountry;
    public $selectedCountryCode;


    public function mount()
    {
        $this->roles = Role::whereIn('name', ['Admin_' . society()->id, 'Manager_' . society()->id, 'Guard_' . society()->id])->get();
        $this->userId = $this->user->id;
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone_number;
        $this->status = $this->user->status;
        $this->role = $this->user->role_id;
        $firstUser = User::whereHas('roles', function ($query) {
            $query->where('display_name', 'Admin');
        })->first();

        $this->firstUserId = $firstUser ? $firstUser->id : null;
        $this->countryCode = $this->user->country_phonecode;
        $this->selectedCountry = Country::where('phonecode', $this->user->country_phonecode)->first();
        $this->countries = Country::all();
    }
    public function updateCountryName()
    {
        $country = Country::where('phonecode', $this->countryCode)->first();
        $this->countryName = $country ? $country->countries_name : '';
        $this->selectedCountry = $country;
    }

    public function updateUser()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email:rfc,strict|unique:users,email,' . $this->userId,
            'role' => 'required',
            'status' => 'required',
            'phone' => 'nullable|string|max:20',
        ]);

        $this->user->name = $this->name;
        $this->user->email = $this->email;
        $this->user->phone_number = $this->phone;
        $this->user->status = $this->status;
        $this->user->role_id = $this->role;
        $this->user->country_phonecode = $this->countryCode ?: null;

        if ($this->profilePhoto instanceof TemporaryUploadedFile) {
            $filename = Files::uploadLocalOrS3($this->profilePhoto, User::FILE_PATH . '/', width: 150, height: 150);
            $this->user->profile_photo_path = $filename;
            $this->hasNewPhoto = false;
        }

        $this->user->save();
        $role = Role::find($this->role);
        $this->user->syncRoles([$role->name]);

        $this->dispatch('hideEditUser');

        $this->alert('success', __('messages.userUpdated'));
    }

    public function removeProfilePhoto()
    {
        $this->profilePhoto = null;
        $this->user->profile_photo_path = null;
        $this->user->save();
        $this->dispatch('photo-removed');
    }

    public function render()
    {
        return view('livewire.forms.edit-user');
    }
}
