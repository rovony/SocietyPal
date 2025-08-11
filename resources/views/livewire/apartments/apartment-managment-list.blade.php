<div>
    <div class="h-screen" wire:key="apartment-management">

        <div class="items-center justify-between block p-4 bg-white sm:flex dark:bg-gray-800 dark:border-gray-700">
            <div class="w-full mb-1">
                <div class="mb-4">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('menu.apartmentManagement')</h1>
                </div>
                <div class="items-center justify-between block sm:flex ">
                    <div class="flex items-center mb-4 sm:mb-0">
                        <form class="sm:pr-3" action="#" method="GET">
                            <label for="products-search" class="sr-only">Search</label>
                            <div class="relative w-48 mt-1 sm:w-64 xl:w-96">
                                <x-input id="name" class="block w-full mt-1 dark:text-gray-400" type="text" placeholder="{{ __('placeholders.searchApartments') }}" wire:model.live.debounce.500ms="search"  />
                            </div>
                        </form>
                        <x-secondary-button wire:click="$dispatch('showUtilityBillsFilters')" class="ml-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mr-1 bi bi-filter" viewBox="0 0 16 16">
                                <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
                            </svg> @lang('app.showFilter')
                        </x-secondary-button>
                    </div>
                    <div class="inline-flex items-center gap-4">
                        @if(user_can('Show Apartment'))
                            <a href="javascript:;" wire:click="$dispatch('exportApartments')" class="inline-flex items-center justify-center w-1/2 px-3 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-primary-300 sm:w-auto dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path></svg>
                                @lang('app.export')
                            </a>
                        @endif

                        @if(user_can('Create Apartment'))
                            <x-button type='button' wire:click="$set('showAddApartmentManagementModal', true)" >@lang('app.add')</x-button>
                        @endif
                    </div>

                </div>

            </div>

        </div>

        <livewire:apartments.apartment-managment :search='$search'  key='apartment-table-{{ microtime() }}' />

    </div>
    <x-right-modal wire:model.live="showAddApartmentManagementModal">
        <x-slot name="title">
            {{ __("modules.settings.addApartmentManagement") }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.addApartmentManagment')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showAddApartmentManagement', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>
</div>
