<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use App\Models\FamilyMember;
use App\Models\OwnerDocument;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Role;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Country;

class AddUser extends Component
{
    use LivewireAlert, WithFileUploads;

    public $roles;
    public $name;
    public $email;
    public $phone;
    public $role;
    public $profilePhoto;
    public $owner = false;
    public $countries = [];
    public $countryCode;
    public $countryName;
    public $selectedCountry;


    public function mount()
    {
        $this->roles = Role::whereIn('name', ['Admin_' . society()->id, 'Manager_' . society()->id, 'Guard_' . society()->id])->get();
        $this->countries = Country::all();
        $this->countryCode = optional(Country::find(society()->country_id))->phonecode;

    }

    public function updateCountryName()
        {
            $country = Country::where('phonecode', $this->countryCode)->first();
            $this->countryName = $country ? $country->countries_name : '';
            $this->selectedCountry = $country;
        }



    public function submitForm()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email:rfc,strict|unique:users,email',
            'role' => 'required|exists:roles,id',
            'phone' => 'nullable|string|max:20',
        ]);
        $user = new User();
        $passwordString = Str::random(8);
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone_number = $this->phone;
        $user->country_phonecode = $this->countryCode;
        $user->password = bcrypt($passwordString);
        $user->role_id = $this->role;
        $user->status = 'active';

        if ($this->profilePhoto) {
            $filename = Files::uploadLocalOrS3($this->profilePhoto, User::FILE_PATH . '/', width: 150, height: 150);
            $user->profile_photo_path = $filename;
        }
        $role = Role::find($this->role);
        $user->save();
        $user->syncRoles([$role->name]);

        try {
            $user->notify(new UserCreatedNotification($user, $passwordString));

            $this->resetForm();
            $this->profilePhoto = null;
            $user = "";

            $this->dispatch('hideAddUser');

            $this->alert('success', __('messages.userAdded'));
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end'
            ]);
        }
    }

    public function removeProfilePhoto()
    {
        $this->profilePhoto = null;
        $this->dispatch('photo-removed');
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->role = '';
        $this->phone = '';
        $this->profilePhoto = null;
        $this->removeProfilePhoto();
    }


    public function render()
    {
        return view('livewire.forms.add-user');
    }
}
