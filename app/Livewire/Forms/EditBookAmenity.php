<?php

namespace App\Livewire\Forms;

use App\Models\Amenities;
use App\Models\BookAmenity;
use App\Notifications\AmenityBookingUpdated;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EditBookAmenity extends Component
{
    use LivewireAlert;

    public $selectedAmenity;
    public $bookedBy;
    public $bookingDate;
    public $bookingTime = [];
    public $persons;
    public $storedPersons;
    public $amenities;
    public $availableSlots = [];
    public $amenityId;
    public $amenity;
    public $showPersonsField = true;
    public $uniqueId;
    public $bookingType = 'single';

    public function mount()
    {
        $this->amenityId = $this->amenity->id;
        $this->loadBookingDetails();
        $this->amenities = Amenities::where('status', 'available')->where('booking_status', 1)->get();
        $this->storedPersons = $this->calculatePersonsForBooking($this->bookingTime);
        $this->bookedBy = user()->id;
    }

    private function calculatePersonsForBooking($bookingTimes)
    {
        $amenity = Amenities::find($this->selectedAmenity);
        $formattedBookingDate = Carbon::parse($this->bookingDate)->format('Y-m-d');

        if (!$amenity || !$bookingTimes) {
            return 0;
        }

        $remainingSlotsForEachTime = [];
        foreach ($bookingTimes as $time) {
            $formattedSlotTime = Carbon::createFromFormat('H:i', $time)->format('H:i:s');
            $alreadyBooked = $this->getAlreadyBookedPersons($this->selectedAmenity, $formattedBookingDate, $formattedSlotTime, $this->amenityId);
            $remainingSlots = $amenity->number_of_person - $alreadyBooked;
            $remainingSlotsForEachTime[$time] = $remainingSlots;
        }

        // Return the smallest remaining slots (to cover multiple booking times)
        return min($remainingSlotsForEachTime) ?: 0;
    }

    // Retrieves the booking information from the database and sets the component's properties.
    public function loadBookingDetails()
    {
        $booking = BookAmenity::find($this->amenityId);

        if ($booking) {
            $this->selectedAmenity = $booking->amenity_id;
            $this->bookingDate = $booking->booking_date;
            $this->uniqueId = $booking->unique_id;
            $this->bookingType = $booking->booking_type;
            $this->storedPersons = $booking->persons;
            $relatedBookings = BookAmenity::where('unique_id', $this->uniqueId)->get();
            $this->bookingTime = $relatedBookings->pluck('booking_time')
                ->map(fn($time) => Carbon::parse($time)->format('H:i'))
                ->toArray();
            $this->updatedBookingDate();
        }
    }

    // Calculates the total number of persons already booked for the given amenity, booking date, and booking time.
    private function getAlreadyBookedPersons($amenityId, $bookingDate, $bookingTime, $excludeBookingId = null, $excludeUniqueId = null)
    {
        $query = BookAmenity::where('amenity_id', $amenityId)
            ->where('booking_date', $bookingDate)
            ->where('booking_time', $bookingTime);

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        if (!is_null($excludeUniqueId)) {
            $query->where('unique_id', '!=', $excludeUniqueId);
        } else {
            $query->whereNull('unique_id');
        }
        return $query->sum('persons');
    }

    public function updatedSelectedAmenity()
    {
        $this->resetValidation();
        $this->reset(['bookingTime', 'storedPersons', 'availableSlots']);
        if ($this->bookingDate) {
            $this->updatedBookingDate();
        }
    }

    public function updatedBookingDate()
    {
        $amenity = Amenities::find($this->selectedAmenity);
        if ($amenity && $this->bookingDate && $amenity->booking_status) {
            $this->availableSlots = $this->generateTimeSlots(
                $amenity->start_time,
                $amenity->end_time,
                $amenity->slot_time
            );
            $this->showPersonsField = $amenity->multiple_booking_status != 0;
            $formattedBookingDate = Carbon::parse($this->bookingDate)->format('Y-m-d');
            $isToday = Carbon::now()->format('Y-m-d') == $formattedBookingDate;

            foreach ($this->availableSlots as $index => $slot) {
                $slotTime = $slot['time'];
                $formattedSlotTime = Carbon::createFromFormat('H:i', $slotTime)->format('H:i:s');
                $slot['formatted_time'] = Carbon::createFromFormat('H:i', $slotTime)->format('h:i A');

                $slotDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $formattedBookingDate . ' ' . $formattedSlotTime, timezone());

                if ($isToday && $slotDateTime->lessThan(Carbon::now())) {
                    $this->availableSlots[$index]['is_disabled'] = true;
                    continue;
                }

                if ($amenity->multiple_booking_status != 0) {
                    $alreadyBooked = $this->getAlreadyBookedPersons($this->selectedAmenity, $formattedBookingDate, $formattedSlotTime, $this->amenityId);
                    $remainingSlots = $amenity->number_of_person - $alreadyBooked;

                    $this->availableSlots[$index]['remaining'] = $remainingSlots;
                    $this->availableSlots[$index]['is_disabled'] = $remainingSlots <= 0;
                } else {
                    $this->availableSlots[$index]['remaining'] = null;
                    $this->availableSlots[$index]['is_disabled'] = false;
                }
            }
        } else {
            $this->availableSlots = [];
        }
    }

    public function updatedBookingTime($time)
    {
        $amenity = Amenities::find($this->selectedAmenity);
        $formattedBookingDate = Carbon::parse($this->bookingDate)->format('Y-m-d');
        $slot = collect($this->availableSlots)->firstWhere('time', $time);

        if ($slot) {
            if ($amenity->multiple_booking_status == null) {
                $this->persons = null;
            } else {
                $formattedSlotTime = Carbon::createFromFormat('H:i', $slot['time'])->format('H:i:s');

                $alreadyBooked = $this->getAlreadyBookedPersons($this->selectedAmenity, $formattedBookingDate, $formattedSlotTime, $this->amenityId);
                $remainingSlots = $amenity->number_of_person - $alreadyBooked;

                $this->availableSlots = collect($this->availableSlots)->map(function ($slotItem) use ($remainingSlots, $formattedSlotTime, $slot) {
                    if ($slotItem['time'] == $slot['time']) {
                        $slotItem['remaining'] = $remainingSlots;
                        $slotItem['is_disabled'] = $remainingSlots <= 0;
                    }
                    return $slotItem;
                })->toArray();

                if ($remainingSlots > 0) {
                    $this->persons = $remainingSlots;
                } else {
                    $this->persons = 0;
                }
            }
        }
    }

    public function toggleBookingTime($time)
    {
        if (!is_array($this->bookingTime)) {
            $this->bookingTime = [];
        }

        if (in_array($time, $this->bookingTime)) {
            $this->bookingTime = array_diff($this->bookingTime, [$time]);
        } else {
            $this->bookingTime[] = $time;
        }
    }

    // Generates time slots between the given start and end time, with each slot duration determined by the slot duration parameter.
    private function generateTimeSlots($startTime, $endTime, $slotDuration)
    {
        $slots = [];
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        while ($start->lessThan($end)) {
            $slotTime = $start->format('H:i');

            $slots[] = [
                'time' => $slotTime,
                'is_disabled' => false,
                'remaining' => null
            ];

            $start->addMinutes($slotDuration);
        }

        return $slots;
    }

    // Submits the booking details and validates the booking.
    public function submitBooking()
    {
        $this->validate([
            'selectedAmenity' => 'required|exists:amenities,id',
            'bookingDate' => 'required|date',
            'bookingTime' => 'required',
        ]);

        $this->bookingDate = Carbon::parse($this->bookingDate)->format('Y-m-d');
        $amenity = Amenities::find($this->selectedAmenity);

        $remainingSlotsForEachTime = [];
        if ($amenity->multiple_booking_status == 0) {
            foreach ($this->bookingTime as $time) {
                $remainingSlotsForEachTime[$time] = 0;
            }
        } else {
            foreach ($this->bookingTime as $time) {
                $alreadyBooked = $this->getAlreadyBookedPersons($this->selectedAmenity, $this->bookingDate, $time, $this->amenityId);
                $remainingSlots = $amenity->number_of_person - $alreadyBooked;

                if ($this->persons > $remainingSlots) {
                    $validator = Validator::make([], []);
                    $validator->errors()->add('persons', __('messages.personLimitExceeded'));
                    throw new \Illuminate\Validation\ValidationException($validator);
                }
                $remainingSlotsForEachTime[$time] = $remainingSlots;
            }
        }

        $isMultipleBooking = is_array($this->bookingTime) && count($this->bookingTime) > 1;
        $uniqueId =$this->uniqueId;

        BookAmenity::where('unique_id', $uniqueId)->delete();
        $this->js('window.location.reload()');
        $unique = Str::random(16);
        foreach ($this->bookingTime as $time) {
            $booking = new BookAmenity();
            $booking->amenity_id = $this->selectedAmenity;
            $booking->booked_by = $this->bookedBy;
            $booking->booking_date = $this->bookingDate;
            $booking->booking_time = $time;
            $booking->unique_id =  $unique ;
            $booking->booking_type = $isMultipleBooking ? 'multiple' : 'single';
            $booking->persons = $isMultipleBooking ? $remainingSlotsForEachTime[$time] :$this->persons;
            $booking->save();
        }

        try{
            $user = user();
            $bookings = BookAmenity::where('unique_id', $unique)->get();
            $user->notify(new AmenityBookingUpdated($bookings));
            $this->alert('success', __('messages.bookingUpdated'));

            $this->dispatch('hideEditBookAmenity');
        }
        catch (\Exception $e) {
            $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.forms.edit-book-amenity');
    }
}
