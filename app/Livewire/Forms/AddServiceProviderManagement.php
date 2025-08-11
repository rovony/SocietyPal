<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use App\Models\ApartmentManagement;
use App\Models\Floor;
use App\Models\ServiceManagement;
use App\Models\ServiceManagementApartment;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\ServiceType;
use App\Models\Tower;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\ServiceProviderManagementNotification;
use Livewire\WithFileUploads;
use App\Models\Country;

class AddServiceProviderManagement extends Component
{
    use LivewireAlert, WithFileUploads;

    public $serviceSettings;
    public $companyName;
    public $serviceSettingsId;
    public $contactPersonName;
    public $status;
    public $photo;
    public $phoneNumber;
    public $websiteLink;
    public $price;
    public $description;
    public $paymentFrequency;
    public $daily_help = 0;
    public $apartments = [];
    public $selectedApartment;
    public $isOpen = false;
    public $towers = [];
    public $floors = [];
    public $selectedTower;
    public $selectedFloor;
    public $selectedApartmentsArray = [];
    public $countries = [];
    public $countryCode;
    public $countryName;
    public $selectedCountry;


    protected $listeners = ['fetchDefault' => 'mount'];

    public function mount()
    {
        $this->serviceSettings = ServiceType::all();
        $this->towers = Tower::all();
        $this->countries = Country::all();
        $this->countryCode = optional(Country::find(society()->country_id))->phonecode;

    }

    public function updateCountryName()
        {
            $country = Country::where('phonecode', $this->countryCode)->first();
            $this->countryName = $country ? $country->countries_name : '';
            $this->selectedCountry = $country;
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
                ->get();
        } else {
            $this->apartments = collect([]);
        }
    }

    public function removeApartment($apartmentId)
    {
        $this->selectedApartmentsArray = array_values(array_filter($this->selectedApartmentsArray, function ($id) use ($apartmentId) {
            return $id != $apartmentId;
        }));
    }

    public function submitForm()
    {
        $rules = [
            'serviceSettingsId' => 'required',
            'contactPersonName' => 'required',
            'phoneNumber' => 'required|string|max:20',
        ];

        $messages = [
            'serviceSettingsId.required' => __('messages.serviceValidationMessage'),
        ];

        $this->validate($rules, $messages);


        $serviceManagement = new ServiceManagement();
        $serviceManagement->service_type_id = $this->serviceSettingsId;
        $serviceManagement->company_name = $this->companyName;
        $serviceManagement->contact_person_name = $this->contactPersonName;
        $serviceManagement->status = $this->status ?? "available";
        $serviceManagement->payment_frequency = $this->paymentFrequency ?? "per_visit";
        $serviceManagement->phone_number = $this->phoneNumber;
        $serviceManagement->country_phonecode = $this->countryCode ?? null;
        $serviceManagement->website_link = $this->websiteLink ?? null;
        $serviceManagement->price =  is_numeric($this->price) ? $this->price : 0;
        $serviceManagement->description = $this->description ?? null;
        $serviceManagement->daily_help = $this->daily_help;

        if ($this->photo) {
            $filename = Files::uploadLocalOrS3($this->photo, ServiceManagement::FILE_PATH . '/', width: 150, height: 150);
            $this->photo = $filename;
        }

        $serviceManagement->service_photo = $this->photo;
        $serviceManagement->save();

        if (!empty($this->selectedApartmentsArray)) {
            foreach ($this->selectedApartmentsArray as $apartmentId) {
                ServiceManagementApartment::create([
                    'service_management_id' => $serviceManagement->id,
                    'apartment_management_id' => $apartmentId,
                ]);
            }
        }

        try {
            $users = User::whereHas('role', function ($q) {
                $q->whereIn('display_name', ['Owner', 'Tenant']);
            })->get();


            foreach ($users as $user) {
                $user->notify(new ServiceProviderManagementNotification($serviceManagement));
            }
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end',
            ]);
        }
        $this->alert('success', __('messages.serviceAdded'));

        $this->dispatch('hideAddServiceManagement');
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->resetValidation();
        $this->dispatch('fetchDefault');
        $this->reset(['serviceSettingsId', 'companyName', 'contactPersonName', 'status', 'phoneNumber', 'websiteLink', 'price', 'description', 'daily_help', 'photo']);
    }

    public function removeProfilePhoto()
    {
        $this->photo = null;
        $this->dispatch('photo-removed');
    }

    public function render()
    {
        return view('livewire.forms.add-service-provider-management');
    }
}
