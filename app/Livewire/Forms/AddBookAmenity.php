<?php

namespace App\Livewire\Forms;

use App\Models\Amenities;
use App\Models\BookAmenity;
use App\Notifications\AmenityBookingNotification;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AddBookAmenity extends Component
{
    use LivewireAlert;

    public $bookedBy;
    public $bookingDate;
    public $amenities;
    public $selectedAmenity;
    public $bookingTime = [];
    public $availableSlots = [];
    public $showPersonsField = true;
    public $bookingType = 'single';
    public $uniqueId;
    public $persons;
    public $availablePersons;

    public function mount()
    {
        $this->amenities = Amenities::where('status', 'available')->where('booking_status', 1)->get();
        $this->bookedBy = user()->id;
        $this->bookingDate = Carbon::now()->format('d F Y');
    }

    private function getAlreadyBookedPersons($amenityId, $bookingDate, $bookingTime)
    {
        return BookAmenity::where('amenity_id', $amenityId)
            ->where('booking_date', $bookingDate)
            ->where('booking_time', $bookingTime)
            ->sum('persons');
    }

    public function updatedSelectedAmenity()
    {
        $this->resetValidation();
        $this->reset(['bookingTime', 'availableSlots', 'availablePersons']);
        $this->updatedBookingDate();
    }

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
            $isToday = Carbon::now()->format('Y-m-d') === $formattedBookingDate;

            foreach ($this->availableSlots as $index => $slot) {
                $slotTime = $slot['time'];
                $formattedSlotTime = Carbon::createFromFormat('H:i', $slotTime)->format('H:i:s');
                $slot['formatted_time'] = Carbon::createFromFormat('H:i', $slotTime)->format('h:i A');


                if ($isToday) {
                    $currentTime = Carbon::now(timezone())->format('H:i:s');
                    if (Carbon::createFromFormat('H:i:s', $formattedSlotTime)->lessThan(Carbon::createFromFormat('H:i:s', $currentTime))) {
                        $this->availableSlots[$index]['is_disabled'] = true;
                        continue;
                    }
                }
                if ($amenity->multiple_booking_status !== 0 ) {
                    $alreadyBooked = $this->getAlreadyBookedPersons($this->selectedAmenity, $formattedBookingDate, $formattedSlotTime);
                    $remainingSlots = $amenity->number_of_person - $alreadyBooked;

                    $this->availableSlots[$index]['remaining'] = $remainingSlots;
                    $this->availableSlots[$index]['is_disabled'] = $remainingSlots <= 0;
                } else {
                    // If no person limit, mark slot as always available
                    $this->availableSlots[$index]['remaining'] = 'Unlimited';
                    $this->availableSlots[$index]['is_disabled'] = false;
                }
            }
            if (count($this->bookingTime) > 1) {
                $this->showPersonsField = false;
            }
        } else {
            $this->availableSlots = [];
        }
    }

    public function updatedBookingTime($time)
    {
        $amenity = Amenities::find($this->selectedAmenity);
        $formattedBookingDate = Carbon::parse($this->bookingDate)->format('Y-m-d');

        $remainingPersons = 0;

        foreach ($this->bookingTime as $time) {
            $slot = collect($this->availableSlots)->firstWhere('time', $time);

            if ($slot && $amenity->multiple_booking_status != 0) {
                $formattedSlotTime = Carbon::createFromFormat('H:i', $slot['time'])->format('H:i:s');
                $alreadyBooked = $this->getAlreadyBookedPersons($this->selectedAmenity, $formattedBookingDate, $formattedSlotTime);
                $remainingSlots = $amenity->number_of_person - $alreadyBooked;

                $remainingPersons += max(0, $remainingSlots);
            }
        }

        $this->availablePersons = $remainingPersons > 0 ? $remainingPersons : null;
    }

    public function toggleBookingTime($time)
    {
        if (in_array($time, $this->bookingTime)) {
            $this->bookingTime = array_diff($this->bookingTime, [$time]);
        } else {
            $this->bookingTime[] = $time;
        }

    }


    public function submitBooking()
    {
        $this->validate([
            'selectedAmenity' => 'required|exists:amenities,id',
            'bookingDate' => 'required|date|after_or_equal:today',
            'bookingTime' => 'required|array|min:1',
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
                $alreadyBooked = $this->getAlreadyBookedPersons($this->selectedAmenity, $this->bookingDate, $time);
                $remainingSlots = $amenity->number_of_person - $alreadyBooked;

                if ($this->persons > $remainingSlots) {
                    $validator = Validator::make([], []);
                    $validator->errors()->add('persons', __('messages.personLimitExceeded'));
                    throw new \Illuminate\Validation\ValidationException($validator);
                }
                $remainingSlotsForEachTime[$time] = $remainingSlots;
            }
        }


        $isMultipleBooking = count($this->bookingTime) > 1;
        $bookingType = $isMultipleBooking ? 'multiple' : 'single';
        $uniqueId = Str::random(16);

        foreach ($this->bookingTime as $time) {
            $booking = new BookAmenity();
            $booking->amenity_id = $this->selectedAmenity;
            $booking->booked_by = $this->bookedBy;
            $booking->booking_date = $this->bookingDate;
            $booking->booking_time = $time;

            if ($isMultipleBooking) {
                $booking->persons = $remainingSlotsForEachTime[$time];
            } else {
                $booking->persons = $this->persons;
            }

            $booking->booking_type = $bookingType;
            $booking->unique_id = $uniqueId;
            $booking->save();
        }

        try {
            $user = user();
            $bookings = BookAmenity::where('unique_id', $uniqueId)->get();
            $user->notify(new AmenityBookingNotification($bookings));

            $this->alert('success', __('messages.bookingSuccess'));

            $this->dispatch('hideAddBookAmenity');
            $this->resetForm();
            $this->updatedBookingDate();

        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end'
            ]);
        }

    }


    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['selectedAmenity', 'bookingDate', 'bookingTime', 'persons']);
    }

    public function render()
    {
        return view('livewire.forms.add-book-amenity');
    }
}
