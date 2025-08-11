<?php

namespace App\Livewire\Forms;

use App\Models\BookAmenity;
use Livewire\Component;

class ShowBookAmenity extends Component
{
    public $amenity;
    public $amenityId;
    public $bookings;

    public function mount()
    {
        $this->amenityId = $this->amenity->id;
        $this->amenity = BookAmenity::with('user', 'amenity')->findOrFail($this->amenityId);

        $this->bookings = BookAmenity::where('unique_id', $this->amenity->unique_id)
                                      ->orderBy('booking_time')->get();
    }


    public function render()
    {
        return view('livewire.forms.show-book-amenity');
    }
}
