<div>
    <div class="p-4 bg-white col-span-full xl:mb-2 xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">
        <nav class="flex items-center" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                @lang('menu.dashboard')
                </a>
            </li>
            <li>
                <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <a href="{{ route('floors.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">@lang('menu.floorManagment')</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">@lang('modules.settings.floorDetails')</span>
                </div>
            </li>
            </ol>
        </nav>
    </div>
    <div class="items-center justify-between block p-4 bg-white sm:flex dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                @lang('modules.settings.floorDetails')</h1>

        </div>
    </div>
    <div class="grid gap-3 px-5 mt-3 sm:grid-cols-2 lg:grid-cols-3 sm:gap-5">

        <div class="p-3 group flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:shadow-md transition bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" class="w-4 h-4 text-gray-500 dark:text-black" viewBox="0 0 16 16">
                        <path d="M8 .5L15 7v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V7l7-6.5z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 9v6h10V9H3zm5 6V9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="grow ms-5">
                    <h3 wire:loading.class.delay='opacity-50'
                        @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                        @lang('modules.settings.apartment')
                    </h3>
                    <p @class(['text-sm dark:text-neutral-200'])>{{ $floors->apartmentManagement->count() }}</p>
                </div>
            </div>
        </div>

        <div class="p-3 group flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:shadow-md transition bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" class="w-4 h-4 text-gray-500 dark:text-black" viewBox="0 0 16 16">
                        <path d="M8 .5L15 7v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V7l7-6.5z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 9v6h10V9H3zm5 6V9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="grow ms-5">
                    <h3 wire:loading.class.delay='opacity-50'
                        @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                        @lang('modules.settings.totalUnsoldApartment')
                    </h3>
                    <p @class(['text-sm dark:text-neutral-200'])>{{ $unsoldApartmentsCount }}</p>
                </div>
            </div>
        </div>
        <div class="p-3 group flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:shadow-md transition bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" class="w-4 h-4 text-gray-500 dark:text-black" viewBox="0 0 16 16">
                        <path d="M8 .5L15 7v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V7l7-6.5z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 9v6h10V9H3zm5 6V9" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="grow ms-5">
                    <h3 wire:loading.class.delay='opacity-50'
                        @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                        @lang('modules.settings.totalOccupiedApartment')
                    </h3>
                    <p @class(['text-sm dark:text-neutral-200'])>{{ $occupiedApartmentsCount }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="grid m-5 lg:grid-cols-1 lg:gap-5 dark:bg-gray-900">
        <div
            class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="flex items-center space-x-4 sm:space-x-4">
                <div class="p-2 bg-gray-100 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="w-10 h-10 text-gray-500 dark:text-black" viewBox="0 0 16 16">
                        <path d="M1 1h14v14H1V1zm1 1v2h2V2H2zm0 3v2h2V5H2zm0 3v2h2V8H2zm0 3v2h2v-2H2zm3-9v2h2V2H5zm0 3v2h2V5H5zm0 3v2h2V8H5zm0 3v2h2v-2H5zm3-9v2h2V2H8zm0 3v2h2V5H8zm0 3v2h2V8H8zm0 3v2h2v-2H8zm3-9v2h2V2h-2zm0 3v2h2V5h-2zm0 3v2h2V8h-2zm0 3v2h2v-2h-2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">@lang('modules.settings.floorName') {{ $floors->floor_name }}
                    </h1>
                </div>
            </div>
        </div>

    </div>
    <div class="grid m-5 lg:grid-cols-1 lg:gap-5 dark:bg-gray-900">
        <div class="relative overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.settings.societyApartmentNumber')
                        </th>
                        <th scope="col"
                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.settings.societyApartmentArea')
                        </th>
                        <th scope="col"
                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.settings.societyApartmentStatus')
                        </th>
                        <th scope="col"
                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.settings.apartmentType')
                        </th>
                        <th scope="col"
                            class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.settings.societyTower')
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>

                    @forelse ($floors->apartmentManagement as $item)
                    <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='member-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                        <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                            <a class="text-base font-semibold hover:underline dark:text-black-400" href="{{ route('apartments.show' , $item->id)}}">{{ $item->apartment_number }}</a>
                        </td>
                        <td class="p-4 text-basetext-gray-900 whitespace-nowrap dark:text-white">
                            {{ $item->apartment_area .' '. $item->apartment_area_unit }}
                        </td>
                        <td class="p-4 text-basetext-gray-900 whitespace-nowrap dark:text-white">
                            @if($item->status === 'occupied')
                                <x-badge type="success">{{  __('app.'.$item->status) }}</x-badge>
                            @endif
                            @if($item->status === 'available_for_rent')
                                <x-badge type="warning">{{  __('app.'.$item->status) }}</x-badge>
                            @endif
                            @if($item->status === 'not_sold')
                                <x-badge type="danger">{{  __('app.'.$item->status) }}</x-badge>
                            @endif
                            @if($item->status === 'rented')
                                <x-badge type="theme">{{  __('app.'.$item->status) }}</x-badge>
                            @endif
                        </td>
                        <td class="p-4 text-basetext-gray-900 whitespace-nowrap dark:text-white">
                            {{ $item->apartments->apartment_type }}
                        </td>
                        <td class="p-4 text-basetext-gray-900 whitespace-nowrap dark:text-white">
                            {{ $item->towers->tower_name }}
                        </td>
                    </tr>
                    @empty
                        <x-no-results :message="__('messages.noApartmentManagementFound')" />
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>
</div>

