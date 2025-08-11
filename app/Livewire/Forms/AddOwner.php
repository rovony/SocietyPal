<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use App\Models\ApartmentManagement;
use App\Models\Floor;
use App\Models\Tower;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Role;
use Livewire\WithFileUploads;
use App\Models\ApartmentOwner;
use App\Models\Country;
use Illuminate\Support\Str;

class AddOwner extends Component
{
    use LivewireAlert, WithFileUploads;

    public $roles;
    public $name;
    public $email;
    public $phone;
    public $role;
    public $profilePhoto;
    public $isOwner = true;

    public $towers = [];
    public $floors = [];
    public $apartments = [];
    public $selectedTower;
    public $selectedFloor;
    public $selectedApartment;
    public $selectedApartmentsArray = [];
    public $countries = [];
    public $countryCode;
    public $countryName;
    public $selectedCountry;
    public $defaultCountry;



    public function mount()
    {
        $this->roles = Role::where('name', 'Owner_' . society()->id)->first();
        $this->towers = Tower::all();
        $this->countries = Country::all();
        $this->countryCode = optional(Country::find(society()->country_id))->phonecode;

    }

    public function updatedSelectedTower()
    {
        $this->selectedFloor = '';
        $this->selectedApartment = '';
        $this->floors = Floor::where('tower_id', $this->selectedTower)->get();
        $this->apartments = collect([]);
    }

    public function updatedSelectedFloor()
    {
        if ($this->selectedTower && $this->selectedFloor) {
            $this->selectedApartment = '';
            $this->apartments = ApartmentManagement::where('tower_id', $this->selectedTower)
            ->where('floor_id', $this->selectedFloor)
            ->where('user_id', null)
            ->where(function($query) {
                $query->where('status', 'not_sold')
                      ->orWhere('status', 'rented');
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


        public function updateCountryName()
        {
            $country = Country::where('phonecode', $this->countryCode)->first();
            $this->countryName = $country ? $country->countries_name : '';
            $this->selectedCountry = $country;
        }

    public function removeApartment($apartmentId)
    {
        $this->selectedApartmentsArray = array_values(array_filter($this->selectedApartmentsArray, function ($id) use ($apartmentId) {
            return $id != $apartmentId;
        }));
    }

    public function submitForm()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email:rfc,strict|unique:users,email',
            'selectedApartmentsArray' => 'required|array|min:1',
            'phone' => 'nullable|string|max:20',
        ], [
            'selectedApartmentsArray.required' => __('messages.apartmentRequired'),
            'selectedApartmentsArray.min' => __('messages.apartmentRequired'),
        ]);

        $owner = new User();
        $passwordString = Str::random(8);
        $owner->name = $this->name;
        $owner->email = $this->email;
        $owner->phone_number = $this->phone;
        $owner->country_phonecode = $this->countryCode ?: null;
        $owner->password = bcrypt($passwordString);
        $owner->owner = $this->isOwner;
        $owner->role_id = $this->roles->id;
        $owner->status = 'active';
        if ($this->profilePhoto) {
            $filename = Files::uploadLocalOrS3($this->profilePhoto, User::FILE_PATH . '/', width: 150, height: 150);
            $owner->profile_photo_path = $filename;
        }

        $role = Role::find($this->roles->id);
        $owner->save();

            foreach ($this->selectedApartmentsArray as $apartmentId) {
                $apartment = ApartmentManagement::find($apartmentId);
                $apartment->user_id = $owner->id;
                if ($apartment->status !== 'rented') {
                    $apartment->status = 'occupied';
                }

                $apartment->save();
                ApartmentOwner::updateOrInsert([
                    'apartment_id' => $apartmentId,
                    'user_id' => $owner->id,
                ]);
            }

            $owner->syncRoles([$role->name]);

        try {
            $owner->notify(new UserCreatedNotification($owner, $passwordString));
            $this->resetForm();
            return redirect()->route('owners.show', ['owner' => $owner->id]);
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
        $this->phone = '';
        $this->profilePhoto = null;
        $this->reset(['selectedApartmentsArray']);
    }

    public function render()
    {
        return view('livewire.forms.add-owner');
    }
}
