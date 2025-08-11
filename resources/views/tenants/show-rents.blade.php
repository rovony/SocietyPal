<div class="flex flex-col mb-12">
    <div class="overflow-x-auto ">
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                #
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.apartmentNumber')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.rent.rentFor')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.tenant.rentAmount')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.status')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.rent.paymentDate')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                @lang('app.action')
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>

                        @forelse ($tenant->rents as $rent)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='rent-{{ $rent->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $loop->index+1 }}
                            </td>

                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $rent->apartment->apartment_number }}
                            </td>
                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                <a href="javascript:;" wire:click="showRentDetail({{ $rent->id }})" class="text-base hover:underline dark:text-black-400">
                                    {{ ucfirst($rent->rent_for_month) . ' ' .$rent->rent_for_year ?? '--'}}
                                </a>
                            </td>
                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                {{ currency_format($rent->rent_amount) ?? '--' }}
                            </td>
                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                @if($rent->status == "paid")
                                    <x-badge type="success">{{ ucfirst($rent->status) }}</x-badge>
                                @else
                                    <x-badge type="danger">{{ ucfirst($rent->status) }}</x-badge>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $rent->payment_date ?? '--' }}
                            </td>
                            <td class="p-4 space-x-2 whitespace-nowrap text-right">
                                @if($rent->status == "unpaid")
                                <x-secondary-button wire:click="showPay({{ $rent->id }})">
                                        <span class="inline-flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M10 2a8 8 0 100 16 8 8 0 000-16zm.75 5.75a.75.75 0 00-1.5 0v1h-1a.75.75 0 000 1.5h1v1h-1a.75.75 0 100 1.5h1v1a.75.75 0 001.5 0v-1h1a.75.75 0 000-1.5h-1v-1h1a.75.75 0 000-1.5h-1v-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            @lang('app.pay')
                                        </span>
                                    </x-secondary-button>
                                @endif
                                @if($rent->status == "paid")
                                <x-secondary-button wire:click="downloadReceipt({{ $rent->id }})">
                                    <span class="inline-flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h.01"/>                                                         </svg>
                                        @lang('app.download')
                                    </span>
                                </x-secondary-button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                            <td class="dark:text-gray-400" colspan="12">
                                <div class="py-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                                    @lang('messages.noRentFound')
                                </div>
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <x-dialog-modal wire:model.live="showPayModal">
        <x-slot name="title">
            {{ __("modules.utilityBills.addPaymentDetail") }}
        </x-slot>
        <x-slot name="content">
            @if($payRent)
                @livewire('forms.rent-pay',['rent' => $payRent], key(str()->random(50)))
            @endif
            </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showPayModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
    <x-right-modal wire:model.live="showRentDetailModal">
        <x-slot name="title">
            {{ __("modules.rent.viewRentDetails") }}
        </x-slot>

        <x-slot name="content">
            @if ($seletedRent)
            @livewire('forms.showRent', ['rent' => $seletedRent], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showTenantDetailModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>
</div>