<div class="px-4 pt-2 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">
    @assets
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
    @endassets
    @if ($showFilters)
        @include('common_area_bills.filters')
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
                    @if(user_can('Delete Common Area Bills'))
                        <x-dropdown-link href="javascript:;" wire:click="showSelectedDelete">
                            <span class="inline-flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                @lang('app.delete')
                            </span>
                        </x-dropdown-link>
                    @endif
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
        <div>
            <div class="min-w-full align-middle md:inline-block">
                <div class="h-screen overflow-x-auto shadow md:h-auto md:overflow-x-visible">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.billType')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.utilityBills.billDate')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.utilityBills.billAmount')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.status')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.utilityBills.billPaymentDate')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>

                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>
                            @forelse ($commonAreaBillData as $item)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='member-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                    <input type="checkbox" wire:model.live="selected" value="{{ $item->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                </td>
                                <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                    <a href="javascript:;"
                                        wire:click="showCommonAreaBill({{ $item->id }})"
                                        class="text-base font-semibold hover:underline dark:text-black-400">
                                        {{ $item->billType->name ?? '--' }}
                                    </a>
                                </td>

                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ \Carbon\Carbon::parse($item->bill_date)->format('d F Y') }}
                                </td>

                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ currency_format($item->bill_amount) }}
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($item->status === 'paid')
                                        <x-badge type="success">{{ __('app.' . $item->status) }}</x-badge>
                                    @else
                                        <x-badge type="danger">{{ __('app.' . $item->status) }}</x-badge>
                                    @endif
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($item->status == "paid" )
                                    {{ \Carbon\Carbon::parse($item->bill_payment_date)->format('d F Y') }}
                                    @else
                                        <p> -- </p>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right">
                                    <x-dropdown align="right">
                                        <x-slot name="trigger">
                                            <button type="button"
                                                 class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 uppercase transition duration-150 ease-in-out border rounded-md dark:border-gray-400 hover:text-gray-700 dark:hover:text-gray-400 focus:outline-none">
                                                <span>@lang('app.action')</span>
                                                <svg class="w-2.5 h-2.5 ms-1" height="24" width="24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                     fill="none" viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                </svg>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">

                                            <x-dropdown-link wire:click="showCommonAreaBill({{ $item->id }})" class="text-yellow-500 dark:text-yellow-400">
                                                @lang('app.view')
                                            </x-dropdown-link>

                                            @if(user_can('Update Common Area Bills'))
                                                <x-dropdown-link wire:click="showEditCommonAreaBill({{ $item->id }})" wire:key='member-edit-{{ $item->id . microtime() }}'>
                                                    @lang('app.update')
                                                </x-dropdown-link>
                                            @endif

                                            @if($item->status == "paid")
                                                <x-dropdown-link wire:click="downloadReceipt({{ $item->id }})" class="text-red-500 dark:text-red-400">
                                                    @lang('app.download')
                                                </x-dropdown-link>

                                                <x-dropdown-link href="{{ route('commonAreaBill.print', $item->id) }}" target="_blank" class="text-purple-500 dark:text-purple-400">
                                                    @lang('modules.utilityBills.print')
                                                </x-dropdown-link>
                                            @endif

                                            @if($item->status == "unpaid")

                                                @if(user_can('Delete Common Area Bills'))
                                                    <x-dropdown-link wire:click="showDeleteCommonAreaBill({{ $item->id }})" class="text-red-500 dark:text-red-400">
                                                        @lang('app.delete')
                                                    </x-dropdown-link>
                                                @endif
                                                <x-dropdown-link wire:click="showPay({{ $item->id }})" class="text-green-500 dark:text-green-400">
                                                    @lang('app.pay')
                                                </x-dropdown-link>
                                            @endif

                                            @if ($item->bill_proof)
                                                <x-dropdown-link href="{{ $item->bill_proof_url }}" download target="_blank" class="text-yellow-500 dark:text-yellow-400">
                                                    @lang('app.billDownload')
                                                </x-dropdown-link>
                                            @endif

                                        </x-slot>
                                    </x-dropdown>
                                </td>
                            </tr>
                            @empty
                                <x-no-results :message="__('messages.noCommonAreaBillsFound')" />
                            @endforelse

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div wire:key='common-bill-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-4 sm:mb-0">
            {{ $commonAreaBillData->links() }}
        </div>
    </div>
    <x-right-modal wire:model.live="showEditCommonAreaBillModal">
        <x-slot name="title">
            {{ __("modules.commonAreaBill.showEditCommonAreaBill") }}
        </x-slot>

        <x-slot name="content">
            @if ($commonAreaBill)
            @livewire('forms.editCommon-area-bill', ['commonAreaBill' => $commonAreaBill], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditCommonAreaBillModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-right-modal wire:model.live="showCommonAreaBillModal">
        <x-slot name="title">
            {{ __("modules.commonAreaBill.showCommonAreaBill") }}
        </x-slot>

        <x-slot name="content">
            @if ($commonAreaBill)
                @livewire('forms.showCommon-area-bill', ['commonAreaBill' => $commonAreaBill], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showCommonAreaBillModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-right-modal wire:model.live="showCommonAreaBill">
        <x-slot name="title">
            {{ __("modules.commonAreaBill.showCommonAreaBill") }}
        </x-slot>

        <x-slot name="content">
            @if ($commonAreaBill)
            @livewire('forms.showCommonAreaBill', ['commonAreaBill' => $commonAreaBill], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showVisitorModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model="confirmDeleteCommonAreaBillModal">
        <x-slot name="title">
            @lang('modules.commonAreaBill.deleteCommonAreaBill')
        </x-slot>

        <x-slot name="content">
            @lang('modules.commonAreaBill.deleteutilityBillsMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteCommonAreaBillModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($commonAreaBill)
            <x-danger-button class="ml-3" wire:click='deleteCommonAreaBill({{ $commonAreaBill->id }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

    <x-dialog-modal wire:model.live="showPayModal">
        <x-slot name="title">
            {{ __("modules.commonAreaBill.addPaymentDetail") }}
        </x-slot>
        <x-slot name="content">
            @if ($commonAreaBill)
                @livewire('forms.Common-area-bill-pay',['commonAreaBill' => $commonAreaBill], key(str()->random(50)))
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
            {{ __("modules.commonAreaBill.addPaymentDetail") }}
        </x-slot>
        <x-slot name="content">
            <form wire:submit="paySelected">
                @csrf
                <div class="space-y-4">
                    <div class = "mt-3">
                        <x-label for="billPaymentDate" value="{{ __('modules.utilityBills.billPaymentDate') }}" required="true" />
                        <x-datepicker class="block w-full mt-1" wire:model.live="billPaymentDate" id="billPaymentDate" autocomplete="off" placeholder="{{ __('modules.utilityBills.chooseBillPaymentDate') }}" :maxDate="true"/>
                        <x-input-error for="billPaymentDate" class="mt-2" />
                    </div>

                    <div class="flex justify-end w-full pb-4 space-x-4 mt-9">
                        <x-button>@lang('app.save')</x-button>
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

    <x-confirmation-modal wire:model="confirmSelectedDeleteCommonAreaBillModal">
        <x-slot name="title">
            @lang('modules.commonAreaBill.deleteCommonAreaBill')
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
            <div class="flex items-center p-4 mt-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div>
                  <span class="font-medium"><strong>@lang('modules.commonAreaBill.cautionMessageForSelectedDelete')</strong></span>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmSelectedDeleteCommonAreaBillModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click='deleteSelected' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
         </x-slot>
    </x-confirmation-modal>

</div>

