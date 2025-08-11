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
                <a href="{{ route('apartments.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">@lang('menu.apartmentManagement')</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">@lang('modules.settings.apartmentDetails')</span>
                </div>
            </li>
            </ol>
        </nav>
    </div>
    <div class="items-center justify-between block p-4 bg-white sm:flex dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                @lang('modules.settings.apartmentDetails')</h1>

        </div>
    </div>
    <div class="grid gap-3 px-5 mt-3 sm:grid-cols-2 lg:grid-cols-3 sm:gap-5">

        <div class="p-3 group flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:shadow-md transition bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="w-4 h-4 text-gray-500 dark:text-black" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="1.5" d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="grow ms-5">
                    <h3 wire:loading.class.delay='opacity-50'
                        @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                        @lang('modules.settings.totalPaidRent')
                    </h3>
                    <p @class(['text-sm dark:text-neutral-200'])>{{ currency_format($totalPaidRent) }}</p>
                </div>
            </div>
        </div>

        <div class="p-3 group flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:shadow-md transition bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="w-4 h-4 text-gray-500 dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M8 2h8a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" stroke-width="1.5"/>
                        <path d="M12 8h4" stroke-width="1.5"/>
                        <path d="M12 12h4" stroke-width="1.5"/>
                        <path d="M8 16h8" stroke-width="1.5"/>
                        <path d="M8 20h8" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="grow ms-5">
                    <h3 wire:loading.class.delay='opacity-50'
                        @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                        @lang('modules.settings.totalPaidUtilityBillAmount')
                    </h3>
                    <p @class(['text-sm dark:text-neutral-200'])>{{ currency_format($totalPaidAmount) }}</p>
                </div>
            </div>
        </div>

        <div class="p-3 group flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:shadow-md transition bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="w-4 h-4 text-gray-500 dark:text-black" viewBox="0 0 30 30">
                        <path d="M6,9.3L3.9,5.8l1.4-1.4l3.5,2.1v1.4l3.6,3.6c0,0.1,0,0.2,0,0.3L11.1,13L7.4,9.3H6z M21,17.8c-0.3,0-0.5,0-0.8,0  c0,0,0,0,0,0c-0.7,0-1.3-0.1-1.9-0.2l-2.1,2.4l4.7,5.3c1.1,1.2,3,1.3,4.1,0.1c1.2-1.2,1.1-3-0.1-4.1L21,17.8z M24.4,14  c1.6-1.6,2.1-4,1.5-6.1c-0.1-0.4-0.6-0.5-0.8-0.2l-3.5,3.5l-2.8-2.8l3.5-3.5c0.3-0.3,0.2-0.7-0.2-0.8C20,3.4,17.6,3.9,16,5.6  c-1.8,1.8-2.2,4.6-1.2,6.8l-10,8.9c-1.2,1.1-1.3,3-0.1,4.1l0,0c1.2,1.2,3,1.1,4.1-0.1l8.9-10C19.9,16.3,22.6,15.9,24.4,14z"/>
                    </svg>
                </div>
                <div class="grow ms-5">
                    <h3 wire:loading.class.delay='opacity-50'
                        @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                        @lang('modules.settings.totalPaidMaintenanceCost')
                    </h3>
                    <p @class(['text-sm dark:text-neutral-200'])>{{currency_format($totalPaidMaintenance) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-3 px-5 mt-3 sm:grid-cols-2 lg:grid-cols-3 sm:gap-5">

        <div class="p-3 group flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:shadow-md transition bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="w-4 h-4 text-gray-500 dark:text-black" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="1.5" d="M3 21h18M4 18h16M6 10v8m4-8v8m4-8v8m4-8v8M4 9.5v-.955a1 1 0 0 1 .458-.84l7-4.52a1 1 0 0 1 1.084 0l7 4.52a1 1 0 0 1 .458.84V9.5a.5.5 0 0 1-.5.5h-15a.5.5 0 0 1-.5-.5Z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="grow ms-5">
                    <h3 wire:loading.class.delay='opacity-50'
                        @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                        @lang('modules.settings.totalUnpaidRent')
                    </h3>
                    <p @class(['text-sm dark:text-neutral-200'])>{{currency_format($totalUnpaidRent) }}</p>
                </div>
            </div>
        </div>

        <div class="p-3 group flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:shadow-md transition bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" class="w-4 h-4 text-gray-500 dark:text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M8 2h8a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" stroke-width="1.5"/>
                        <path d="M12 8h4" stroke-width="1.5"/>
                        <path d="M12 12h4" stroke-width="1.5"/>
                        <path d="M8 16h8" stroke-width="1.5"/>
                        <path d="M8 20h8" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="grow ms-5">
                    <h3 wire:loading.class.delay='opacity-50'
                        @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                        @lang('modules.settings.totalUnpaidUtilityBillAmount')
                    </h3>
                    <p @class(['text-sm dark:text-neutral-200'])>{{currency_format($totalUnpaidAmount) }}</p>
                </div>
            </div>
        </div>

        <div class="p-3 group flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:shadow-md transition bg-white dark:bg-gray-800">
            <div class="flex items-center">
                <div class="p-2 bg-gray-100 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="w-4 h-4 text-gray-500 dark:text-black" viewBox="0 0 30 30">
                        <path d="M6,9.3L3.9,5.8l1.4-1.4l3.5,2.1v1.4l3.6,3.6c0,0.1,0,0.2,0,0.3L11.1,13L7.4,9.3H6z M21,17.8c-0.3,0-0.5,0-0.8,0  c0,0,0,0,0,0c-0.7,0-1.3-0.1-1.9-0.2l-2.1,2.4l4.7,5.3c1.1,1.2,3,1.3,4.1,0.1c1.2-1.2,1.1-3-0.1-4.1L21,17.8z M24.4,14  c1.6-1.6,2.1-4,1.5-6.1c-0.1-0.4-0.6-0.5-0.8-0.2l-3.5,3.5l-2.8-2.8l3.5-3.5c0.3-0.3,0.2-0.7-0.2-0.8C20,3.4,17.6,3.9,16,5.6  c-1.8,1.8-2.2,4.6-1.2,6.8l-10,8.9c-1.2,1.1-1.3,3-0.1,4.1l0,0c1.2,1.2,3,1.1,4.1-0.1l8.9-10C19.9,16.3,22.6,15.9,24.4,14z"/>
                    </svg>
                </div>
                <div class="grow ms-5">
                    <h3 wire:loading.class.delay='opacity-50'
                        @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                        @lang('modules.settings.totalUnpaidMaintenanceCost')
                    </h3>
                    <p @class(['text-sm dark:text-neutral-200'])>{{currency_format($totalUnpaidMaintenance) }}</p>
                </div>
            </div>
        </div>
    </div>

        <div class="grid m-5 lg:grid-cols-2 lg:gap-5 dark:bg-gray-900">
            <div
                class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex items-center space-x-4 sm:space-x-4">
                    <div class="p-2 bg-gray-100 rounded-md">
                        <svg class="w-10 h-10 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">@lang('modules.settings.apartmentNumber'): {{ $apartment->apartment_number }}
                            @if ($apartment->status == 'occupied')
                                <x-badge type="success">{{ __('app.'.$apartment->status) }}</x-badge>
                            @elseif($apartment->status == 'available_for_rent')
                                <x-badge type="warning">{{ __('app.'.$apartment->status) }}</x-badge>
                            @elseif($apartment->status == 'not_sold')
                                <x-badge type="danger">{{ __('app.'.$apartment->status) }}</x-badge>
                            @elseif($apartment->status == 'rented')
                                <x-badge type="theme">{{ __('app.'.$apartment->status) }}</x-badge>
                            @endif
                        </h1>

                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        @lang('modules.settings.societyTower'): {!! nl2br($apartment->towers->tower_name) !!}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        @lang('modules.settings.floorName'): {!! nl2br($apartment->floors->floor_name) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <div class="flex items-center space-x-4 sm:space-x-4">
                <div>
                    <h5 class="mb-2 text-lg font-medium text-gray-900 dark:text-white">
                        @lang('modules.settings.parkingDetails')
                    </h5>
                    <dl class="flex flex-col gap-1 sm:flex-row">
                        <dt class="min-w-40 text-sm text-gray-500 dark:text-neutral-500">
                            @lang('modules.settings.societyParkingCode')
                        </dt>
                        <dd class="text-sm text-gray-800 dark:text-neutral-200">
                            {{ $apartment->parkingCodes->pluck('parking_code')->join(', ') ?: '--' }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <div class="grid mx-5 mb-5 lg:grid-cols-2 lg:gap-5">

        <div
        class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
            <!-- List -->
            <h5 class="items-center mb-6 text-lg font-medium text-gray-900 dark:text-white">@lang('modules.user.ownerDetails')</h5>

            <div class="space-y-3">
                <dl class="flex flex-col gap-1 sm:flex-row">
                    <dt class="min-w-40">
                        <span class="text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.ownerName')</span>
                    </dt>
                    <dd>
                        <ul>
                            <li
                                class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                {{ $apartment->user->name ?? '--' }}
                            </li>
                        </ul>
                    </dd>
                </dl>
                <dl class="flex flex-col gap-1 sm:flex-row">
                    <dt class="min-w-40">
                        <span class="text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.societyPhoneNumber')</span>
                    </dt>
                    <dd>
                        <ul>
                            <li
                                class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                {{ $apartment->user->phone_number ?? '--' }}
                            </li>
                        </ul>
                    </dd>
                </dl>

                <dl class="flex flex-col gap-1 sm:flex-row">
                    <dt class="min-w-40">
                        <span class="text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.societyEmailAddress')</span>
                    </dt>
                    <dd>
                        <ul>
                            <li
                                class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                {{ $apartment->user->email ?? '--' }}
                            </li>
                        </ul>
                    </dd>
                </dl>
            </div>
            <!-- End List -->
        </div>
            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <h5 class="mb-6 text-lg font-medium text-gray-900 dark:text-white">@lang('modules.tenant.tenantDetails')</h5>
                <div class="space-y-3">
                    @forelse ($apartment->tenants as $tenant)
                        <dl class="flex flex-col gap-1 sm:flex-row">
                            <dt class="min-w-40">
                                <span class="text-sm text-gray-500 dark:text-neutral-500">@lang('modules.tenant.name')</span>
                            </dt>
                            <dd>
                                <ul>
                                    <li class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                        {{ $tenant->user->name ?? '--' }}
                                    </li>
                                </ul>
                            </dd>
                        </dl>
                        <dl class="flex flex-col gap-1 sm:flex-row">
                            <dt class="min-w-40">
                                <span class="text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.societyPhoneNumber')</span>
                            </dt>
                            <dd>
                                <ul>
                                    <li class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                        {{ $tenant->user->phone_number ?? '--' }}
                                    </li>
                                </ul>
                            </dd>
                        </dl>
                        <dl class="flex flex-col gap-1 sm:flex-row">
                            <dt class="min-w-40">
                                <span class="text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.societyEmailAddress')</span>
                            </dt>
                            <dd>
                                <ul>
                                    <li class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                        {{ $tenant->user->email ?? '--' }}
                                    </li>
                                </ul>
                            </dd>
                        </dl>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-neutral-500">@lang('messages.noTenantFound')</p>
                    @endforelse
                </div>
            </div>
    </div>
</div>
