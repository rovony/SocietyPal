<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use App\Models\ApartmentManagement;
use App\Models\ApartmentTenant;
use App\Models\Currency;
use App\Models\Rent;
use App\Models\Tenant;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Tower;
use App\Models\Floor;

class EditRent extends Component
{
    use LivewireAlert, WithFileUploads;

    public $rent;
    public $tenant;
    public $apartments;
    public $payment_date;
    public $currency_id;
    public $tenant_id;
    public $apartment_id;
    public $status;
    public $rent_amount;
    public $rentId;
    public $rent_for_month;
    public $profilePhoto;
    public $payment_proof;
    public $billing_cycle;
    public $rent_for_year;
    public $months = ['january', 'february', 'march', 'april', 'may', 'june','july', 'august', 'september', 'october', 'november', 'december' ];
    public $towers = [];
    public $floors = [];
    public $tower_id;
    public $floor_id;

    public function mount()
    {
        $this->rentId = $this->rent->id;
        $this->towers = Tower::all();

        $apartment = ApartmentManagement::find($this->rent->apartment_id);
        $this->tower_id = $apartment->tower_id ?? null;
        $this->floor_id = $apartment->floor_id ?? null;

        $this->floors = Floor::where('tower_id', $this->tower_id)->get();
        $this->apartments = ApartmentManagement::where('tower_id', $this->tower_id)
            ->where('floor_id', $this->floor_id)
            ->where('status', 'rented')
            ->get();
        $this->tenant = Tenant::with('user')->find($this->rent->tenant_id);

        $rent = Rent::findOrFail($this->rentId);
        $this->apartment_id = $rent->apartment_id;
        $this->tenant_id = $rent->tenant_id;
        $this->currency_id = $rent->currency_id;
        $this->rent_amount = $rent->rent_amount;
        $this->status = $rent->status;
        $this->rent_for_month = $rent->rent_for_month;
        $this->payment_date = $rent->payment_date ? Carbon::parse($rent->payment_date)->format('Y-m-d') : null;

        $apartmentTenant = ApartmentTenant::where('tenant_id', $this->tenant_id)->where('apartment_id', $this->apartment_id)->first();
        $this->billing_cycle = $apartmentTenant ? $apartmentTenant->rent_billing_cycle : null;

        if ($this->billing_cycle == 'annually') {
            $this->rent_for_year = $rent->rent_for_year;
            $this->rent_for_month = null;
        } elseif ($this->billing_cycle === 'monthly') {
            $this->rent_for_month = $rent->rent_for_month;
            $this->rent_for_year = $rent->rent_for_year;
        }
    }
    public function updatedTowerId()
    {
        $this->floor_id = '';
        $this->apartment_id = '';
        $this->floors = Floor::where('tower_id', $this->tower_id)->get();
        $this->apartments = [];
    }

    public function updatedFloorId()
    {
        if ($this->tower_id && $this->floor_id) {
            $this->apartment_id = '';
            $this->apartments = ApartmentManagement::where([
                ['tower_id', '=', $this->tower_id],
                ['floor_id', '=', $this->floor_id],
                ['status', '=', 'rented'],
            ])->get();
        } else {
            $this->apartments = [];
        }
    }

    public function loadAvailableTimeSlots()
    {
        if ($this->payment_date) {
            $dayOfWeek = Carbon::parse($this->payment_date)->dayOfWeek;
            $currentTime = Carbon::now(config('app.timezone'))->format('H:i:s');
            $selectedDate = Carbon::parse($this->payment_date)->format('Y-m-d');
        }
    }

    public function updatedApartmentId($value)
    {
        $apartmentTenant = ApartmentTenant::where('apartment_id', $value)->first();

        if ($apartmentTenant) {
            $this->tenant_id = $apartmentTenant->tenant_id;
            $this->tenant = $apartmentTenant->tenant;
            $this->rent_amount = $apartmentTenant->rent_amount;
            $this->billing_cycle = $apartmentTenant->rent_billing_cycle;
        } else {
            $this->tenant_id = null;
            $this->tenant = null; 
            $this->rent_amount = null;
            $this->billing_cycle = null;
        }
    }
    public function submitForm()
    {
        $rentForMonthRule = ($this->billing_cycle === 'monthly') ? 'required' : 'nullable';

        $validatedData = $this->validate([
            'apartment_id' => 'required',
            'tenant_id' => 'required|exists:tenants,id',
            'rent_for_year' => 'required',
            'rent_for_month' => $rentForMonthRule,
            'rent_amount' => 'required|numeric|min:0',
            'status' => 'required|in:paid,unpaid',
        ], [
            'apartment_id.required' => __('messages.apartmentNumberRequired'),
        ]);

        $formattedPaymentDate = ($this->status === 'paid' && $this->payment_date) ? Carbon::parse($this->payment_date)->format('Y-m-d') : null;

        $filename = $this->profilePhoto
        ? Files::uploadLocalOrS3($this->profilePhoto, Rent::FILE_PATH .'/')
        : Rent::find($this->rentId)->payment_proof;

        // Find and update the rent record
        $rent = Rent::findOrFail($this->rentId);
        $rent->update([
            'apartment_id' => $this->apartment_id,
            'tenant_id' => $this->tenant_id,
            'rent_for_month' => $this->rent_for_month,
            'rent_for_year' => $this->rent_for_year,
            'rent_amount' => $this->rent_amount,
            'status' => $this->status,
            'payment_date' => $formattedPaymentDate,
            'payment_proof' => $filename,
        ]);

        $this->dispatch('hideEditRent');

        $this->alert('success', __('messages.rentUpdated'));

    }

    public function removeProfilePhoto()
    {
        $this->profilePhoto = null;
        $this->rent->payment_proof = null;
        $this->rent->save();
        $this->dispatch('photo-removed');
    }

    public function render()
    {
        return view('livewire.forms.edit-rent');
    }
}
