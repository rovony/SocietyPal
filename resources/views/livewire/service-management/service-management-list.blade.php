<div>
    <div class="h-screen" wire:key="service-management">
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 dark:border-gray-700">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                @lang('menu.serviceManagement')
            </h1>
            <div class="inline-flex items-center gap-4">
                @if(user_can('Create Service Provider'))
                    <x-button type='button' wire:click="$set('showAddServiceManagementModal', true)">
                        @lang('modules.serviceManagement.add')
                    </x-button>
                @endif
            </div>
        </div>
        <div class="flex flex-col my-4 px-4">
            <!-- Service Icons Section -->
            <div class="flex flex-wrap gap-4">
                @forelse ($serviceTypes as $item)
                <a
                    @class([
                        'group flex flex-col items-center border shadow-sm rounded-lg hover:shadow-md transition dark:bg-gray-700 dark:border-gray-600 w-20 h-20',
                        'bg-skin-base' => ($serviceId == $item->id),
                        'bg-white' => ($serviceId != $item->id),
                        'dark:bg-skin-base' => ($serviceId == $item->id)
                    ])
                    wire:key='service-{{ $item->id . microtime() }}'
                    wire:click='showServiceItems({{ $item->id }})'
                    href="javascript:;">
                    <div class="p-1 flex flex-col items-center justify-center w-full h-full">
                        <button id="service-toggle-{{ $item->id }}"
                                type="button"
                                @class([
                                    'p-1 rounded-md w-10 h-10 flex items-center justify-center mb-1',
                                    'bg-gray-100' => ($serviceId != $item->id),
                                    'bg-white' => ($serviceId == $item->id)
                                ])>
                            <span @class([
                                'text-gray-600' => ($serviceId != $item->id),
                                'text-skin-base' => ($serviceId == $item->id)
                            ])>
                                {!! $item->icon !!}
                            </span>
                        </button>
                        <span @class([
                            'text-xs font-medium truncate w-16 text-center',
                            'text-gray-700 dark:text-gray-200' => ($serviceId != $item->id),
                            'text-white' => ($serviceId == $item->id)
                        ])>
                            {{ Str::limit($item->name, 12) }}
                        </span>
                    </div>
                </a>
                @empty
                    <div class="w-full text-center text-gray-500 dark:text-gray-400">
                        @lang('messages.noServiceTypeFound')
                    </div>
                @endforelse
            </div>

            <!-- Search Bar and Export Button Section -->
            <div class="flex flex-none flex-wrap items-center justify-between mt-6">
                <!-- Search Bar -->
                <div class="flex items-center mb-4 sm:mb-0">
                    <form class="sm:pr-3" action="#" method="GET">
                        <label for="products-search" class="sr-only">Search</label>
                        <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                            <x-input id="name" class="block w-full mt-1 dark:text-gray-400" type="text" placeholder="{{ __('placeholders.searchService') }}" wire:model.live.debounce.500ms="search"  />
                        </div>
                    </form>
                    <x-secondary-button wire:click="$dispatch('showServiceFilters')" class="ml-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mr-1 bi bi-filter" viewBox="0 0 16 16">
                            <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
                        </svg> @lang('app.showFilter')
                    </x-secondary-button>
                </div>

                <!-- Export Button -->
                @if(user_can('Show Service Provider'))
                    <a href="javascript:;" wire:click="$dispatch('exportService')"
                        class="ml-4 inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                        </svg>
                        @lang('app.export')
                    </a>
                @endif
            </div>

            <!-- Service Management Table -->
            <div class="mt-4">
                <livewire:service-management.service-management-table :search='$search' :serviceId='$serviceId' key='utility-table-{{ microtime() }}' />
            </div>
        </div>


    <x-right-modal wire:model.live="showAddServiceManagementModal">
        <x-slot name="title">
            {{ __("modules.serviceManagement.addServiceManagement") }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.addServiceProviderManagement')
        </x-slot>
    </x-right-modal>
</div>
