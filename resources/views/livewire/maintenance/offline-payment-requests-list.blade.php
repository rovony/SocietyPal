<div>
    @assets
        <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js" defer></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
    @endassets

    <div class="items-center justify-between block p-4 bg-white sm:flex dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <div class="mb-4">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('menu.offlineRequest')</h1>
            </div>
        </div>
    </div>
    <div class="flex flex-col">
        <div>
            <div class="min-w-full align-middle">
                <div class="relative h-screen overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('#')
                                </th>

                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.apartmentNumber')
                                </th>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.maintenance.totalCost')
                                </th>

                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.billing.monthAndYear')
                                </th>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.status')
                                </th>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                    @lang('app.action')
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700"
                            wire:key='invoice-list-{{ microtime() }}'>
                            @forelse ($offlinePaymentRequest as $request)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700"
                                    wire:key='request-{{ $request->id . rand(1111, 9999) . microtime() }}'
                                    wire:loading.class.delay='opacity-10'>
                                    <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $loop->index + 1 }}
                                    </td>

                                    <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $request->apartment->apartment_number }}
                                    </td>
                                    <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ currency_format($request->cost) }}
                                    </td>
                                    <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ ucfirst($request->maintenanceManagement->month) }} {{ $request->maintenanceManagement->year }}
                                    </td>

                                    <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        @if ($request->paid_status == 'paid')
                                            <span
                                                class="bg-green-100 uppercase text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">@lang('app.verified')</span>
                                        @elseif ($request->paid_status == 'payment_requested')
                                            <span
                                                class="bg-yellow-100 uppercase text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">@lang('app.pending')</span>
                                        @else
                                            <span
                                                class="bg-red-100 uppercase text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">@lang('app.rejected')</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right dark:text-white">

                                        <x-dropdown align="right">
                                            <x-slot name="trigger">
                                                <button type="button"
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 uppercase transition duration-150 ease-in-out border rounded-md dark:border-gray-400 hover:text-gray-700 dark:hover:text-gray-400 focus:outline-none">
                                                    <span>@lang('app.action')</span>
                                                    <svg class="w-2.5 h-2.5 ms-1" height="24" width="24"
                                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 10 6">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                    </svg>
                                                </button>
                                            </x-slot>
                                            <x-slot name="content">

                                                <x-dropdown-link
                                                    wire:click="confirmChangePlan({{ $request->id }}, 'paid')"
                                                    class="text-green-500 dark:text-green-400">
                                                    @lang('app.accept')
                                                </x-dropdown-link>

                                                <x-dropdown-link
                                                    wire:click="confirmChangePlan({{ $request->id }}, 'unpaid')"
                                                    class="text-red-600 dark:text-red-400">
                                                    @lang('app.decline')
                                                </x-dropdown-link>

                                                <x-dropdown-link wire:click="ViewRequest('{{ $request->id }}')">
                                                    @lang('app.view')
                                                </x-dropdown-link>

                                            </x-slot>
                                        </x-dropdown>

                                    </td>
                                </tr>
                            @empty
                                <x-no-results :message="__('messages.noOfflinePaymentRequestFound')" />
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

   
    <x-right-modal wire:model.live="showViewRequestModal">
        <x-slot name="title">
            {{ __('modules.billing.viewPaymentMethod') }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 gap-6 p-6 rounded-md sm:grid-cols-2 bg-gray-50 dark:bg-gray-700">
                @if ($showViewRequestModal && $selectViewRequest)
                    <!-- Society Name -->


                    <!-- Package -->
                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.apartmentNumber')</span>
                        <span
                            class="text-sm text-gray-800 dark:text-neutral-200">{{ $selectViewRequest->apartment->apartment_number }}</span>
                    </div>

                    <!-- Amount -->

                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-neutral-500">@lang('modules.billing.amount')</span>
                        <span class="text-sm text-gray-800 dark:text-neutral-200">
                            {{ currency_format($selectViewRequest->cost) }}</span>
                    </div>


                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-neutral-500">@lang('app.status')</span>
                        <span class="text-sm">
                            @if ($selectViewRequest->paid_status == 'paid')
                                <span
                                    class="bg-green-100 uppercase text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">@lang('app.verified')</span>
                            @elseif ($selectViewRequest->paid_status == 'payment_requested')
                                <span
                                    class="bg-yellow-100 uppercase text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">@lang('app.pending')</span>
                            @else
                                <span
                                    class="bg-red-100 uppercase text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">@lang('app.rejected')</span>
                            @endif

                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-sm text-gray-500 dark:text-neutral-500">@lang('modules.billing.monthAndYear')</span>
                        <span
                            class="text-sm text-gray-800 dark:text-neutral-200">{{ ucfirst($selectViewRequest->maintenanceManagement->month) }}
                            {{ $selectViewRequest->maintenanceManagement->year }}</span>
                    </div>

                    @if ($selectViewRequest->payment_proof)
                        <div class="flex flex-col">
                            <span class="text-sm text-gray-500 dark:text-neutral-500">@lang('modules.billing.paymentProof')</span>


                            <div class="relative w-full group">
                                <div
                                    class="relative w-64 h-56 p-1 overflow-hidden rounded bg-gray-50 ring-gray-300 ring-1 dark:ring-gray-500">
                                    @if (Str::endsWith($selectViewRequest->payment_proof_url, '.pdf'))
                                        <a href="{{ $selectViewRequest->payment_proof }}" target="_blank">
                                            <div class="flex items-center justify-center w-full h-full bg-gray-200">
                                                <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M14.5 2h-5A2.5 2.5 0 007 4.5v15A2.5 2.5 0 009.5 22h5a2.5 2.5 0 002.5-2.5v-15A2.5 2.5 0 0014.5 2zm-5 1h5a1.5 1.5 0 011.5 1.5v15a1.5 1.5 0 01-1.5 1.5h-5A1.5 1.5 0 018 18.5v-15A1.5 1.5 0 019.5 3zm3.5 8h-3v1h1.5v3H10v1h3v-5zM13.5 5h-3v1h1.5v2H10v1h3.5V5z" />
                                                </svg>
                                                <p class="mt-2 text-sm text-gray-600">View PDF</p>
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ $selectViewRequest->payment_proof_url }}" target="_blank">
                                            <img class="object-cover w-full h-full"
                                                src="{{ $selectViewRequest->payment_proof_url }}" alt="Bill Proof" />
                                        </a>
                                    @endif
                                    <div
                                        class="absolute inset-0 flex items-end justify-end p-2 transition-opacity duration-300 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100">
                                        <a href="{{ $selectViewRequest->payment_proof_url }}" target="_blank"
                                            class="px-4 py-2 mr-2 text-white rounded shadow hover:bg-gray-800">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M12 4.5C6.75 4.5 3 12 3 12s3.75 7.5 9 7.5 9-7.5 9-7.5-3.75-7.5-9-7.5zM12 16.5c-2.25 0-4.5-1.5-4.5-4.5s2.25-4.5 4.5-4.5 4.5 1.5 4.5 4.5-2.25 4.5-4.5 4.5zM12 10.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                                            </svg>
                                        </a>
                                        <a href="{{ $selectViewRequest->payment_proof_url }}" download
                                            target="_blank" wire:loading.attr="disabled"
                                            class="px-4 py-2 text-white rounded shadow hover:bg-gray-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 24 24" class="w-4 h-4">
                                                <path d="M12 16l4-4h-3V4h-2v8H8l4 4zM5 20h14v-2H5v2z"
                                                    stroke="currentColor" stroke-width="1.5" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showViewRequestModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>


</div>
