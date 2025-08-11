 Hello{{ $username }}
# New Amenities Available!

You have been notified about a new amenity: **{{ $amenities->amenities_name }}**.

@if ($amenities->booking_status)
    ## Booking Status
    {{ $amenities->booking_status ? 'Yes' : 'No' }}
@endif


If you have any questions or concerns, feel free to reach out to us.

Thank you,<br>
{{ config('app.name') }}
