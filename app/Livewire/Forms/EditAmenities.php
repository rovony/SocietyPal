<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditAmenities extends Component
{
    use LivewireAlert;

    public $amenitiesManagment;
    public $amenitiesManagmentId;
    public $amenityName;
    public $status;
    public $bookingStatus;
    public $startTime;
    public $endTime;
    public $slotTime;
    public $multipleBookingStatus;
    public $numberOfPerson;
    public $ameniities;
    public $amenity;

    public function mount()
    {
        $this->amenityName = $this->amenitiesManagment->amenities_name;
        $this->status = $this->amenitiesManagment->status;
        $this->bookingStatus = (bool) $this->amenitiesManagment->booking_status;
        $this->startTime = $this->amenitiesManagment->start_time;
        $this->endTime = $this->amenitiesManagment->end_time;
        $this->slotTime = $this->amenitiesManagment->slot_time;
        $this->multipleBookingStatus = (bool) $this->amenitiesManagment->multiple_booking_status;
        $this->numberOfPerson = $this->amenitiesManagment->number_of_person;
        $this->amenitiesManagmentId = $this->amenitiesManagment->id;
    }

    public function UpdatedStatus()
    {
        if($this->status == "not_available"){
            $this->bookingStatus = false;
        }
        if(!$this->bookingStatus){
            $this->startTime = null;
            $this->endTime = null;
            $this->slotTime = null;
            $this->multipleBookingStatus = false;
            $this->numberOfPerson = null;
        }
    }

    public function submitForm()
    {
        $rules =['amenityName' => [
                'required',
                Rule::unique('amenities', 'amenities_name')
                    ->where('society_id', society()->id)
                    ->ignore($this->amenitiesManagmentId)],
                    'status' => 'required',];
        if ($this->bookingStatus == true) {
            $rules ['slotTime'] ='required';
            $rules ['startTime'] ='required';
            $rules ['endTime'] ='required|after:startTime';
        }

        if ($this->multipleBookingStatus == true) {
            $rules ['numberOfPerson'] ='required';
        }

        $messages['endTime']['after'] = __('messages.endTimeValidationMessage');

        $this->validate($rules,$messages);

        if(!$this->bookingStatus){
            $this->startTime = null;
            $this->endTime = null;
            $this->slotTime = null;
            $this->multipleBookingStatus = false;
            $this->numberOfPerson = null;
        }

        if(!$this->multipleBookingStatus){
            $this->numberOfPerson = null;
        }

        $this->amenitiesManagment->amenities_name = $this->amenityName;
        $this->amenitiesManagment->status = $this->status;
        $this->amenitiesManagment->booking_status = $this->bookingStatus;
        $this->amenitiesManagment->start_time = $this->startTime;
        $this->amenitiesManagment->end_time = $this->endTime;
        $this->amenitiesManagment->slot_time = $this->slotTime ? $this->slotTime : 0;
        $this->amenitiesManagment->multiple_booking_status = $this->multipleBookingStatus;
        $this->amenitiesManagment->number_of_person = $this->numberOfPerson != "" ? $this->numberOfPerson : 0;
        $this->amenitiesManagment->save();

        $this->alert('success', __('messages.amenityUpdated'));

        $this->dispatch('hideEditAmenities');
    }

    public function render()
    {
        return view('livewire.forms.edit-amenities');
    }
}
