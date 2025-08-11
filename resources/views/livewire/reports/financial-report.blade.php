<div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
    <!-- Header Section -->
    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                @lang('menu.financialReport')
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                @lang('messages.financialReportDescription')
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">@lang('messages.reportSettings')</h2>

        <!-- Report Type Selection -->
        <div class="space-y-4 mb-6 w-[100%]">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                @lang('messages.reportType')
            </label>

            <div class="flex gap-4">
                <label class="flex-1 flex items-center p-3 rounded-lg bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                    <input type="radio" wire:model.live="reportType" value="month-wise" name="reportType"
                        class="w-4 h-4 text-skin-base bg-gray-100 border-gray-300 focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                    <div class="ml-3">
                        <span class="block text-sm font-medium">@lang('modules.tenant.monthly')</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">@lang('messages.monthlyBreakdown')</span>
                    </div>
                </label>

                <label class="flex-1 flex items-center p-3 rounded-lg bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                    <input type="radio" wire:model.live="reportType" value="year-wise" name="reportType"
                        class="w-4 h-4 text-skin-base bg-gray-100 border-gray-300 focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                    <div class="ml-3">
                        <span class="block text-sm font-medium">@lang('modules.tenant.annually')</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">@lang('messages.yearlyAnalysis')</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Time Period Selection -->
        @if ($reportType === 'month-wise')
           <div class="flex gap-4">
                <div class="w-1/2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('app.month')</label>
                    <x-select id="select-month" class="block w-full" wire:model.live="selectedMonth">
                        @foreach ($months as $month)
                            <option value="{{ $month }}">{{ ucfirst($month) }}</option>
                        @endforeach
                    </x-select>
                </div>

                <div class="w-1/2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('app.year')</label>
                    <x-select id="select-year" class="block w-full" wire:model.live="selectedYear">
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </x-select>
                </div>
            </div>
        @elseif ($reportType === 'year-wise')
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('app.year')</label>
                <x-select id="select-year" class="block w-[50%]" wire:model.live="selectedYear">
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </x-select>
            </div>
        @endif
    </div>
    <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full">
                <div class="mb-4">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('messages.financialReportSummary')</h1>
                </div>
                <div class="items-center justify-between block sm:flex ">
                    <div class="flex items-center mb-4 sm:mb-0">
                        @if(isRole() == 'Admin')
                            <x-secondary-button wire:click="$dispatch('showReportFilters')" class="ml-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filter mr-1" viewBox="0 0 16 16">
                                    <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
                                </svg> @lang('app.showFilter')
                            </x-secondary-button>
                        @endif
                    </div>
                    <div class="inline-flex items-center gap-4">
                        <x-button type='button' wire:click="downloadPdf" class="flex items-center bg-skin-base hover:bg-skin-base/90">
                            <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01"/>
                            </svg>
                            @lang('app.download') PDF
                        </x-button>
                    </div>

                </div>

            </div>

    </div>
    <div class="px-4 pt-2 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">
        @if ($showFilters && isRole() == 'Admin')
            @include('maintenance.filters-financial-report')
        @endif
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden shadow">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.settings.apartment')</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.financial.maintenanceBilled')</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.financial.maintenancePaid')</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.financial.utilityBilled')</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.financial.utilityPaid')</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.financial.totalPaid')</th>
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">@lang('modules.financial.totalPending')</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='user-list-{{ microtime() }}'>
                                @php
                                    $totalMaintenanceBilled = 0;
                                    $totalMaintenancePaid = 0;
                                    $totalUtilityBilled = 0;
                                    $totalUtilityPaid = 0;
                                    $totalPaid = 0;
                                    $totalPending = 0;
                                @endphp

                                @forelse ($financialData as $item)
                                    <tr>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ $item['apartment'] }}</td>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($item['maintenance_billed']) }}</td>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($item['maintenance_paid']) }}</td>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($item['utility_billed']) }}</td>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($item['utility_paid']) }}</td>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($item['total_paid']) }}</td>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($item['total_pending']) }}</td>
                                    </tr>

                                    @php
                                        $totalMaintenanceBilled += $item['maintenance_billed'];
                                        $totalMaintenancePaid += $item['maintenance_paid'];
                                        $totalUtilityBilled += $item['utility_billed'];
                                        $totalUtilityPaid += $item['utility_paid'];
                                        $totalPaid += $item['total_paid'];
                                        $totalPending += $item['total_pending'];
                                    @endphp
                                @empty
                                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <td class="p-8 dark:text-gray-400 text-center" colspan="{{ $colspan ?? 12 }}">
                                            {{ __('messages.noRecordFound') }}
                                        </td>
                                    </tr>
                                @endforelse                
                            </tbody>
                            @if (!empty($financialData))
                                <tfoot>
                                    <tr class="total-row font-bold bg-gray-100 dark:bg-gray-700">
                                        <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">@lang('app.total')</td>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($totalMaintenanceBilled) }}</td>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($totalMaintenancePaid) }}</td>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($totalUtilityBilled) }}</td>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($totalUtilityPaid) }}</td>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($totalPaid) }}</td>
                                        <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ currency_format($totalPending) }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
