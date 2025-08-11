<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Payment Receipt</title>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
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

        .f-21 {
            font-size: 17px;
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

        .line-height {
            line-height: 15px;
        }

        .mt-1 {
            margin-top: 1rem;
        }

        .mb-0 {
            margin-bottom: 0px;
        }

        .b-collapse {
            border-collapse: collapse;
            width: 100%;
        }

        .heading-table-left {
            padding: 8px;
            border: 1px solid #DBDBDB;
            font-weight: bold;
            background-color: #f1f1f3;
            text-align: left;
        }

        .heading-table-right {
            padding: 8px;
            border: 1px solid #DBDBDB;
            text-align: left;
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
            border-left: 0;
            border-right: 0;
        }

        .total-amt {
            padding: 10px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
            font-weight: 700;
        }

        .balance {
            font-size: 13px;
            font-weight: bold;
        }

        .balance-left {
            padding: 10px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
        }

        .balance-right {
            padding: 10px 8px;
            border: 1px solid #e7e9eb;
            border-top: 0;
            border-left: 0;
            border-right: 0;
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

        .page_break {
            page-break-before: always;
        }

        #logo {
            height: 40px;
        }

        .word-break {
            max-width: 175px;
            word-wrap: break-word;
        }

        .summary {
            padding: 11px 10px;
            border: 1px solid #e7e9eb;
            font-size: 11px;
        }

        .border-left-0 {
            border-left: 0 !important;
        }

        .border-right-0 {
            border-right: 0 !important;
        }

        .border-top-0 {
            border-top: 0 !important;
        }

        .border-bottom-0 {
            border-bottom: 0 !important;
        }

        .h3-border {
            border-bottom: 1px solid #AAAAAA;
        }
    </style>
</head>

<body class="content-wrapper">
    <table class="bg-white" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
        <tbody>
            <!-- Table Row Start -->
            <tr>
                <td style="vertical-align: top;">
                    <p class="mb-0 text-black line-height f-11">
                        {{ $payment->id }}
                    </p>
                </td>
                <td align="right" class="text-black f-21 text-uppercase">Payment Receipt<br>
                    <table class="mt-1 text-black f-11 b-collapse rightaligned">
                        <tr>
                            <td class="heading-table-left text-capitalize">Payment Date</td>
                            <td class="heading-table-right">
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- Table Row End -->
            <tr>
                <td height="10"></td>
            </tr>
            <!-- Table Row Start -->
            <tr>
                <td class="text-black f-12" style="vertical-align: top;">
                    <p class="mb-0 line-height">
                        <span class="text-grey text-capitalize">Billed To</span><br>
                        {{ $society->name }}
                    </p>
                    <p class="mb-0 line-height">
                        <span class="text-grey text-capitalize">Address</span><br>
                        {{ $society->address }}
                    </p>
                </td>
                <td align="right">
                    <div style="margin: 0 0 auto auto" class="bg-white text-uppercase paid rightaligned">
                        Paid
                    </div>
                </td>
            </tr>
            <!-- Table Row End -->
            <tr>
                <td height="10"></td>
            </tr>
            <!-- Table Row End -->
        </tbody>
    </table>

    <table width="100%" class="f-14 b-collapse">
        <!-- Table Row Start -->
        <tr class="main-table-heading text-grey">
            <td width="40%">Description</td>
            <td align="right" width="10%">Amount</td>
            <td align="right" width="10%">Paid</td>
            <td align="right">Total ({{ $currencySymbol }})</td>
        </tr>
        <!-- Table Row End -->

        <!-- Table Row Start -->
        <tr class="text-black f-12 main-table-items">
            <td width="40%" class="border-bottom-0">
                <div>Payment for {{ $payment->description }}</div>
            </td>
            <td align="right" width="10%" class="border-bottom-0">{{ $payment->amount }} ({{ $currencySymbol }})</td>
            <td align="right" width="10%" class="border-bottom-0">{{ $payment->amount }} ({{ $currencySymbol }})</td>
            <td align="right" class="border-bottom-0">{{ $payment->amount }} ({{ $currencySymbol }})</td>
        </tr>
        <!-- Table Row End -->

        <!-- Table Row Start -->
        <tr>
            <td class="total-box" align="right" colspan="3">
                <table width="100%" border="0" class="b-collapse">
                    <!-- Table Row Start -->
                    <tr align="right" class="text-grey">
                        <td width="50%" class="total">Total</td>
                    </tr>
                    <!-- Table Row End -->
                </table>
            </td>
            <td class="total-box" align="right">
                <table width="100%" class="b-collapse">
                    <!-- Table Row Start -->
                    <tr align="right" class="text-grey">
                        <td class="total-amt f-15">{{ $payment->amount }} ({{ $currencySymbol }})</td>
                    </tr>
                    <!-- Table Row End -->
                </table>
            </td>
        </tr>
    </table>

    <div class="mt-1 f-12">
        Date & Time: {{ $payment->created_at->format('Y-m-d H:i:s') }}
    </div>

</body>

</html>
