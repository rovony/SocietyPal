<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Livewire\Component;
use App\Models\Amenities;
use Livewire\Attributes\On;
use App\Models\NotificationSetting;
use App\Notifications\AmenitiesNotification;
use Illuminate\Support\Facades\Notification;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddAmenities extends Component
{
    use LivewireAlert;

    public $amenityName;
    public $status = "not_available";
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
        $this->amenity = Amenities::get();
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

        if(!$this->multipleBookingStatus){
            $this->numberOfPerson = null;
        }
    }

    public function submitForm()
    {
        $rules = [
            'amenityName' => [
                'required',
                'unique:amenities,amenities_name,NULL,id,society_id,' . society()->id
            ],
            'status' => 'required'
        ];

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

        $ameniities = new Amenities();
        $ameniities->amenities_name = $this->amenityName;
        $ameniities->status = $this->status;
        $ameniities->booking_status = $this->bookingStatus ?? 0;
        $ameniities->start_time = $this->startTime;
        $ameniities->end_time = $this->endTime;
        $ameniities->slot_time = $this->slotTime;
        $ameniities->multiple_booking_status = $this->multipleBookingStatus ?? 0;
        $ameniities->number_of_person = $this->numberOfPerson;
        $ameniities->save();

        $this->alert('success', __('messages.amenityAdded'));

        if ($ameniities->status == 'available') {
            try {
                $users = User::whereHas('role', function ($q) {
                    $q->whereIn('display_name', ['Owner', 'Admin', 'Tenant']);
                })->get();

                foreach ($users as $user) {
                    $user->notify(new AmenitiesNotification($ameniities));
                }
            } catch (\Exception $e) {
                $this->alert('error', __('messages.notificationFailed') . ': ' . $e->getMessage(), [
                    'toast' => true,
                    'position' => 'top-end'
                ]);
            }
        }
        $this->dispatch('hideAddAmenities');
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['amenityName','status','bookingStatus','startTime','endTime','slotTime','multipleBookingStatus','numberOfPerson']);
    }

    public function render()
    {
        $this->dispatch('refreshAmenities');
        return view('livewire.forms.add-amenities');
    }
}
