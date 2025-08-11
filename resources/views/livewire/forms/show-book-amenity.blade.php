<div>
    <div class="grid grid-cols-2 mb-6">
        <div>
            <x-user :user="$amenity->user" />
        </div>
    </div>

    <div class="grid grid-cols-3 mb-6">
        <div>
            <x-label value="{{__('modules.bookAmenity.amenityName')}}"/>
            <p class="mt-2 text-gray-600 dark:text-gray-200">{{ $amenity->amenity->amenities_name }}</p>
        </div>

        <div>
            <x-label class="mb-2" value="{{ __('modules.bookAmenity.bookingDate') }}" />
            <p class="mt-2 text-gray-600 dark:text-gray-200">{{ \Carbon\Carbon::parse($amenity->booking_date)->format('d F Y') ?? '--' }}</p>
        </div>

        <div>
            <x-label value="{{ __('modules.bookAmenity.slotTime') }}" />
            <p class="mt-2 text-gray-600 dark:text-gray-200">{{ $amenity->amenity->slot_time }} Min</p>
        </div>
    </div>

    <div class="mt-6">
        <x-label value="{{ __('modules.bookAmenity.bookingTimePersons') }}" class="mb-2"/>
        <table class="flex-1 min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600 border dark:border-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th scope="col"
                        class="p-2 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                        @lang('modules.bookAmenity.bookingTime')
                    </th>
                    <th scope="col"
                        class="p-2 text-xs font-medium text-center text-gray-500 uppercase dark:text-gray-400">
                        @lang('modules.bookAmenity.numberOfpersons')
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='menu-item-list-{{ microtime() }}'>

                @foreach ($bookings as $booking)
                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700"  wire:loading.class.delay='opacity-10'>
                    <td class="p-2 text-xs font-medium text-left text-gray-500 dark:text-gray-400">
                        {{ \Carbon\Carbon::createFromFormat('H:i:s', $booking->booking_time)->format('h:i A') }}
                    </td>
                    <td class="p-2 text-xs font-medium text-center text-gray-500 dark:text-gray-400">
                        @if ($booking->persons == 0 || $booking->persons == 'null')
                            <span class="dark:text-gray-400">--</span>
                        @else
                            {{ $booking->persons }}
                        @endif
                    </td>
                </tr>
                @endforeach

            </tbody>
        </table>
    </div>

    <div class="mb-6 mt-4 flex justify-center">
        <a class="min-h-[40px] w-24 rounded-xl bg-white hover:bg-gray-50 text-gray-700 border p-2 inline-flex items-center justify-center gap-1 transition-colors dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
            href="{{ route('amenities.print', $amenity->id) }}" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor"
                viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                <path
                    d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
            </svg>
            <span class="text-sm font-medium">@lang('app.print')</span>
        </a>
    </div>


    <div class="flex w-full pb-4 mt-6 space-x-4">
        <x-button-cancel  wire:click="$dispatch('hideBookAmenityDetail')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
    </div>
</div>
