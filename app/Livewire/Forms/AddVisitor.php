<?php

namespace App\Livewire\Forms;

use DateTime;
use DateTimeZone;
use App\Helper\Files;
use App\Models\Floor;
use App\Models\Tower;
use App\Models\Tenant;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
use App\Models\VisitorManagement;
use App\Models\ApartmentManagement;
use App\Models\VisitorTypeSettingsModel;
use Illuminate\Support\Facades\Notification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\VisitorManagementNotification;
use App\Models\Country;

class AddVisitor extends Component
{

    use LivewireAlert, WithFileUploads;

    public $apartments;
    public $visitorName;
    public $mobileNumber;
    public $visitorAddress;
    public $apartmentId;
    public $dateOfVisit;
    public $dateOfExit;
    public $inTime;
    public $outTime;
    public $photo;
    public $visitor;
    public $floor;
    public $floors = [];
    public $apartment = [];
    public $towers;
    public $visitorTypes;
    public $visitorTypeId;
    public $purposeOfVisit;
    public $selectedFloor = null;
    public $selectedTower = null;
    public $selectedApartment = null;
    public $countries = [];
    public $countryCode;
    public $countryName;
    public $selectedCountry;
    public $selectedCountryCode;

    public function mount()
    {
        $this->apartments = ApartmentManagement::with('user')->whereNotIn('status', ['not_sold'])->get();
        $this->dateOfVisit = Carbon::now()->timezone(timezone())->format('Y-m-d');
        $this->inTime = Carbon::now()->timezone(timezone())->format('H:i');
        $this->visitorTypes = VisitorTypeSettingsModel::get();
        $this->floor = Floor::all();
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


    public function updatedSelectedTower($towerId)
    {
        $this->floors = Floor::where('tower_id', $towerId)->get();
        $this->selectedFloor = null;
    }

    public function updatedSelectedFloor($floorId)
    {
        $this->apartment = ApartmentManagement::with('user')
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
            'outTime' =>  __('messages.outTimeValidationMessage'),
        ];

        $this->validate($rules, $messages);

        $visitor = new VisitorManagement();
        $visitor->apartment_id = $this->apartmentId;
        $visitor->visitor_type_id = $this->visitorTypeId;
        $visitor->purpose_of_visit = $this->purposeOfVisit;
        $visitor->visitor_name = $this->visitorName;
        $visitor->phone_number = $this->mobileNumber;
        $visitor->country_phonecode = $this->countryCode ?: null;
        $visitor->address = $this->visitorAddress;
        $visitor->in_time =  Carbon::createFromFormat('H:i', $this->inTime, timezone())->setTimezone('UTC')->format('H:i');
        $visitor->out_time = $this->outTime ? Carbon::createFromFormat('H:i', $this->outTime, timezone())->setTimezone('UTC')->format('H:i') : null;
        $visitor->date_of_visit = Carbon::parse($this->dateOfVisit)->timezone(timezone())->setTimezone('UTC');
        $visitor->date_of_exit = $this->dateOfExit ? Carbon::parse($this->dateOfExit)->timezone(timezone())->setTimezone('UTC') : null;
        $visitor->added_by = user()->id;

        if ($this->photo) {
            $filename = Files::uploadLocalOrS3($this->photo, VisitorManagement::FILE_PATH . '/', width: 150, height: 150);
            $this->photo = $filename;
        }

        $visitor->visitor_photo = $this->photo;
        $visitor->save();
        $visitor = VisitorManagement::with('apartment.user', 'apartment.tenants.user')->find($visitor->id);
        $apartment = $visitor->apartment;
        $owner = $apartment->user ?? null;
        $tenants = $apartment->tenants ?? [];

        try {
            if ($apartment->status === 'rented' && $tenants->isNotEmpty()) {
                foreach ($tenants as $tenant) {
                    $tenant->user->notify(new VisitorManagementNotification($visitor));
                }
            } elseif (in_array($apartment->status, ['available_for_rent', 'occupied']) && $owner) {
                $owner->notify(new VisitorManagementNotification($visitor));
            }
        }  catch (\Exception $e) {
                $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end',
            ]);
        }


        $this->alert('success', __('messages.visitorAdded'));

        $this->dispatch('hideAddVisitor');
    }

    #[On('resetForm')]

    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['apartmentId', 'visitorName', 'mobileNumber', 'visitorAddress', 'inTime', 'outTime', 'photo', 'visitorTypeId', 'purposeOfVisit', 'selectedApartment', 'selectedTower', 'selectedFloor', 'dateOfExit']);
    }

    public function removeProfilePhoto()
    {
        $this->photo = null;
        $this->dispatch('photo-removed');
    }

    public function render()
    {
        return view('livewire.forms.add-visitor');
    }
}
