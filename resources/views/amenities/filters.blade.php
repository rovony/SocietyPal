<div class="flex w-full gap-2 p-4 bg-gray-50 dark:bg-gray-900 sm:gap-4">
    <div>
        <x-dropdown align="left" dropdownClasses="z-10">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button type="button"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                        @lang('modules.maintenance.filterStatus')
                        <div class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-md dark:border-gray-900 ml-1 {{ count($filterStatuses) == 0 ? 'hidden' : '' }}">
                            {{ count($filterStatuses) }}
                        </div>
                        <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                <div class="block px-4 py-2 text-sm font-medium text-gray-500" onclick="event.stopPropagation()">
                    <h6 class="text-sm font-medium text-gray-900 dark:text-white">
                        @lang('app.status')
                    </h6>
                </div>

                @foreach (['not_available' => __('modules.settings.notAvailable'), 'available' => __('modules.settings.available')] as $key => $label)
                    <x-dropdown-link wire:key="status-option-{{ $key }}" onclick="event.stopPropagation()">
                        <input id="status-{{ $key }}" type="checkbox" value="{{ $key }}" wire:model.live="filterStatuses"
                            class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                        <label for="status-{{ $key }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $label }}
                        </label>
                    </x-dropdown-link>
                @endforeach
            </x-slot>
        </x-dropdown>
    </div>
    <div>
        <x-dropdown align="left" dropdownClasses="z-10">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button type="button"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                        @lang('modules.settings.filterBookingRequired')
                        <div class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-md dark:border-gray-900 ml-1 {{ count($filterBookingRequired) == 0 ? 'hidden' : '' }}">
                            {{ count($filterBookingRequired) }}
                        </div>
                        <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                <div class="block px-4 py-2 text-sm font-medium text-gray-500" onclick="event.stopPropagation()">
                    <h6 class="text-sm font-medium text-gray-900 dark:text-white">
                        @lang('modules.settings.societyAmenitiesBooking')
                    </h6>
                </div>

                @foreach ([0 => __('app.no'), 1 => __('app.yes')] as $key => $label)
                    <x-dropdown-link wire:key="booking-option-{{ $key }}" onclick="event.stopPropagation()">
                        <input id="booking-{{ $key }}" type="checkbox" value="{{ $key }}" wire:model.live="filterBookingRequired"
                            class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                        <label for="booking-{{ $key }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $label }}
                        </label>
                    </x-dropdown-link>
                @endforeach
            </x-slot>
        </x-dropdown>
    </div>

    @if ($clearFilterButton)
        <div wire:key='filter-btn-{{ microtime() }}'>
            <x-danger-button wire:click='clearFilters'>
                <svg aria-hidden="true" class="w-5 h-3 -ml-1 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
                @lang('app.clearFilter')
            </x-danger-button>
        </div>
    @endif
    <x-secondary-button wire:click="$toggle('showFilters')">@lang('app.hideFilter')</x-secondary-button>

</div>
