<?php

namespace App\Livewire\Forms;

use App\Livewire\Apartments\ApartmentManagment;
use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Models\ApartmentManagement;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditParking extends Component
{
    use LivewireAlert;

    public $apartmentId;
    public $parkingCode;
    public $apartmentName;
    public $apartment;
    public $parkingId;
    public $parking;
    public $apartments;

    public function mount()
    {
        $this->parkingCode = $this->parking->parking_code;
        $this->apartmentName = $this->parking->parkingCode->apartment_management_id ?? null;
        $this->parkingId = $this->parking->id;
        $this->apartments = ApartmentManagement::all();
    }

    public function submitForm()
    {
        $this->validate([
            'parkingCode' => 'required|unique:parking_managements,parking_code,' . $this->parking->id,
        ]);
        $apartmentId = !empty($this->apartmentName) ? $this->apartmentName : null;

        $this->parking->parking_code = $this->parkingCode;
        $this->parking->status = $apartmentId ? 'not_available' : 'available';
        $this->parking->save();

        $this->parking->apartmentParking()->delete();

        if ($apartmentId) {
            $this->parking->apartmentParking()->create([
                'parking_id' => $this->parking->id,
                'apartment_management_id' => $apartmentId
            ]);
        }
        if (!is_null($apartmentId)) {
            ApartmentManagement::where('id', $apartmentId)
                ->update(['parking_code_id' => $this->parking->id]);
        }

        $this->alert('success', __('messages.parkingUpdated'));
        $this->dispatch('hideEditParking');
    }

    public function render()
    {
        return view('livewire.forms.edit-parking');
    }

}
