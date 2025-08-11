<div class="px-4 pt-2 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">
    @if ($showFilters)
        @include('amenities.filters')
    @endif
    <div class="flex items-center justify-end">
        @if($showActions)
            <x-dropdown dropdownClasses="z-50">
                <x-slot name="trigger">
                    <span class="inline-flex">
                        <button type="button"
                            class="inline-flex items-center justify-center p-2 text-sm font-medium text-gray-500 transition duration-150 ease-in-out bg-white border border-gray-300 hover:text-gray-700 focus:outline-none hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 mb-2">
                            @lang('app.action')
                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </span>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link href="javascript:;" wire:click="showSelectedDelete">
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
                                @if(user_can('Delete Amenities'))
                                    <th scope="col" class="p-4 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-400">
                                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </th>
                                @endif
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.societyAmenitiesName')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.societyAmenitiesStatus')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.societyAmenitiesBooking')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.societySlotTiming')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.societyMultipleBooking')
                                </th>
                                @if(user_can('Update Amenities') || user_can('Delete Amenities'))

                                <th scope="col"
                                    class="p-4 text-xs font-medium text-right uppercase text-geray-500 dark:text-gray-400">
                                    @lang('app.action')
                                </th>
                                @endif

                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>

                            @forelse ($amenitiesData as $item)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='member-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                @if(user_can('Delete Amenities'))
                                    <td class="p-4 text-base font-semibold text-center text-gray-900 whitespace-nowrap dark:text-white">
                                        <input type="checkbox" wire:model.live="selected" value="{{ $item->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </td>
                                @endif
                                <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->amenities_name }}
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($item->status === 'available')
                                        <x-badge type="success">{{ __('app.' . $item->status) }}</x-badge>
                                    @else
                                        <x-badge type="danger">{{ __('app.' . $item->status) }}</x-badge>

                                    @endif
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->booking_status ? 'Yes' : 'No' }}
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white ">
                                @if($item->booking_status == 1 )
                                    <p >@lang('modules.settings.amenitiesStartTime') {{ $item->start_time ? \Carbon\Carbon::parse($item->start_time)->format('h:i A') : '--' }}</p>
                                    <p> @lang('modules.settings.amenitiesEndTime') {{ $item->end_time ? \Carbon\Carbon::parse($item->end_time)->format('h:i A') : '--' }}</p>
                                    @if($item->slot_time > 0)<p> @lang('modules.settings.amenitiesSlotTime') {{ $item->slot_time }} @lang('modules.settings.min') </p> @endif
                                @else
                                    <p>@lang('modules.settings.notAvailable') </p>
                                @endif
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($item->multiple_booking_status == 1)
                                        <p>@lang('modules.settings.amenitiesMultipleBooking') {{ $item->multiple_booking_status ?'Yes' : '--'}}</p>
                                        @if($item->number_of_person > 0)  <p>@lang('modules.settings.amenitiesNumberOfPerson') {{ $item->number_of_person}}</p>@endif
                                    @else
                                        <p> @lang('modules.settings.notAvailable') </p>
                                    @endif
                                </td>
                                @if(user_can('Update Amenities') || user_can('Delete Amenities'))
                                    <td class="p-4 space-x-2 text-right whitespace-nowrap">
                                        @if(user_can('Update Amenities'))
                                            <x-secondary-button wire:click='showEditAmenities({{ $item->id }})' wire:key='member-edit-{{ $item->id . microtime() }}'
                                                wire:key='editmenu-item-button-{{ $item->id }}'>
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                    </path>
                                                    <path fill-rule="evenodd"
                                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                @lang('app.update')
                                            </x-secondary-button>
                                        @endif

                                        @if(user_can('Delete Amenities'))
                                            <x-danger-button  wire:click="showDeleteAmenities({{ $item->id }})"  wire:key='member-del-{{ $item->id . microtime() }}'>
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
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="p-4 space-x-6 dark:text-gray-400" colspan="6">
                                    @lang('messages.noAmenitiesManagmentFound')
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div wire:key='amenity-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="mb-4 sm:mb-0 w-full">
            {{ $amenitiesData->links() }}
        </div>
    </div>

    <x-right-modal wire:model.live="showEditAmenitiesModal">
        <x-slot name="title">
            {{ __("modules.settings.editSocietyAmenitiesManagment") }}
        </x-slot>

        <x-slot name="content">
            @if ($amenitiesManagment)
            @livewire('forms.editAmenities', ['amenitiesManagment' => $amenitiesManagment], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditAmenitiesModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model="confirmDeleteAmenitiesModal">
        <x-slot name="title">
            @lang('modules.settings.deleteAmenitiesManagement')
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteAmenitiesModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>
            @if ($amenitiesManagment)
            <x-danger-button class="ml-3" wire:click='deleteAmenities({{ $amenitiesManagment->id }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="confirmSelectedDeleteAmenitiesModal">
        <x-slot name="title">
            @lang('modules.settings.deleteAmenitiesManagement')
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmSelectedDeleteAmenitiesModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click='deleteSelected' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
         </x-slot>
    </x-confirmation-modal>

</div>

