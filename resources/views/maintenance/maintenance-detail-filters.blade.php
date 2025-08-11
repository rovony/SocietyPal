<div class="w-full p-4 flex flex-wrap gap-2 sm:gap-4  bg-gray-50 dark:bg-gray-900">

    <div>
        <x-dropdown align="left" dropdownClasses="z-10">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button type="button"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        @lang('modules.maintenance.filterApartments')
                        <div
                            class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-md dark:border-gray-900 ml-1 {{ count($filterApartments) == 0 ? 'hidden' : '' }}">
                            {{ count($filterApartments) }}
                        </div>
                        <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                <!-- Apartment Numbers -->
                <div class="block px-4 py-2 text-sm font-medium text-gray-500" onclick="event.stopPropagation()">
                    <h6 class="text-sm font-medium text-gray-900 dark:text-white">
                        @lang('modules.settings.apartmentNumber')
                    </h6>
                </div>

                @foreach ($apartmentList as $apartment)
                    <x-dropdown-link wire:key="apartment-option-{{ $apartment->id }}" onclick="event.stopPropagation()">
                        <input id="apartment-{{ $apartment->id }}" type="checkbox"
                            value="{{ $apartment->apartment_number }}" wire:model.live="filterApartments"
                            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-gray-600 focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                        <label for="apartment-{{ $apartment->id }}"
                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $apartment->apartment_number }}
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
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        @lang('modules.maintenance.filterStatus')
                        <div class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500  rounded-md  dark:border-gray-900 ml-1 {{ count($filterStatus) == 0 ? 'hidden' : '' }}">{{ count($filterStatus) }}</div>

                        <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                <!-- Account Management -->
                <div class="block px-4 py-2 text-sm font-medium text-gray-500" onclick="event.stopPropagation()">
                    <h6 class="text-sm font-medium text-gray-900 dark:text-white">
                        @lang('app.status')
                    </h6>
                </div>

                <x-dropdown-link wire:key='status-paid' onclick="event.stopPropagation()">
                    <input id="status-paid" type="checkbox" value="paid" wire:model.live='filterStatus'
                        class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-gray-600 focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                    <label for="status-paid" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                        Paid
                    </label>
                </x-dropdown-link>
                <x-dropdown-link wire:key='status-unpaid' onclick="event.stopPropagation()">
                    <input id="status-unpaid" type="checkbox" value="unpaid" wire:model.live='filterStatus'
                        class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-gray-600 focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                    <label for="status-unpaid" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                        Unpaid
                    </label>
                </x-dropdown-link>

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

    <div>
        <x-secondary-button wire:click="$toggle('showFilters')">@lang('app.hideFilter')</x-secondary-button>
    </div>

</div>
