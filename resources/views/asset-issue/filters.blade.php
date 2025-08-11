
<div class="flex w-full gap-2 p-4 bg-gray-50 dark:bg-gray-900 sm:gap-4">
<div>
    <x-dropdown align="left" dropdownClasses="z-10">
        <x-slot name="trigger">
            <span class="inline-flex rounded-md">
                <button type="button"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                    @lang('modules.maintenance.filterStatus')
                    <div class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-md dark:border-gray-900 ml-1 {{ count($filterStatus) == 0 ? 'hidden' : '' }}">
                        {{ count($filterStatus) }}
                    </div>
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
                    @lang('modules.assets.status')
                </h6>
            </div>

            <x-dropdown-link wire:key='status-process' onclick="event.stopPropagation()">
                <input id="status-process" type="checkbox" value="process" wire:model.live='filterStatus'
                    class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                <label for="status-process" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @lang('modules.assets.process')
                </label>
            </x-dropdown-link>
            <x-dropdown-link wire:key='status-pending' onclick="event.stopPropagation()">
                <input id="status-pending" type="checkbox" value="pending" wire:model.live='filterStatus'
                    class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                <label for="status-pending" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @lang('modules.assets.pending')
                </label>
            </x-dropdown-link>
            <x-dropdown-link wire:key='status-resolved' onclick="event.stopPropagation()">
                <input id="status-resolved" type="checkbox" value="resolved" wire:model.live='filterStatus'
                    class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                <label for="status-resolved" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @lang('modules.assets.resolved')
                </label>
            </x-dropdown-link>

        </x-slot>
    </x-dropdown>
</div>

<div>
    <x-dropdown align="left" dropdownClasses="z-10">
        <x-slot name="trigger">
            <span class="inline-flex rounded-md">
                <button type="button"
                    class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                    @lang('modules.assets.filterPriority')
                    <div class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-red-500 rounded-md dark:border-gray-900 ml-1 {{ count($filterPriority) == 0 ? 'hidden' : '' }}">
                        {{ count($filterPriority) }}
                    </div>
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
                    @lang('modules.assets.priority')
                </h6>
            </div>

            <x-dropdown-link wire:key='priority-low' onclick="event.stopPropagation()">
                <input id="priority-low" type="checkbox" value="low" wire:model.live='filterPriority'
                    class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                <label for="priority-low" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @lang('modules.assets.low')
                </label>
            </x-dropdown-link>
            <x-dropdown-link wire:key='priority-medium' onclick="event.stopPropagation()">
                <input id="priority-medium" type="checkbox" value="medium" wire:model.live='filterPriority'
                    class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                <label for="priority-medium" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @lang('modules.assets.medium')
                </label>
            </x-dropdown-link>
            <x-dropdown-link wire:key='priority-high' onclick="event.stopPropagation()">
                <input id="priority-high" type="checkbox" value="high" wire:model.live='filterPriority'
                    class="w-4 h-4 text-gray-600 bg-gray-100 border-gray-300 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" />
                <label for="priority-high" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @lang('modules.assets.high')
                </label>
            </x-dropdown-link>

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





