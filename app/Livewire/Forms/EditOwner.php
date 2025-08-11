<?php

namespace App\Livewire\Forms;

use App\Models\Role;
use App\Models\User;
use App\Helper\Files;
use App\Models\Floor;
use App\Models\Tower;
use App\Models\Country;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ApartmentOwner;
use App\Models\ApartmentManagement;
use App\Models\ParkingManagementSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditOwner extends Component
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

    public $towers = [];
    public $floors = [];
    public $apartments = [];
    public $selectedTower;
    public $selectedFloor;
    public $selectedApartment;
    public $selectedApartmentsArray = [];
    public $countryCode;
    public $selectedCountryCode;
    public $countries = [];
    public $selectedCountry;
    public $countryName;

    public function mount()
    {
        $this->roles = Role::where('name', 'Owner_' . society()->id)->first();
        $this->towers = Tower::all();

        $this->userId = $this->user->id;
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone_number;
        $this->status = $this->user->status;
        $this->countryCode = $this->user->country_phonecode;
        $this->selectedCountry = Country::where('phonecode', $this->user->country_phonecode)->first();
        $this->countries = Country::all();

        $this->selectedApartmentsArray = ApartmentManagement::where('user_id', $this->userId)->pluck('id')->toArray();
    }

    public function updatedSelectedTower()
    {
        $this->selectedFloor = '';
        $this->selectedApartment = '';
        $this->floors = Floor::where('tower_id', $this->selectedTower)->get();
        $this->apartments = collect([]);
    }
    public function updateCountryName()
    {
        $country = Country::where('phonecode', $this->countryCode)->first();
        $this->countryName = $country ? $country->countries_name : '';
        $this->selectedCountry = $country;
    }


    public function updatedSelectedFloor()
    {
        if ($this->selectedTower && $this->selectedFloor) {
            $this->selectedApartment = '';
            $this->apartments = ApartmentManagement::where('tower_id', $this->selectedTower)
                ->where('floor_id', $this->selectedFloor)
                ->where(function ($query) {
                    $query->where('user_id', null)
                        ->orWhere('user_id', $this->userId);
                })
                ->get();
        } else {
            $this->apartments = collect([]);
        }
    }

    public function updatedSelectedApartment()
    {
        if ($this->selectedApartment && !in_array($this->selectedApartment, $this->selectedApartmentsArray)) {
            $apartment = ApartmentManagement::find($this->selectedApartment);
            if ($apartment) {
                $this->selectedApartmentsArray[] = $this->selectedApartment;
                $this->selectedApartment = '';
            }
        }
    }

    public function removeApartment($apartmentId)
    {
        $this->selectedApartmentsArray = array_values(array_filter($this->selectedApartmentsArray, function ($id) use ($apartmentId) {
            return $id != $apartmentId;
        }));
        ApartmentOwner::where('apartment_id', $apartmentId)
        ->where('user_id', $this->userId)
        ->delete();
    }

    public function updateOwner()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email:rfc,strict|unique:users,email,' . $this->userId,
            'status' => 'required',
            'selectedApartmentsArray' => 'required|array|min:1',
            'phone' => 'nullable|string|max:20',
        ], [
            'selectedApartmentsArray.required' => __('messages.apartmentRequired'),
            'selectedApartmentsArray.min' => __('messages.apartmentRequired'),
        ]);

        $this->user->name = $this->name;
        $this->user->email = $this->email;
        $this->user->phone_number = $this->phone;
        $this->user->country_phonecode = $this->countryCode ?: null;

        $this->user->status = $this->status;
        $this->user->role_id = $this->roles->id;

        if ($this->profilePhoto instanceof TemporaryUploadedFile) {
            $filename = Files::uploadLocalOrS3($this->profilePhoto, User::FILE_PATH . '/', width: 150, height: 150);
            $this->user->profile_photo_path = $filename;
            $this->hasNewPhoto = false;
        }
        $this->user->save();
        $role = Role::find($this->roles->id);
        $this->user->syncRoles([$role->name]);

        $apartments = ApartmentManagement::where('user_id', $this->userId)->get();

        foreach ($apartments as $apartment) {
            $apartment->user_id = null;
            if ($apartment->status !== 'rented') {
                $apartment->status = 'not_sold';
            }
            $apartment->save();
        }

        foreach ($this->selectedApartmentsArray as $apartmentId) {
            $apartment = ApartmentManagement::find($apartmentId);

            if ($this->status === 'inactive') {
                $apartment->user_id = null;
                if ($apartment->status !== 'rented') {
                    $apartment->status = 'not_sold';
                }
            } else {
                $apartment->user_id = $this->userId;
                if ($apartment->status !== 'rented') {
                    $apartment->status = 'occupied';
                }
            }

            $apartment->save();

            if ($this->status === 'active') {
                ApartmentOwner::updateOrInsert([
                    'apartment_id' => $apartmentId,
                    'user_id' => $this->user->id,
                ]);
            }
        }
        $this->dispatch('hideEditOwner');

        $this->alert('success', __('messages.ownerUpdated'));
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
        return view('livewire.forms.edit-owner');
    }
}
