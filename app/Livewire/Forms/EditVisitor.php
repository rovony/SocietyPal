<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use App\Models\Floor;
use App\Models\Tower;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use App\Models\ApartmentManagement;
use App\Models\VisitorManagement;
use App\Models\VisitorTypeSettingsModel;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Models\Country;

class EditVisitor extends Component
{
    use LivewireAlert, WithFileUploads;

    public $apartments = [];
    public $apartment;
    public $visitorName;
    public $mobileNumber;
    public $visitorAddress;
    public $apartmentId;
    public $visitorTypeId;
    public $dateOfVisit;
    public $dateOfExit;
    public $inTime;
    public $outTime;
    public $photo;
    public $visitor;
    public $hasNewPhoto = false;
    public $purposeOfVisit;
    public $visitorTypes;
    public $floor = [];
    public $floors = [];
    public $selectedFloor = null;
    public $selectedTower = null;
    public $selectedApartment = null;
    public $towers;
    public $countries = [];
    public $countryCode;
    public $countryName;
    public $selectedCountry;


    public function mount()
    {
        $this->apartments = ApartmentManagement::with('user')->whereNotIn('status', ['not_sold'])->get();
        $this->apartmentId = $this->visitor->apartment_id;
        $this->apartment = ApartmentManagement::with('user')->find($this->apartmentId);
        $this->visitorTypeId = $this->visitor->visitor_type_id;
        $this->visitorName = $this->visitor->visitor_name;
        $this->visitorAddress = $this->visitor->address;
        $this->mobileNumber = $this->visitor->phone_number;
        $this->dateOfVisit = Carbon::parse($this->visitor->date_of_visit)->timezone(timezone())->format('d F Y');
        $this->dateOfExit = !is_null($this->visitor->date_of_exit) ? Carbon::parse($this->visitor->date_of_exit)->timezone(timezone())->format('d F Y') : null;
        $this->outTime =  (!is_null($this->visitor->out_time)) ? Carbon::parse($this->visitor->out_time)->timezone(timezone())->format('H:i') : null;
        $this->inTime = (!is_null($this->visitor->in_time)) ? Carbon::parse($this->visitor->in_time)->timezone(timezone())->format('H:i') : null;
        $this->photo = $this->visitor->visitor_photo;
        $this->purposeOfVisit = $this->visitor->purpose_of_visit;
        $this->selectedTower = $this->apartment->tower_id;
        $this->selectedFloor = $this->apartment->floor_id;
        $this->towers = Tower::all();
        $this->floors = Floor::where('tower_id', $this->selectedTower)->get();
        $this->visitorTypes = VisitorTypeSettingsModel::get();
        $this->updatedApartmentId($this->apartmentId);
        $this->countryCode = $this->visitor->country_phonecode;
        $this->selectedCountry = Country::where('phonecode', $this->visitor->country_phonecode)->first();
        $this->countries = Country::all();

    }
    public function updateCountryName()
    {
        $country = Country::where('phonecode', $this->countryCode)->first();
        $this->countryName = $country ? $country->countries_name : '';
        $this->selectedCountry = $country;
    }


    public function updatedSelectedTower($towerId)
    {
        $this->floors = Floor::where('tower_id', $towerId)->get();
        $this->selectedFloor = null;
    }

    public function updatedSelectedFloor($floorId)
    {
        $this->apartments = ApartmentManagement::with('user')
            ->whereNotIn('status', ['not_sold'])
            ->where('floor_id', $floorId)
            ->get();

        $this->selectedApartment = null;
    }

    public function updatedApartmentId($apartmentId)
    {
        $this->selectedApartment = ApartmentManagement::with('user')->find($apartmentId);
        if (!$this->selectedApartment) {
            $this->selectedApartment = null;
        }
    }

    public function submitForm()
    {
        $rules = [
            'apartmentId' => 'required',
            'visitorTypeId' => 'required',
            'visitorName' => 'required',
            'mobileNumber' => 'required|min:0',
            'dateOfVisit' => 'required',
            'inTime' => 'required',
            'visitorAddress' => 'required',
            'dateOfExit' => [
                'nullable',
                'date',
                'after_or_equal:dateOfVisit',
                'required_with:outTime',
            ],
            'outTime' => [
                'nullable',
                'required_with:dateOfExit',
                function ($attribute, $value, $fail) {
                    if ($value && $this->dateOfExit) {
                        $visitDate = Carbon::parse($this->dateOfVisit)->format('Y-m-d');
                        $exitDate = Carbon::parse($this->dateOfExit)->format('Y-m-d');

                        if ($visitDate === $exitDate) {
                            $inDateTime = Carbon::parse($this->dateOfVisit . ' ' . $this->inTime);
                            $outDateTime = Carbon::parse($this->dateOfExit . ' ' . $value);

                            if ($outDateTime->lessThanOrEqualTo($inDateTime)) {
                                $fail(__('messages.outTimeValidationMessage'));
                            }
                        }
                    }
                },
            ],
        ];
        $messages = [
            'apartmentId.required' => __('messages.apartmentValidationMessage'),
            'visitorTypeId.required' => __('messages.visitorValidationMessage'),
            'outTime' . 'after' =>  __('messages.outTimeValidationMessage'),
        ];

        $this->validate($rules, $messages);

        $this->visitor->apartment_id = $this->apartmentId;
        $this->visitor->visitor_type_id = $this->visitorTypeId;
        $this->visitor->visitor_name = $this->visitorName;
        $this->visitor->address = $this->visitorAddress;
        $this->visitor->phone_number = $this->mobileNumber;
        $this->visitor->country_phonecode = $this->countryCode ?: null;
        $this->visitor->date_of_visit = Carbon::parse($this->dateOfVisit)->timezone(timezone())->setTimezone('UTC')->format('Y-m-d');
        $this->visitor->date_of_exit = $this->dateOfExit ? Carbon::parse($this->dateOfExit)->timezone(timezone())->setTimezone('UTC')->format('Y-m-d') : null;
        $this->visitor->in_time = Carbon::createFromFormat('H:i', $this->inTime, timezone())->setTimezone('UTC')->format('H:i');
        $this->visitor->out_time = $this->outTime ? Carbon::createFromFormat('H:i', $this->outTime, timezone())->setTimezone('UTC')->format('H:i') : null;
        $this->visitor->purpose_of_visit = $this->purposeOfVisit;

        if ($this->photo instanceof TemporaryUploadedFile) {
            $filename = Files::uploadLocalOrS3($this->photo, VisitorManagement::FILE_PATH . '/', width: 150, height: 150);
            $this->photo = $filename;
            $this->visitor->visitor_photo = $this->photo;
            $this->hasNewPhoto = false;
        }
        $this->visitor->save();

        $this->alert('success', __('messages.visitorUpdated'));
        $this->dispatch('hideEditVisitor');
    }

    public function removeProfilePhoto()
    {
        $this->photo = null;
        $this->visitor->visitor_photo = null;
        $this->visitor->save();
        $this->dispatch('photo-removed');
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['apartmentId', 'visitorName', 'mobileNumber', 'visitorAddress', 'inTime', 'outTime', 'photo', 'purposeOfVisit', 'visitorTypeId', 'dateOfExit']);
    }

    public function render()
    {
        return view('livewire.forms.edit-visitor');
    }
}
