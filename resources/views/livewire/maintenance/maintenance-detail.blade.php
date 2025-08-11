@assets
<script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js" defer></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
@endassets
<div class="px-4 pt-6 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">

    <div class="mb-4 col-span-full xl:mb-2">
        <nav class="flex mb-5" aria-label="Breadcrumb">
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
                <a href="{{ route('maintenance.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">@lang('menu.maintenance')</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">@lang('modules.maintenance.maintenanceDetail')</span>
                </div>
            </li>
            </ol>
        </nav>
    </div>
    <div>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 sm:gap-4">

            <div class="flex flex-col p-3 transition bg-white border border-gray-200 rounded-lg shadow-sm group dark:border-gray-700 hover:shadow-md dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 rounded-md">
                        <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"></path>
                        </svg>
                    </div>
                    <div class="grow ms-5">
                        <h3 wire:loading.class.delay='opacity-50'
                            @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                            @lang('app.month') & @lang('app.year')
                        </h3>
                        <p @class(['text-sm dark:text-neutral-200'])>{{ ucFirst($maintenance->month) }}, {{ $maintenance->year }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col p-3 transition bg-white border border-gray-200 rounded-lg shadow-sm group dark:border-gray-700 hover:shadow-md dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 rounded-md">
                        <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5"></path>
                        </svg>
                    </div>
                    <div class="grow ms-5">
                        <h3 wire:loading.class.delay='opacity-50'
                            @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                            @lang('modules.maintenance.paidApartments')
                        </h3>
                        <p @class(['text-sm dark:text-neutral-200'])>{{ $paidApartments }}/{{ $totalApartments }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col p-3 transition bg-white border border-gray-200 rounded-lg shadow-sm group dark:border-gray-700 hover:shadow-md dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-stack" viewBox="0 0 16 16">
                            <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4"></path>
                            <path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2z"></path>
                        </svg>
                    </div>
                    <div class="grow ms-5">
                        <h3 wire:loading.class.delay='opacity-50'
                            @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                            @lang('modules.maintenance.totalAdditionalCost')
                        </h3>
                        <p @class(['text-sm dark:text-neutral-200'])>
                            @php
                                $totalCost = is_numeric($maintenance->total_additional_cost) ? $maintenance->total_additional_cost : 0;
                                $totalApartments = is_numeric($totalApartments) ? $totalApartments : 0;
                                $cost = $totalCost * $totalApartments;
                            @endphp
                            {{ currency_format($cost) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col p-3 transition bg-white border border-gray-200 rounded-lg shadow-sm group dark:border-gray-700 hover:shadow-md dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-stack" viewBox="0 0 16 16">
                            <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4"></path>
                            <path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2z"></path>
                        </svg>
                    </div>
                    <div class="grow ms-5">
                        <h3 wire:loading.class.delay='opacity-50'
                            @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                            @lang('modules.maintenance.totalAmount')
                        </h3>
                        <p @class(['text-sm dark:text-neutral-200'])>{{ currency_format($apartments->sum('cost')) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col p-3 transition bg-white border border-gray-200 rounded-lg shadow-sm group dark:border-gray-700 hover:shadow-md dark:bg-gray-800">
                <div class="flex items-center">
                    <div class="p-2 bg-gray-100 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500 dark:text-black" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 64 64" enable-background="new 0 0 64 64">
                            <rect x="4" y="10" width="56" height="50" fill="none" stroke="currentColor" stroke-width="2" /><rect x="4" y="10" width="56" height="10" fill="currentColor" /><line x1="4" y1="24" x2="60" y2="24" stroke="currentColor" stroke-width="2" /><line x1="18" y1="24" x2="18" y2="60" stroke="currentColor" stroke-width="2" /><line x1="32" y1="24" x2="32" y2="60" stroke="currentColor" stroke-width="2" /><line x1="46" y1="24" x2="46" y2="60" stroke="currentColor" stroke-width="2" />
                        </svg>
                    </div>
                    <div class="grow ms-5">
                        <h3 wire:loading.class.delay='opacity-50'
                            @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                            @lang('modules.maintenance.paymentDueDate')
                        </h3>
                        <p @class(['text-sm dark:text-neutral-200'])>{{ \Carbon\Carbon::parse($maintenance->payment_due_date)->format('d F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="items-center justify-between block mt-4 sm:flex">

            <div class="flex items-center mb-4 sm:mb-0">
                <x-secondary-button wire:click="$dispatch('showMaintenanceFilters')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mr-1 bi bi-filter" viewBox="0 0 16 16">
                        <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
                    </svg> @lang('app.showFilter')
                </x-secondary-button>
            </div>
            <div class="inline-flex items-center gap-4">
                <x-secondary-button wire:click="export">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path></svg>
                    @lang('app.export')
                </x-secondary-button>
                @if($maintenance->status == 'draft')
                    <x-button type='button' wire:click="publishMaintenance" class="flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M16.293 5.293a1 1 0 00-1.414 0L8 11.586 5.121 8.707a1 1 0 10-1.414 1.414l3.536 3.535a1 1 0 001.414 0l7.121-7.121a1 1 0 000-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                        @lang('app.publish')
                    </x-button>
                @endif
            </div>
        </div>
    </div>


<div class="mt-4">
    @if ($showFilters)
        @include('maintenance.maintenance-detail-filters')
    @endif
    <div class="flex items-center justify-end">
        @if($showActions)
            <x-dropdown dropdownClasses="z-50">
                <x-slot name="trigger">
                    <span class="inline-flex">
                        <button type="button"
                            class="inline-flex items-center justify-center p-2 mb-2 text-sm font-medium text-gray-500 transition duration-150 ease-in-out bg-white border border-gray-300 hover:text-gray-700 focus:outline-none hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                            @lang('app.action')
                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </span>
                </x-slot>
                <x-slot name="content">
                    @if($maintenance->status == 'published')
                        <x-dropdown-link href="javascript:;" wire:click="showSelectedPay">
                            <span class="inline-flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 2a8 8 0 100 16 8 8 0 000-16zm.75 5.75a.75.75 0 00-1.5 0v1h-1a.75.75 0 000 1.5h1v1h-1a.75.75 0 100 1.5h1v1a.75.75 0 001.5 0v-1h1a.75.75 0 000-1.5h-1v-1h1a.75.75 0 000-1.5h-1v-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                @lang('app.pay')
                            </span>
                        </x-dropdown-link>
                    @endif
                </x-slot>
            </x-dropdown>
        @endif
    </div>

    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.apartmentNumber')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.maintenance.totalCost')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.status')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.maintenance.paymentDate')
                                </th>
                                @if($maintenance->status == 'published')
                                    <th scope="col"
                                        class="p-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                        @lang('app.action')
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>
                            @forelse ($apartments as $item)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='notice-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                    <td class="p-4">
                                        <input type="checkbox" wire:model.live="selected" value="{{ $item->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </td>
                                    <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $item->apartment->apartment_number }}
                                    </td>
                                    <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                         {{ currency_format($item->cost) }}
                                    </td>
                                    <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="flex items-center">
                                            @if($item->paid_status == "paid")
                                                <x-badge type="success">{{ucFirst($item->paid_status)}}</x-badge>
                                            @elseif($item->paid_status == "payment_requested")
                                                <x-badge type="warning">{{ __('modules.maintenance.paymentRequested') }}</x-badge>
                                            @else
                                                <x-badge type="danger">{{ucFirst($item->paid_status)}}</x-badge>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                        @if($item->payment_date ==null)
                                        --
                                        @else
                                        {{ \Carbon\Carbon::parse($item->payment_date)->format('d F Y') ?? '--' }}
                                        @endif
                                    </td>
                                    @if ($maintenance->status == 'published')
                                        <td class="p-4 space-x-2 text-right whitespace-nowrap">
                                            @if ($item->paid_status == "paid")

                                                <a href="javascript:;" wire:click="downloadReceipt({{ $item->id }})"
                                                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25">
                                                        <span class="inline-flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                                                <path d="M12 16l4-4h-3V4h-2v8H8l4 4zM5 20h14v-2H5v2z" stroke="currentColor" stroke-width="1.5"/>
                                                            </svg>
                                                            @lang('app.download')
                                                        </span>
                                                    </a>

                                                @if (!empty($item->payment_proof))
                                                    <a href="{{ asset_url_local_s3($item->payment_proof) }}" target="_blank"
                                                    class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25">
                                                        <span class="inline-flex items-center justify-center">
                                                            <svg class="w-4 h-4 mr-1 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                <path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
                                                                <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                                            </svg>
                                                            @lang('app.paymentProof')
                                                        </span>
                                                    </a>

                                                @endif
                                            @else
                                                <a href="javascript:;" wire:click="showPay({{ $item->id }})"
                                                class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25">
                                                    <span class="inline-flex items-center justify-center">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm.75 5.75a.75.75 0 00-1.5 0v1h-1a.75.75 0 000 1.5h1v1h-1a.75.75 0 100 1.5h1v1a.75.75 0 001.5 0v-1h1a.75.75 0 000-1.5h-1v-1h1a.75.75 0 000-1.5h-1v-1z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        @lang('app.pay')
                                                    </span>
                                                </a>
                                            @endif
                                        </td>


                                        </td>
                                    @endif
                                </tr>
                                @empty
                                    <x-no-results :message="__('messages.noMaintenanceFound')" />
                                @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div wire:key='apartments-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center w-full mb-4 sm:mb-0">
            {{ $apartments->links() }}
        </div>
    </div>

    <x-dialog-modal wire:model.live="showPayModal">
        <x-slot name="title">
            {{ __("modules.maintenance.addPaymentDetail") }}
        </x-slot>
        <x-slot name="content">
            @if($apartment_maintenance)
                @livewire('forms.maintenance-pay',['apartment_maintenance' => $apartment_maintenance], key(str()->random(50)))
                @endif
            </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showPayModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showSelectedPayModal">
        <x-slot name="title">
            {{ __("modules.maintenance.addPaymentDetail") }}
        </x-slot>
        <x-slot name="content">
            <form wire:submit="paySelected">
                @csrf
                <div class="space-y-4">
                    <div class = "mt-3">
                        <x-label for="paymentDate" value="{{ __('modules.maintenance.paymentDate') }}" required="true" />
                        <x-datepicker class="block w-full mt-1" wire:model.live="paymentDate" id="paymentDate" autofocus="false" autocomplete="off" placeholder="{{ __('modules.maintenance.choosepaymentDate') }}" :maxDate="true"/>
                        <x-input-error for="paymentDate" class="mt-2" />
                    </div>

                    <div class="flex justify-end w-full pb-4 mt-6 space-x-4">
                        <x-button class="ml-3">@lang('app.save')</x-button>
                    </div>
                </div>
            </form>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showSelectedPayModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
</div>
