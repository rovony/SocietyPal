<?php

namespace App\Notifications;

use App\Models\BookAmenity;
use Illuminate\Notifications\Messages\MailMessage;

class AmenityBookingUpdated extends BaseNotification
{

    public $bookings;
    /**
     * Create a new notification instance.
     */
    public function __construct($bookings)
    {
        $this->bookings = $bookings;
        $this->society = $bookings->first()?->society;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject(__('modules.bookAmenity.bookingUpdated'))
            ->greeting(__('modules.bookAmenity.greeting', ['name' => $notifiable->name]));

        if ($this->bookings->isNotEmpty()) {
            $firstBooking = $this->bookings->first();

            $mailMessage->line(__('modules.bookAmenity.amenityName') . ': ' . $firstBooking->amenity->amenities_name)
                ->line(__('modules.bookAmenity.bookingDate') . ': ' . \Carbon\Carbon::parse($firstBooking->booking_date)->format('d M Y'));

            $timeAndPersons = $this->bookings->map(function ($booking) {
                $personLabel = $booking->persons == 1
                    ? __('modules.bookAmenity.person')
                    : __('modules.bookAmenity.persons');

                return \Carbon\Carbon::parse($booking->booking_time)->format('h:i A') .
                    ($booking->persons ? ' (' . $booking->persons . ' '  . $personLabel . ')' : ' ');
            })->join(', ');

            $mailMessage->line(__('modules.bookAmenity.bookingTimePersons') . ': ' . $timeAndPersons);
            $mailMessage->line(__('modules.bookAmenity.slotTime') . ': ' . ($firstBooking->amenity->slot_time . ' ' . __('modules.settings.min')));
        }

        return $mailMessage->action(__('modules.bookAmenity.viewBookingDetails'), route('book-amenity.index'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'society_id' => $this->society->id,
            'message' => __('modules.bookAmenity.bookingUpdated'),
            'url' => route('book-amenity.index'),
            'created_at' => now()->toDateTimeString(),
            'user_name' => $notifiable->name,
        ];
    }
}
