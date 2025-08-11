
<div class="flex w-full gap-2 p-4 bg-gray-50 dark:bg-gray-900 sm:gap-4">
    <div>
        <x-dropdown align="left" dropdownClasses="z-10">
            <x-slot name="trigger">
                <span class="inline-flex rounded-md">
                    <button type="button"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                        @lang('modules.assets.filterCategories')
                        <div class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500  rounded-md  dark:border-gray-900 ml-1 {{ count($filterCategories) == 0 ? 'hidden' : '' }}">{{ count($filterCategories) }}</div>

                        <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path clip- rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                        </svg>
                    </button>
                </span>
            </x-slot>

            <x-slot name="content">
                <div class="block px-4 py-2 text-sm font-medium text-gray-500" onclick="event.stopPropagation()">
                    <h6 class="text-sm font-medium text-gray-900 dark:text-white">
                        @lang('app.role')
                    </h6>
                </div>
                @foreach ($assetCategories as $role)
                    <x-dropdown-link wire:key='role-option-{{ $role->id }}' onclick="event.stopPropagation()">
                        <input id="role-{{ $role->id }}" type="checkbox" value="{{ $role->id }}" wire:model.live='filterCategories'
                            class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                        <label for="role-{{ $role->id }}" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $role->name }}
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
                        @lang('modules.settings.filterTower')
                        <div
                            class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-md dark:border-gray-900 ml-1 {{ count($filterTower) == 0 ? 'hidden' : '' }}">
                            {{ count($filterTower) }}
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
                <div class="block px-4 py-2 text-sm font-medium text-gray-500" onclick="event.stopPropagation()">
                    <h6 class="text-sm font-medium text-gray-900 dark:text-white">
                        @lang('modules.settings.societyTower')
                    </h6>
                </div>

                @foreach ($tower as $towers)
                    <x-dropdown-link wire:key="towers-option-{{ $towers->id }}" onclick="event.stopPropagation()">
                        <input id="towers-{{ $towers->id }}" type="checkbox"
                            value="{{ $towers->tower_name }}" wire:model.live="filterTower"
                            class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                        <label for="towers-{{ $towers->id }}"
                            class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $towers->tower_name }}
                        </label>
                    </x-dropdown-link>
                @endforeach

            </x-slot>
        </x-dropdown>
    </div>
    <div class="flex gap-2 x-4">

        <!-- Clear Filters Button -->
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
</div>





