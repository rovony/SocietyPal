<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Component;
use App\Models\Apartment;
use Livewire\Attributes\On;
use App\Models\ParkingManagementSetting;
use App\Models\ApartmentManagement;
use App\Models\ApartmentParking;
use App\Models\Tenant;
use App\Notifications\ParkingNotification;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddParking extends Component
{
    use LivewireAlert;

    public $parkingCode;
    public $apartmentName;
    public $apartments;
    public $status;

    public function mount()
    {
        $this->apartments = ApartmentManagement::all();
    }

    public function submitForm()
    {
        $this->validate([
            'parkingCode' => 'required|unique:parking_managements,parking_code,',
        ]);
        $apartmentId = !empty($this->apartmentName) ? $this->apartmentName : null;
        $parking = new ParkingManagementSetting();
        $parking->parking_code = $this->parkingCode;
        $parking->status = $apartmentId ? 'not_available' : 'available';
        $parking->save();

        if ($apartmentId) {
                $apartmentParking = new ApartmentParking();
                $apartmentParking->parking_id = $parking->id;
                $apartmentParking->apartment_management_id = $apartmentId;
                $apartmentParking->save();
            }

        $this->resetForm();

        $this->alert('success', __('messages.parkingAdded'));
        $users = ApartmentManagement::where('id', $apartmentId)
            ->with('user')
            ->get();

            $tenants = Tenant::whereHas('apartments', function ($query) use ($apartmentId) {
                $query->where('apartment_tenant.apartment_id', $apartmentId);
            })->with('user')->get();

        $allUsers = $users->pluck('user')->merge($tenants->pluck('user'));

        foreach ($allUsers as $user) {
            if ($user) {
                $user->notify(new ParkingNotification($parking));
            }

        }

        $this->dispatch('parkingAdded');
        $this->dispatch('hideAddParking');
        $this->dispatch('refreshParkingCodes');

        $this->alert('success', __('messages.parkingAdded'));
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['parkingCode','apartmentName']);
    }

    public function render()
    {
        return view('livewire.forms.add-parking');
    }
}
