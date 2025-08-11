<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    @lang('modules.dashboard.todayAmenitiesBooking')
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ now()->format('l, d M Y') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Bookings List with Custom Scrollbar -->
    <div class="max-h-[320px] overflow-y-auto overflow-x-hidden p-4
                [&::-webkit-scrollbar]:w-1.5
                [&::-webkit-scrollbar-track]:rounded-lg
                [&::-webkit-scrollbar-thumb]:rounded-lg
                [&::-webkit-scrollbar-track]:bg-gray-100
                dark:[&::-webkit-scrollbar-track]:bg-gray-700
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-thumb]:bg-gray-500
                hover:[&::-webkit-scrollbar-thumb]:bg-gray-400
                dark:hover:[&::-webkit-scrollbar-thumb]:bg-gray-400">
        
        <div class="space-y-3">
            @forelse ($todayBooking as $item)
                <div class="group">
                    <a href="javascript:;" 
                       wire:click="showBookAmenityDetail({{ $item->id }})"
                       class="block relative overflow-hidden transition-all duration-300 ease-in-out hover:shadow-lg">
                        
                        <!-- Content -->
                        <div class="relative p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 
                                  dark:border-gray-700 hover:border-purple-400 dark:hover:border-purple-800
                                  transition-all duration-300 ease-in-out hover:shadow-lg">
                            
                            <div class="flex items-center justify-between">
                                <!-- Left: Amenity Info -->
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-gradient-to-br from-purple-50 to-fuchsia-50 
                                                dark:from-purple-900/40 dark:to-fuchsia-900/40 rounded-lg">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item->amenity->amenities_name }}
                                        </h3>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $item->user->name }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Right: Time Info and View -->
                                <div class="flex items-center space-x-4">
                                    <!-- Booking Time -->
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-purple-500 dark:text-purple-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $item->booking_time)->format('h:i A') }}
                                        </span>
                                    </div>

                                    <!-- Duration -->
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-purple-500 dark:text-purple-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm text-gray-600 dark:text-gray-300">
                                            {{ $item->amenity->slot_time }} min
                                        </span>
                                    </div>

                                    <!-- View Details -->
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <span class="text-purple-600 dark:text-purple-300 text-sm flex items-center">
                                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" 
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full 
                                bg-gradient-to-br from-purple-50 to-fuchsia-50 
                                dark:from-purple-900/40 dark:to-fuchsia-900/40 mb-4">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-300">
                        @lang('messages.noBookingFound')
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    <x-right-modal wire:model.live="showBookAmenityDetailModal">
        <x-slot name="title">
            {{ __("modules.bookAmenity.viewBookAmenity") }}
        </x-slot>

        <x-slot name="content">
            @if ($showAmenity)
                @livewire('forms.showBookAmenity', ['amenity' => $showAmenity], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showBookAmenityDetailModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>
</div>