<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\BookAmenity;
use Livewire\Attributes\On;

class TodayBookingAmenitiesCount extends Component
{
    public $showAmenity;
    public $showBookAmenityDetailModal = false;
    public $todayBooking;

    public function showBookAmenityDetail($id)
    {
        $this->showAmenity = BookAmenity::findOrFail($id);
        $this->showBookAmenityDetailModal = true;
    }

    #[On('hideBookAmenityDetail')]
    public function hideBookAmenityDetail()
    {
        $this->showBookAmenityDetailModal = false;
    }

    public function render()
    {
        if(user_can('Show Book Amenity')){
            $this->todayBooking = BookAmenity::with('amenity', 'user')->whereDate('booking_date', now()->format('Y-m-d'))->whereHas('amenity')->get();
        }
        else{
            $this->todayBooking = BookAmenity::with('amenity', 'user')->whereDate('booking_date', now()->format('Y-m-d'))->where('booked_by', user()->id)->whereHas('amenity')->get();
        }
        return view('livewire.dashboard.today-booking-amenities-count');
    }
}
