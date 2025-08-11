<div class="px-4 pt-2 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">
    @if ($showFilters)
        @include('maintenance.filters-apartment')
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
                                    @lang('app.year')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.month')
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
                                    @lang('modules.maintenance.paymentDueDate')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.maintenance.paymentDate')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.status')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>

                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>
                            @forelse ($maintenances as $item)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='notice-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                <td class="p-4">
                                    <input type="checkbox" wire:model.live="selected" value="{{ $item->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                </td>
                                <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->maintenanceManagement->year }}
                                </td>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ ucwords(strtolower($item->maintenanceManagement->month)) }}
                                </td>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ ($item->apartment->apartment_number) }}
                                </td>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ currency_format($item->cost) }}
                                </td>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ \Carbon\Carbon::parse($item->maintenanceManagement->payment_due_date)->format('d F Y') ?? '--' }}
                                </td>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($item->payment_date ==null)
                                    --
                                    @else
                                    {{ \Carbon\Carbon::parse($item->payment_date)->format('d F Y') ?? '--' }}
                                    @endif
                                </td>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
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
                                <td class="p-4 space-x-2 text-right whitespace-nowrap">
                                    @if($item->paid_status == "paid")
                                    <a href="javascript:;" wire:click="downloadReceipt({{ $item->id }})"
                                        class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25">
                                            <span class="inline-flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                                    <path d="M12 16l4-4h-3V4h-2v8H8l4 4zM5 20h14v-2H5v2z" stroke="currentColor" stroke-width="1.5"/>
                                                </svg>
                                                @lang('app.download')
                                            </span>
                                        </a>
                                        @if(!empty($item->payment_proof))
                                            <a href="{{ asset_url_local_s3($item->payment_proof) }}" target="_blank" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25">
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
                                        <a href="javascript:;" wire:click="showPay({{ $item->id }})" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-gray-700 uppercase transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25">
                                            <span class="inline-flex items-center justify-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd"
                                                        d="M10 2a8 8 0 100 16 8 8 0 000-16zm.75 5.75a.75.75 0 00-1.5 0v1h-1a.75.75 0 000 1.5h1v1h-1a.75.75 0 100 1.5h1v1a.75.75 0 001.5 0v-1h1a.75.75 0 000-1.5h-1v-1h1a.75.75 0 000-1.5h-1v-1z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                @lang('app.pay')
                                            </span>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                                <td class="p-4 space-x-6 dark:text-gray-400" colspan="12">
                                    @lang('messages.noMaintenanceFound')
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div wire:key='maintenance-apartment-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-4 sm:mb-0">
            {{ $maintenances->links() }}
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
                        <x-datepicker class="block w-full mt-1" wire:model.live="paymentDate" id="paymentDate" autofocus="false" autocomplete="off" placeholder="{{ __('modules.maintenance.choosepaymentDate') }}" />
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

