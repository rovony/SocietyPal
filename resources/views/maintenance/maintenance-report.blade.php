<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $type }} @lang('menu.maintenanceReport')</title>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <style>
        @page {
            size: landscape;
            margin: 20px;
        }
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

        .f-13 {
            font-size: 13px;
        }

        .f-14 {
            font-size: 13px;
        }

        .f-15 {
            font-size: 13px;
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

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-capitalize {
            text-transform: capitalize;
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
        }

        .heading-table-left {
            padding: 6px;
            border: 1px solid #DBDBDB;
            font-weight: bold;
            background-color: #f1f1f3;
            border-right: 0;
        }

        .heading-table-right {
            padding: 6px;
            border: 1px solid #DBDBDB;
            border-left: 0;
        }

        .paid {
            color: #28a745 !important;
            border: 1px solid #28a745;
            position: relative;
            padding: 3px 8px;
            font-size: 12px;
            border-radius: 0.25rem;
            width: 75px;
            text-align: center;
            margin-top: 50px;
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

        .page {
            page-break-before: always;
        }

        .table-container {
            overflow-x: auto; 
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .th-td-style {
            border: 1px solid #ddd;
            padding: 4px; 
            text-align: left;
            font-size: 10px;
            word-wrap: break-word; 
        }
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f4f4f4;
        }
    </style>
    

</head>

<body class="content-wrapper">
    <table class="bg-white" border="0" cellpadding="0" cellspacing="0" width="100%" role="presentation">
        <tbody>
            <tr>
                <td align="center" class="f-21 text-black text-uppercase">@lang('menu.maintenanceReport')<br>
                    @if($type == 'Month-wise')
                        <span class="f-12">@lang('modules.tenant.monthly')</span>
                    @else
                        <span class="f-12">@lang('modules.tenant.annually')</span>
                    @endif 

                    <table class="text-black mt-1 f-11 b-collapse rightaligned">
                        <tr>
                            @if($type == 'Month-wise')
                                <td class="heading-table-left text-capitalize">@lang('app.month') & @lang('app.year')</td>
                                <td class="heading-table-right">
                                    {{ $month ?? '' }} {{ $year }}                            
                                </td>
                            @else
                                <td class="heading-table-left text-capitalize">@lang('app.year')</td>
                                <td class="heading-table-right">
                                    {{ $month ?? '' }} {{ $year }}                            
                                </td>
                            @endif
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td height="10"></td>
            </tr>
            <tr>
                <td class="f-12 text-black" style="vertical-align: top;">
                    <p class="line-height mb-0">
                        <span class="text-grey text-capitalize">@lang('modules.utilityBills.societyName')</span><br>
                        {{ $society->name }}
                    </p>
                    <p class="line-height mb-0">
                        <span class="text-grey text-capitalize">@lang('modules.receipt.address')</span><br>
                        {{ $society->address }}
                    </p>
                    <p class="line-height mb-0">
                        <span class="text-grey text-capitalize">@lang('modules.settings.totalApartments')</span><br>
                        {{ count($data) }}                    
                    </p>
                </td>
            </tr>
            <tr>
                <td height="10"></td>
            </tr>

        </tbody>
    </table>

    @if($type == 'Month-wise')
        <div class="table-container">
            <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                <thead>
                    <tr>
                        <th class="th-td-style">@lang('modules.settings.apartment')</th>
                        <th class="th-td-style">@lang('modules.maintenance.amountBilled') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.maintenance.amountPaid') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.maintenance.pending') ({{ $society->currency->currency_code }})</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalBilled = 0;
                        $totalPaid = 0;
                        $totalPending = 0;
                    @endphp

                    @foreach ($data as $item)
                    <tr>
                        <td class="th-td-style">{{ $item['apartment'] }}</td>
                        <td class="th-td-style">{{ $item['billed'] ?? $item['total_billed'] }}</td>
                        <td class="th-td-style">{{ $item['paid'] ?? $item['total_paid'] }}</td>
                        <td class="th-td-style">{{ $item['pending'] }}</td>
                    </tr>

                    @php
                        $totalBilled += $item['billed'] ?? $item['total_billed'];
                        $totalPaid += $item['paid'] ?? $item['total_paid'];
                        $totalPending += $item['pending'];
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td class="th-td-style"><strong>@lang('app.total')</strong></td>
                        <td class="th-td-style">{{ $totalBilled }}</td>
                        <td class="th-td-style">{{ $totalPaid }}</td>
                        <td class="th-td-style">{{ $totalPending }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="table-container">
            <table class="table table-bordered" style="width: 100%; border-collapse: collapse; table-layout: fixed;">
                <thead>
                    <tr>
                        <th class="th-td-style">@lang('modules.settings.apartment')</th>
                        <th class="th-td-style">@lang('modules.rent.jan') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.rent.feb') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.rent.mar') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.rent.apr') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.rent.may') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.rent.jun') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.rent.jul') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.rent.aug') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.rent.sep') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.rent.oct') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.rent.nov') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.rent.dec') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.maintenance.totalBilled') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.maintenance.totalPaid') ({{ $society->currency->currency_code }})</th>
                        <th class="th-td-style">@lang('modules.maintenance.pending') ({{ $society->currency->currency_code }})</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalJanuary = 0;
                        $totalFebruary = 0;
                        $totalMarch = 0;
                        $totalApril = 0;
                        $totalMay = 0;
                        $totalJune = 0;
                        $totalJuly = 0;
                        $totalAugust = 0;
                        $totalSeptember = 0;
                        $totalOctober = 0;
                        $totalNovember = 0;
                        $totalDecember = 0;
                        $totalBilled = 0;
                        $totalPaid = 0;
                        $totalPending = 0;
                    @endphp
                    @foreach($data as $row)
                    <tr>
                        <td class="th-td-style">{{ $row['apartment'] }}</td>
                        <td class="th-td-style">{{ $row['january'] }}</td>
                        <td class="th-td-style">{{ $row['february'] }}</td>
                        <td class="th-td-style">{{ $row['march'] }}</td>
                        <td class="th-td-style">{{ $row['april'] }}</td>
                        <td class="th-td-style">{{ $row['may'] }}</td>
                        <td class="th-td-style">{{ $row['june'] }}</td>
                        <td class="th-td-style">{{ $row['july'] }}</td>
                        <td class="th-td-style">{{ $row['august'] }}</td>
                        <td class="th-td-style">{{ $row['september'] }}</td>
                        <td class="th-td-style">{{ $row['october'] }}</td>
                        <td class="th-td-style">{{ $row['november'] }}</td>
                        <td class="th-td-style">{{ $row['december'] }}</td>
                        <td class="th-td-style">{{ $row['total_billed'] }}</td>
                        <td class="th-td-style">{{ $row['total_paid'] }}</td>
                        <td class="th-td-style">{{ $row['total_pending'] }}</td>
                    </tr>
                    @php
                        $totalJanuary += $row['january'];
                        $totalFebruary += $row['february'];
                        $totalMarch += $row['march'];
                        $totalApril += $row['april'];
                        $totalMay += $row['may'];
                        $totalJune += $row['june'];
                        $totalJuly += $row['july'];
                        $totalAugust += $row['august'];
                        $totalSeptember += $row['september'];
                        $totalOctober += $row['october'];
                        $totalNovember += $row['november'];
                        $totalDecember += $row['december'];
                        $totalBilled += $row['total_billed'];
                        $totalPaid += $row['total_paid'];
                        $totalPending += $row['total_pending'];
                    @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td class="th-td-style"><strong>@lang('app.total')</strong></td>
                        <td class="th-td-style">{{ $totalJanuary }}</td>
                        <td class="th-td-style">{{ $totalFebruary }}</td>
                        <td class="th-td-style">{{ $totalMarch }}</td>
                        <td class="th-td-style">{{ $totalApril }}</td>
                        <td class="th-td-style">{{ $totalMay }}</td>
                        <td class="th-td-style">{{ $totalJune }}</td>
                        <td class="th-td-style">{{ $totalJuly }}</td>
                        <td class="th-td-style">{{ $totalAugust }}</td>
                        <td class="th-td-style">{{ $totalSeptember }}</td>
                        <td class="th-td-style">{{ $totalOctober }}</td>
                        <td class="th-td-style">{{ $totalNovember }}</td>
                        <td class="th-td-style">{{ $totalDecember }}</td>
                        <td class="th-td-style">{{ $totalBilled }}</td>
                        <td class="th-td-style">{{ $totalPaid }}</td>
                        <td class="th-td-style">{{ $totalPending }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif
</body>

</html>
