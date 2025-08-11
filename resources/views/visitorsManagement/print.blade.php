<!doctype html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Visitor Pass</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body onload="window.print()">
    <div class="print-container">
        <h2 class="text-sm font-bold text-center">Visitor Pass</h2>

        <div class="text-xs">
            <p><strong>Name:</strong> {{ $visitor->visitor_name ?? '--' }}</p>
            <p><strong>Apartment:</strong> {{ $visitor->apartment->apartment_number ?? '--' }}</p>
            <p><strong>Floor:</strong> {{ $visitor->apartment->floors->floor_name ?? '--' }}</p>
            <p><strong>In Time:</strong> {{ $visitor->in_time ? \Carbon\Carbon::parse($visitor->in_time)->format('h:i A') : '--' }}</p>
            <p><strong>Purpose:</strong> {{ $visitor->purpose_of_visit ?? '--' }}</p>
            <p><strong>Mobile:</strong> {{ $visitor->phone_number ?? '--' }}</p>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white !important;
                margin: 0 !important;
                padding: 5px !important;
            }
            .print-container {
                width: 50mm !important; /* Adjust width for a quarter of A4 */
                height: 70mm !important; /* Adjust height accordingly */
                border: 1px solid black;
                padding: 5px;
                font-family: Arial, sans-serif;
                font-size: 10px !important;
            }
            h2 {
                font-size: 12px !important;
                margin-bottom: 5px;
            }
            p {
                margin: 2px 0 !important;
                font-size: 14px !important;
            }
        }
    </style>
</body>

</html>
