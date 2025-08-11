<?php

namespace App\Livewire\Forms;

use App\Helper\Files;
use App\Models\ApartmentManagement;
use App\Models\ApartmentTenant;
use App\Models\Rent;
use App\Models\Tenant;
use App\Notifications\RentNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Tower;
use App\Models\Floor;

class AddRent extends Component
{
    use LivewireAlert, WithFileUploads;

    public $tenant;
    public $payment_date;
    public $tenant_id;
    public $apartment_id;
    public $rent_amount;
    public $rent_for_month;
    public $profilePhoto;
    public $rent_for_year;
    public $billing_cycle;
    public $status = 'unpaid';
    public $months = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];
    public $towers = [];
    public $floors = [];
    public $apartments = [];
    public $tower_id;
    public $floor_id;

    public function mount()
    {
        $this->towers = Tower::all();
        $this->payment_date = now()->format('Y-m-d');
    }

    public function updatedTowerId()
    {
        $this->floor_id = '';
        $this->apartment_id = '';
        $this->floors = Floor::where('tower_id', $this->tower_id)->get();
        $this->apartments = collect([]);
    }

    public function updatedFloorId()
    {
        if ($this->tower_id && $this->floor_id) {
            $this->apartment_id = '';
            $this->apartments = ApartmentManagement::where('tower_id', $this->tower_id)->where('floor_id', $this->floor_id)->where('status', 'rented')->get();
        }
        else {
            $this->apartments = collect([]);
        }
    }

    public function loadAvailableTimeSlots()
    {
        if ($this->payment_date) {
            $dayOfWeek = Carbon::parse($this->payment_date)->dayOfWeek;
            $currentTime = Carbon::now(timezone())->format('H:i:s');
            $selectedDate = Carbon::parse($this->payment_date)->format('Y-m-d');
        }
    }

    public function updatedApartmentId($value)
    {
        $this->tenant = Tenant::whereHas('apartments', function ($query) use ($value) {
            $query->where('apartment_managements.id', $value);
        })->first();

        if ($this->tenant) {
            $this->tenant_id = $this->tenant->id;
            $this->rent_amount = $this->tenant->rent_amount;
            $this->billing_cycle = $this->tenant->rent_billing_cycle;
            $apartmentTenant = ApartmentTenant::where('apartment_id', $value)->first();
        }
        if ($apartmentTenant) {
            $this->tenant_id = $apartmentTenant->tenant_id;
            $this->tenant = $apartmentTenant->tenant;
            $this->rent_amount = $apartmentTenant->rent_amount;
            $this->billing_cycle = $apartmentTenant->rent_billing_cycle;
            $this->rent_for_year = now()->year;
            $this->rent_for_month = ($this->billing_cycle === 'monthly') ? strtolower(now()->format('F')) : null;
        } else {
            $this->tenant_id = null;
            $this->tenant = null;
            $this->rent_amount = null;
            $this->billing_cycle = null;
            $this->rent_for_month = null;
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

        $filename = $this->profilePhoto ? Files::uploadLocalOrS3($this->profilePhoto, Rent::FILE_PATH . '/', width: 150, height: 150) : null;

        $rent = new Rent();
        $rent->apartment_id = $this->apartment_id;
        $rent->tenant_id = $this->tenant_id;
        $rent->rent_for_year = $this->rent_for_year;
        $rent->rent_for_month = $this->rent_for_month;
        $rent->rent_amount = $this->rent_amount;
        $rent->status = $this->status;
        $rent->payment_date = $formattedPaymentDate;
        $rent->payment_proof = $filename;
        $rent->save();

        try {
            $tenantUser = $rent->tenant->user;
            $tenantUser->notify(new RentNotification($rent));
            $this->dispatch('hideAddRent');
            $this->alert('success', __('messages.rentAdded'));
            $this->reset(['apartment_id', 'rent_for_year', 'rent_for_month', 'rent_amount', 'status', 'payment_date']);
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

    public function render()
    {
        return view('livewire.forms.add-rent');
    }
}
