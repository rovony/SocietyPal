<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Common Area Bill - {{ $commonAreaBill->apartmentId }}</title>
    <style>
        * {
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }

        .bg-grey {
            background-color: #F2F4F7;
        }

        .bg-white {
            background-color: #fff;
        }

        .border-radius-25 {
            border-radius: 0.25rem;
        }

        .p-25 {
            padding: 1.25rem;
        }

        .f-11 {
            font-size: 11px;
        }

        .f-12 {
            font-size: 12px;
        }

        .f-14 {
            font-size: 14px;
        }

        .text-black {
            color: #28313c;
        }

        .text-grey {
            color: #616e80;
        }

        .font-weight-700 {
            font-weight: 700;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .line-height {
            line-height: 15px;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .main-table-heading {
            border: 1px solid #DBDBDB;
            background-color: #f1f1f3;
            font-weight: 700;
        }

        .main-table-heading td {
            padding: 5px 8px;
            border: 1px solid #DBDBDB;
            font-size: 11px;
        }

        .main-table-items td {
            padding: 5px 8px;
            border: 1px solid #e7e9eb;
        }

        .total-box {
            border: 1px solid #e7e9eb;
            padding: 0px;
            border-bottom: 0px;
        }

        .total {
            padding: 10px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            font-weight: 700;
        }

        .total-amt {
            padding: 10px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            font-weight: 700;
        }

        .centered {
            margin: 0 auto;
        }

        .rightaligned {
            margin-right: 0;
            margin-left: auto;
        }

        .leftaligned {
            margin-left: 0;
            margin-right: auto;
        }

        @media print {
            @page {
                margin: 0;
                size: auto; /* Auto size for printing */
            }
        }
    </style>
</head>

<body class="content-wrapper">
    <table class="bg-white" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
        <tbody>
            <tr>
                <td style="vertical-align: top;">
                    <p class="mb-0 text-black line-height f-11">
                        Common Area Bill for Apartment: {{ $commonAreaBill->apartmentId }}
                    </p>
                </td>
                <td align="right" class="text-black f-21 text-uppercase">Common Area Bill<br>
                    <table class="mt-1 text-black f-11 b-collapse rightaligned">
                        <tr>
                            <td class="heading-table-left text-capitalize">Due Date</td>
                            <td class="heading-table-right">
                                {{ \Carbon\Carbon::parse($commonAreaBill->billDueDate)->format('d M, Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="heading-table-left text-capitalize">Bill Date</td>
                            <td class="heading-table-right">
                                {{ \Carbon\Carbon::parse($commonAreaBill->billDate)->format('d M, Y') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td height="10"></td>
            </tr>
            <tr>
                <td class="text-black f-12" style="vertical-align: top;">
                    <p class="mb-0 line-height">
                        <span class="text-grey text-capitalize">Billed To</span><br>
                        {{ $commonAreaBill->society->name }}
                    </p>
                    <p class="mb-0 line-height">
                        <span class="text-grey text-capitalize">Address</span><br>
                        {{ $commonAreaBill->society->address }}
                    </p>
                </td>
                <td align="right">
                    <div style="margin: 0 0 auto auto" class="bg-white text-uppercase paid rightaligned">
                        @if($commonAreaBill->status == 'paid')
                            Paid
                        @else
                            Unpaid
                        @endif
                    </div>
                </td>
            </tr>
            <tr>
                <td height="10"></td>
            </tr>
        </tbody>
    </table>

    <table width="100%" class="f-14 b-collapse">
        <tr class="main-table-heading text-grey">
            <td width="40%">Description</td>
            <td align="right" width="10%">Bill Amount</td>
            <td align="right" width="10%">Amount Paid</td>
            <td align="right">Total ({{ $currencySymbol }})</td>
        </tr>
        <tr class="text-black f-12 main-table-items">
            <td width="40%" class="border-bottom-0">
                <div>Apartment Number: {{ $commonAreaBill->apartmentId }}</div>
                <div>Bill Type: {{ $commonAreaBill->billTypeId }}</div>
            </td>
            <td align="right" width="10%" class="border-bottom-0">{{ currency_format($commonAreaBill->billAmount) }}</td>
            <td align="right" width="10%" class="border-bottom-0">{{ currency_format($commonAreaBill->billAmount) }}</td>
            <td align="right" class="border-bottom-0">{{ currency_format($commonAreaBill->billAmount) }}</td>
        </tr>
        <tr>
            <td class="total-box" align="right" colspan="3">
                <table width="100%" border="0" class="b-collapse">
                    <tr align="right" class="text-grey">
                        <td width="50%" class="total">Total</td>
                    </tr>
                </table>
            </td>
            <td class="total-box" align="right">
                <table width="100%" class="b-collapse">
                    <tr align="right" class="text-grey">
                        <td class="total-amt f-15">{{ currency_format($commonAreaBill->billAmount) }} ({{ $currencySymbol }})</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="mt-1 f-12">
        Date & Time: {{ $commonAreaBill->created_at->format('Y-m-d H:i:s') }}
    </div>

</body>

</html>