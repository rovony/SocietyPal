<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use App\Models\ApartmentManagement;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\TenantNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Role;
use Illuminate\Support\Str;
use App\Models\Country;

class AddTenant extends Component
{
    use LivewireAlert, WithFileUploads;

    public $roles;
    public $role;
    public $name;
    public $email;
    public $phone;
    public $date;
    public $contract_start_date = null;
    public $contract_end_date = null;
    public $rent_amount;
    public $rent_billing_cycle = 'monthly';
    public $status = 'current_resident';
    public $move_in_date = null;
    public $move_out_date = null;
    public $profilePhoto;
    public $document;
    public $familyMembers = [];
    public $apartmentRented = [];
    public $selectedApartment;
    public $isTenant = true;
    public $countries = [];
    public $countryCode;
    public $countryName;
    public $selectedCountry;


    public function mount()
    {
        $this->date = now(timezone())->format('d-M-Y');
        $this->roles = Role::where('name', 'Tenant_' . society()->id)->first();
        $this->apartmentRented = ApartmentManagement::whereNotIn('status', ['occupied', 'rented'])->get();
        $this->familyMembers = [];
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
        $this->validate(
            [
                'name' => 'required',
                'email' => 'required|email:rfc,strict|unique:users,email',
                'phone' => 'nullable|string|max:20',
                'rent_amount' => 'nullable|numeric|min:0',
                'selectedApartment' => 'required',
                'contract_start_date' => 'required|date',
                'contract_end_date' => 'required|date|after:contract_start_date',
                'move_in_date' => [
                    'nullable',
                    'date',
                    function ($attribute, $value, $fail) {
                        if ($this->contract_start_date && $value < $this->contract_start_date) {
                            $fail(__('messages.moveInDateAfterError'));
                        }
                        if ($this->contract_end_date && $value > $this->contract_end_date) {
                            $fail(__('messages.moveOutDateBeforeError'));
                        }
                    }
                ],
                'move_out_date' => [
                    'nullable',
                    'date',
                    'after:move_in_date',
                    function ($attribute, $value, $fail) {
                        if ($value && !$this->move_in_date) {
                            $fail(__('messages.moveOutDateRequiresMoveInDate'));
                        }
                        if ($this->contract_start_date && $value < $this->contract_start_date) {
                            $fail(__('messages.moveOutDateAfterError'));
                        }
                        if ($this->contract_end_date && $value > $this->contract_end_date) {
                            $fail(__('messages.moveOutDateBeforeError'));
                        }
                    }
                ],
            ],
        );


        // Format the dates
        $formattedContractStartDate = $this->contract_start_date ? Carbon::parse($this->contract_start_date)->format('Y-m-d') : null;
        $formattedContractEndDate = $this->contract_end_date ? Carbon::parse($this->contract_end_date)->format('Y-m-d') : null;
        $formattedMoveInDate = $this->move_in_date ? Carbon::parse($this->move_in_date)->format('Y-m-d') : null;
        $formattedMoveOutDate = $this->move_out_date ? Carbon::parse($this->move_out_date)->format('Y-m-d') : null;

        // Create user
        $user = new User();
        $passwordString = Str::random(8);
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone_number = $this->phone;
        $user->country_phonecode = $this->countryCode ?: null;
        $user->tenant = $this->isTenant;
        $user->role_id = $this->roles->id;
        $user->status = 'active';
        $user->password = bcrypt($passwordString);

        if ($this->profilePhoto) {
            $filename = Files::uploadLocalOrS3($this->profilePhoto, User::FILE_PATH . '/', width: 150, height: 150);
            $user->profile_photo_path = $filename;
        }
        $user->save();
        $role = Role::find($this->roles->id);
        $user->syncRoles([$role->name]);

        $tenant = Tenant::create([
            'user_id' => $user->id,
        ]);

        $tenant->apartments()->attach($this->selectedApartment, [
            'contract_start_date' => $formattedContractStartDate,
            'contract_end_date' => $formattedContractEndDate,
            'rent_amount' => $this->rent_amount,
            'rent_billing_cycle' => $this->rent_billing_cycle,
            'status' => $this->status ?: null,
            'move_in_date' => $formattedMoveInDate,
            'move_out_date' => $formattedMoveOutDate,
        ]);

        ApartmentManagement::where('id', $this->selectedApartment)->update(['status' => 'rented']);
        $tenantEmail = $this->email;
        try {
            $user->notify(new TenantNotification($tenant, $passwordString));
            $this->resetForm();

            $this->alert('success', __('messages.tenantAdded'));

            return redirect()->route('tenants.show', ['tenant' => $tenant->id]);
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end'
            ]);
        }
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->role = '';
        $this->phone = '';
        $this->contract_start_date = '';
        $this->contract_end_date = '';
        $this->rent_amount = '';
        $this->rent_billing_cycle = '';
        $this->status = '';
        $this->move_in_date = '';
        $this->move_out_date = '';
        $this->profilePhoto = null;
        $this->selectedApartment = '';
    }

    public function removeProfilePhoto()
    {
        $this->profilePhoto = null;
        $this->dispatch('photo-removed');
    }

    public function render()
    {
        return view('livewire.forms.add-tenant');
    }
}
