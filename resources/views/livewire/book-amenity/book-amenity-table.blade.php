<div class="px-4 pt-2 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">
    @if ($showFilters)
        @include('book-amenity.filters')
    @endif
    <div class="flex items-center justify-between mb-4">
        <div class="items-center lg:flex">
            <form class="sm:pr-3" action="#" method="GET">
                <div class="items-center gap-2 lg:flex">
                    <div id="date-range-picker" date-rangepicker class="flex items-center">
                        <div class="relative">
                            <div class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-3">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </div>
                            <input id="datepicker-range-start" name="start" type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                wire:model.change='startDate' placeholder="@lang('app.selectStartDate')">
                        </div>
                        <span class="mx-4 text-gray-500">@lang('app.to')</span>
                        <div class="relative">
                            <div class="absolute inset-y-0 flex items-center pointer-events-none start-0 ps-3">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                </svg>
                            </div>
                            <input id="datepicker-range-end" name="end" type="text"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                wire:model.live='endDate' placeholder="@lang('app.selectEndDate')">
                        </div>
                        <div class="ml-4">
                            <x-secondary-button wire:click="$dispatch('resetBookAmenity')" class="gap-1">
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
        @if($showActions)
            <x-dropdown dropdownClasses="z-50">
                <x-slot name="trigger">
                    <span class="inline-flex">
                        <button type="button"
                            class="inline-flex items-center p-2 ml-4 text-sm font-medium text-gray-500 transition duration-150 ease-in-out bg-white border border-gray-300 hover:text-gray-700 focus:outline-none hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                            @lang('app.action')
                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </span>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link href="javascript:;" wire:click="showSelectedDeleteBookAmenity">
                        <span class="inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            @lang('app.delete')
                        </span>
                    </x-dropdown-link>
                </x-slot>
            </x-dropdown>
        @endif
    </div>
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                @if(user_can('Delete Book Amenity'))
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </th>
                                @endif
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.bookAmenity.bookedID')
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.bookAmenity.amenityName')
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.bookAmenity.bookedBy')
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.bookAmenity.bookingDate')
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.bookAmenity.bookingTime')
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.bookAmenity.slotTime')
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.bookAmenity.numberOfpersons')
                                </th>
                                <th scope="col" class="p-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='amenity-list-{{ microtime() }}'>
                            @forelse ($amenities as $group)

                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='amenity-{{ $group->first()->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                    @if(user_can('Delete Book Amenity'))
                                        <td class="p-4">
                                            <input type="checkbox" wire:model.live="selected" value="{{ $group->first()->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                        </td>
                                    @endif
                                    <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $group->first()->id }}
                                    </td>
                                    <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                        <a href="javascript:;" wire:click="showBookAmenityDetail({{ $group->first()->id }})" class="text-base font-semibold hover:underline dark:text-black-400">
                                            {{ $group->first()->amenity->amenities_name }}
                                        </a>
                                    </td>
                                    <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $group->first()->user->name }}
                                    </td>
                                    <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ \Carbon\Carbon::parse($group->first()->booking_date)->format('d F Y') ?? '--' }}
                                    </td>
                                    <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        @if($group->count() > 1)
                                        <a href="javascript:;" wire:click="showBookAmenityDetail({{ $group->first()->id }})" class="text-base font-semibold hover:underline dark:text-black-400">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-500 dark:text-yellow-100">
                                                {{ ucFirst($group->first()->booking_type) }}
                                            </span>
                                        </a>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-500 dark:text-blue-100">
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $group->first()->booking_time)->format('h:i A') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $group->first()->amenity->slot_time }} min
                                    </td>
                                    <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        @if($group->first()->persons == 'null' || $group->first()->persons == 0 || $group->count() > 1)
                                            <span class="dark:text-gray-400">--</span>
                                        @else
                                            {{ $group->first()->persons }}
                                        @endif
                                    </td>
                                    <td class="p-4 space-x-2 text-right whitespace-nowrap">
                                        @if(user_can('Update Book Amenity') || $group->first()->booked_by == auth()->id())
                                            <x-secondary-button wire:click="showEditBookAmenity({{ $group->first()->id }})" wire:key="amenity-edit-{{ $group->first()->id . microtime() }}">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"></path>
                                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                @lang('app.update')
                                            </x-secondary-button>
                                        @endif

                                        @if(user_can('Delete Book Amenity') || $group->first()->booked_by == auth()->id())
                                            <x-danger-button  wire:click="showDeleteBookAmenity({{ $group->first()->id }})"  wire:key='amenity-del-{{ $group->first()->id . microtime() }}'>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                            </x-danger-button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <td class="p-4 space-x-6 dark:text-gray-400" colspan="12">
                                        @lang('messages.noBookingFound')
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div wire:key='book-amenity-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-4 sm:mb-0">
            {{ $paginatedUniqueIds->links() }}
        </div>
    </div>

    <x-right-modal wire:model.live="showEditBookAmenityModal">
        <x-slot name="title">
            {{ __("modules.bookAmenity.editBookAmenity") }}
        </x-slot>

        <x-slot name="content">
            @if ($amenity)
            @livewire('forms.editBookAmenity', ['amenity' => $amenity], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditBookAmenityModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-right-modal wire:model.live="showBookAmenityDetailModal">
        <x-slot name="title">
            {{ __("modules.bookAmenity.viewBookAmenity") }}
        </x-slot>

        <x-slot name="content">
            @if ($amenity)
            @livewire('forms.showBookAmenity', ['amenity' => $amenity], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showBookAmenityDetailModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model="confirmDeleteBookAmenityModal">
        <x-slot name="title">
            @lang('modules.bookAmenity.deleteBookAmenity')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteBookAmenityModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($amenity)
            <x-danger-button class="ml-3" wire:click='deleteBookAmenity({{ $amenity->id }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="confirmSelectedDeleteBookAmenityModal">
        <x-slot name="title">
            @lang('modules.bookAmenity.deleteBookAmenity')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmSelectedDeleteBookAmenityModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click='deleteSelected' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
         </x-slot>
    </x-confirmation-modal>


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
