<div class="p-6 bg-white rounded-lg shadow dark:bg-gray-800">
    <!-- Header Section -->
    <div class="mb-8 border-b border-gray-200 dark:border-gray-700 pb-5">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                @lang('menu.maintenanceReport')
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                @lang('messages.maintenanceReportDescription')
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">@lang('messages.reportSettings')</h2>

        <!-- Report Type Selection -->
        <div class="space-y-4 mb-6 w-[50%]">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('messages.reportType')</label>
            <div class="space-y-2">
                <label class="relative flex items-center p-3 rounded-lg bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                    <input type="radio" wire:model.live="reportType" value="month-wise" name="reportType"
                           class="w-4 h-4 text-skin-base bg-gray-100 border-gray-300 focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                    <div class="ml-3">
                        <span class="block text-sm font-medium">@lang('modules.tenant.monthly')</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">@lang('messages.monthlyBreakdown')</span>
                    </div>
                </label>

                <label class="relative flex items-center p-3 rounded-lg bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
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
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('app.month')</label>
                    <x-select id="select-month" class="block w-[50%]" wire:model.live="selectedMonth">
                        @foreach ($months as $month)
                            <option value="{{ $month }}">{{ ucfirst($month) }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">@lang('app.year')</label>
                    <x-select id="select-year" class="block w-[50%]" wire:model.live="selectedYear">
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

        <div class="mt-6">
            <x-button type='button' wire:click="downloadPdf" class="flex items-center bg-skin-base hover:bg-skin-base/90">
                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01"/>
                </svg>
                @lang('app.download') PDF
            </x-button>
        </div>
    </div>
</div>
