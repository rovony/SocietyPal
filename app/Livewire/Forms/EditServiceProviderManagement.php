<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use App\Models\ApartmentManagement;
use App\Models\Floor;
use App\Models\ServiceManagement;
use App\Models\ServiceType;
use App\Models\Tower;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Models\Country;

class EditServiceProviderManagement extends Component
{
    use LivewireAlert, WithFileUploads;

    public $serviceSettings;
    public $companyName;
    public $serviceSettingsId;
    public $contactPersonName;
    public $status;
    public $phoneNumber;
    public $websiteLink;
    public $price;
    public $description;
    public $serviceManagement;
    public $paymentFrequency;
    public $daily_help;
    public $towers = [];
    public $floors = [];
    public $apartments = [];
    public $selectedTower;
    public $selectedFloor;
    public $selectedApartment;
    public $selectedApartmentsArray = [];
    public $photo;
    public $hasNewPhoto = false;
    public $countries = [];
    public $countryCode;
    public $countryName;
    public $selectedCountry;


    public function mount()
    {
        $this->serviceSettings = ServiceType::all();
        $this->towers = Tower::all();

        $this->serviceSettingsId = $this->serviceManagement->service_type_id;
        $this->companyName = $this->serviceManagement->company_name;
        $this->contactPersonName = $this->serviceManagement->contact_person_name;
        $this->status = $this->serviceManagement->status;
        $this->phoneNumber = $this->serviceManagement->phone_number;
        $this->websiteLink = $this->serviceManagement->website_link;
        $this->price = $this->serviceManagement->price;
        $this->description = $this->serviceManagement->description;
        $this->paymentFrequency = $this->serviceManagement->payment_frequency;
        $this->daily_help = (bool) $this->serviceManagement->daily_help;
        $this->photo = $this->serviceManagement->service_photo;

        $this->selectedApartmentsArray = $this->serviceManagement->apartmentManagements->pluck('id')->toArray();
        $this->countryCode = $this->serviceManagement->country_phonecode;
        $this->selectedCountry = Country::where('phonecode', $this->serviceManagement->country_phonecode)->first();
        $this->countries = Country::all();

    }
    public function updateCountryName()
    {
        $country = Country::where('phonecode', $this->countryCode)->first();
        $this->countryName = $country ? $country->countries_name : '';
        $this->selectedCountry = $country;
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

        $this->serviceManagement->service_type_id = $this->serviceSettingsId;
        $this->serviceManagement->company_name = $this->companyName;
        $this->serviceManagement->contact_person_name = $this->contactPersonName;
        $this->serviceManagement->status = $this->status;
        $this->serviceManagement->phone_number = $this->phoneNumber;
        $this->serviceManagement->country_phonecode = $this->countryCode ?: null;
        $this->serviceManagement->website_link = $this->websiteLink;
        $this->serviceManagement->price = is_numeric($this->price) ? $this->price : 0;
        $this->serviceManagement->description = $this->description;
        $this->serviceManagement->payment_frequency = $this->paymentFrequency;
        $this->serviceManagement->daily_help = $this->daily_help;

        if ($this->photo instanceof TemporaryUploadedFile) {
            $filename = Files::uploadLocalOrS3($this->photo, ServiceManagement::FILE_PATH . '/', width: 150, height: 150);
            $this->photo = $filename;
            $this->serviceManagement->service_photo = $this->photo;
            $this->hasNewPhoto = false;
        }

        $this->serviceManagement->save();

        $this->serviceManagement->apartmentManagements()->sync($this->selectedApartmentsArray);

        $this->alert('success', __('messages.serviceUpdated'));

        $this->dispatch('hideEditServiceManagement');
    }

    public function removeProfilePhoto()
    {
        $this->photo = null;
        $this->serviceManagement->service_photo = null;
        $this->serviceManagement->save();
        $this->dispatch('photo-removed');
    }

    public function render()
    {
        return view('livewire.forms.edit-service-provider-management');
    }
}
