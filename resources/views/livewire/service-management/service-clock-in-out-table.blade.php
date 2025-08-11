<div class="px-4 pt-2 xl:grid-cols-3 xl:gap-4 bg-white dark:bg-gray-900">
    <div class="flex items-center justify-between mb-4">
        <div class="lg:flex items-center">
            <form class="ltr:sm:pr-3 rtl:sm:pl-3" action="#" method="GET">

                <div class="lg:flex gap-2 items-center">
                    <x-select id="dateRangeType" class="block w-fit" wire:model="dateRangeType"
                     wire:change="setDateRange">
                        <option value="today">@lang('app.today')</option>
                        <option value="currentWeek">@lang('app.currentWeek')</option>
                        <option value="lastWeek">@lang('app.lastWeek')</option>
                        <option value="last7Days">@lang('app.last7Days')</option>
                        <option value="currentMonth">@lang('app.currentMonth')</option>
                        <option value="lastMonth">@lang('app.lastMonth')</option>
                        <option value="currentYear">@lang('app.currentYear')</option>
                        <option value="lastYear">@lang('app.lastYear')</option>
                    </x-select>

                    <div id="date-range-picker" date-rangepicker class="flex items-center w-full">
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                </svg>
                            </div>
                            <input id="datepicker-range-start" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.change='startDate' placeholder="@lang('app.selectStartDate')">
                            </div>
                            <span class="mx-4 text-gray-500">@lang('app.to')</span>
                            <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                </svg>
                            </div>
                            <input id="datepicker-range-end" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model.live='endDate' placeholder="@lang('app.selectEndDate')">
                        </div>
                        <div class="ml-4">
                            <x-secondary-button wire:click="$dispatch('resetdates')" class="gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
                                    <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
                                </svg>
                                @lang('app.reset')
                            </x-secondary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="p-4 w-128 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.serviceManagement.servicePerson')
                                </th>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.serviceManagement.clockInTime')
                                </th>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.serviceManagement.clockOutTime')
                                </th>
                                @if (user_can('Update Service Time Logging') || user_can('Delete Service Time Logging') || isRole() == 'Guard')
                                    <th scope="col"
                                        class="p-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                        @lang('app.action')
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='attendance-list-{{ microtime() }}'>
                            @forelse ($attendances as $item)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='attendance-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @if (user_can('Show Service Provider'))
                                        <a href="javascript:;" wire:click="showServiceManagement({{ $item->service->id }})" class="text-base font-semibold hover:underline dark:text-gray-400">
                                            {{ $item->service->contact_person_name }}
                                        </a>
                                    @else
                                        {{ $item->service->contact_person_name }}
                                    @endif
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ \Carbon\Carbon::parse($item->clock_in_date)->timezone(timezone())->format('d F Y') }}, {{ \Carbon\Carbon::parse($item->clock_in_time)->timezone(timezone())->format('h:i A') }}
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($item->status == 'clock_in' && ( user_can('Update Service Time Logging') || isRole() == 'Guard'))
                                        <x-button wire:click="clockOut({{ $item->id }})" class="text-xs">
                                            @lang('app.clockOut')
                                        </x-button>
                                    @elseif($item->status == 'clock_in' && !user_can('Update Service Time Logging') && isRole() != 'Guard')
                                        <span class="dark:text-gray-400">--</span>
                                    @else
                                        {{ \Carbon\Carbon::parse($item->clock_out_date)->timezone(timezone())->format('d F Y') }}, {{ \Carbon\Carbon::parse( $item->clock_out_time)->timezone(timezone())->format('h:i A') }}
                                    @endif
                                </td>
                                @if (user_can('Update Service Time Logging') || user_can('Delete Service Time Logging') || isRole() == 'Guard')
                                    <td class="p-4 space-x-2 whitespace-nowrap text-right">
                                        @if(user_can('Update Service Time Logging') || isRole() == 'Guard')
                                            <x-secondary-button wire:click="showEditAttendance({{ $item->id }})" wire:key="attendance-edit-{{ $item->id . microtime() }}">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                    </path>
                                                    <path fill-rule="evenodd"
                                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                @lang('app.update')
                                            </x-secondary-button>
                                        @endif

                                        @if(user_can('Delete Service Time Logging') || isRole() == 'Guard')
                                            <x-danger-button  wire:click="showDeleteAttendance({{ $item->id }})"  wire:key='attendance-del-{{ $item->id . microtime() }}'>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                            </x-danger-button>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @empty
                                <x-no-results :message="__('messages.noRecordFound')" />
                            @endforelse

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div wire:key='attendance-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center mb-4 sm:mb-0 w-full">
            {{ $attendances->links() }}
        </div>
    </div>

    <x-dialog-modal wire:model.live="showEditAttendanceModal">
        <x-slot name="title">
            {{ __("modules.serviceManagement.editClockIn") }}
        </x-slot>

        <x-slot name="content">
            @if ($attendance)
            @livewire('forms.editAttendance', ['attendance' => $attendance], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditAttendanceModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <x-confirmation-modal wire:model="confirmDeleteAttendanceModal">
        <x-slot name="title">
            @lang('modules.serviceManagement.deleteRecord')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteAttendanceModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($attendance)
            <x-danger-button class="ml-3" wire:click='deleteAttendance({{ $attendance->id }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

    <x-right-modal wire:model.live="showServiceManagementModal">
        <x-slot name="title">
            {{ __("modules.serviceManagement.showService") }}
        </x-slot>

        <x-slot name="content">
            @if ($serviceManagementShow)
                @livewire('forms.showServiceProviderManagement', ['serviceManagementShow' => $serviceManagementShow], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showServiceManagementModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>


    @script
    <script>
        const datepickerEl1 = document.getElementById('datepicker-range-start');

        datepickerEl1.addEventListener('changeDate', (event) => {
            $wire.dispatch('setStartDate', { start: datepickerEl1.value });
        });

        const datepickerEl2 = document.getElementById('datepicker-range-end');

        datepickerEl2.addEventListener('changeDate', (event) => {
            $wire.dispatch('setEndDate', { end: datepickerEl2.value });
        });
    </script>
    @endscript
</div>
