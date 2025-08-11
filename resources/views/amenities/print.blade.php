<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Amenity - {{ $amenity->amenities_name }}</title>
    <style>
        * {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            padding: 20px;
            background-color: #f9f9f9;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #4A5568; /* Gray-700 */
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
            color: #4A5568; /* Gray-700 */
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #4A5568; /* Gray-700 */
        }

        .value {
            margin-top: 5px;
            color: #718096; /* Gray-500 */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #E2E8F0; /* Gray-300 */
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #EDF2F7; /* Gray-200 */
            color: #4A5568; /* Gray-700 */
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #4A5568; /* Gray-700 */
        }

        @media print {
            @page {
                margin: 0;
                size: auto;
            }
            body {
                margin: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Amenity Booking Details</h1>
        <h3>Booking Id : {{$amenity->id}}</h3>
        <div class="grid">
            <div>
                <div class="label">{{ __('modules.bookAmenity.amenityName') }}</div>
                <div class="value">{{ $amenity->amenity->amenities_name }}</div>
            </div>
            <div>
                <div class="label">{{ __('modules.bookAmenity.bookedBy') }}</div>
                <div class="value">{{ $amenity->user->name }}</div>
            </div>
        </div>

        <div class="grid">
            <div>
                <div class="label">{{ __('modules.bookAmenity.bookingDate') }}</div>
                <div class="value">{{ \Carbon\Carbon::parse($amenity->booking_date)->format('d F Y') ?? '--' }}</div>
            </div>
            <div>
                <div class="label">{{ __('modules.bookAmenity.slotTime') }}</div>
                <div class="value">{{ $amenity->slot_time }} Min</div>
            </div>
        </div>

        <div>
            <div class="label">{{ __('modules.bookAmenity.bookingTimePersons') }}</div>
            <table>
                <thead>
                    <tr>
                        <th>@lang('modules.bookAmenity.bookingTime')</th>
                        <th>@lang('modules.bookAmenity.numberOfpersons')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                    <tr>
                        <td>{{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->booking_time)->format('h:i A') }}</td>
                        <td>
                            @if ($booking->persons == 0 || $booking->persons == 'null')
                                <span>--</span>
                            @else
                                {{ $booking->persons }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

     
    </div>
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>

</html>

