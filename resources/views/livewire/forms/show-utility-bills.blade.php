<div class="card">
    <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg shadow-lg p-8 border dark:border-gray-700 w-full">
        <div class="space-y-4">

            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.settings.apartmentNumber')</strong>
                <span>{{ $apartmentId }}</span>
            </p>

            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.settings.billType')</strong>
                <span>{{ $billTypeId }}</span>
            </p>

            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.settings.status')</strong>
                <span @class(['text-xs font-medium px-2.5 py-0.5
                        rounded',
                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'=> ($status == 'paid'),
                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'=> ($status == 'unpaid')
                        ])>
                        {{ __('app.' . $status) }}
                </span>
            </p>

            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.utilityBills.billDate')</strong>
                <span>{{ \Carbon\Carbon::parse($billDate)->format('d F Y') }}</span>
            </p>

            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.utilityBills.billAmount')</strong>
                <span>{{ currency_format($billAmount) }}</span>
            </p>

            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">@lang('modules.utilityBills.billDueDate')</strong>
                <span>{{ \Carbon\Carbon::parse($billDueDate)->format('d F Y') }}</span>
            </p>

            @if($status == 'paid')
                <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                    <strong class="text-gray-600 dark:text-gray-400">@lang('modules.utilityBills.billPaymentDate')</strong>
                    <span>{{ \Carbon\Carbon::parse($billPaymentDate)->format('d F Y') }}</span>
                </p>
            @endif

            <div class="flex flex-wrap gap-4">
                @if($billProof)
                    <div class="group">
                        <x-label class="mb-2" value="{{ __('modules.utilityBills.billProofShow') }}" />
                        <div class="relative w-24 h-24 p-1 overflow-hidden rounded bg-gray-50 ring-gray-300 ring-1 dark:ring-gray-500">
                            @if(Str::endsWith($billProof, '.pdf'))
                            <a href="{{ $billProof }}" target="_blank">
                                <div class="flex items-center justify-center w-full h-full bg-gray-200">
                                    <svg class="w-12 h-12 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M14.5 2h-5A2.5 2.5 0 007 4.5v15A2.5 2.5 0 009.5 22h5a2.5 2.5 0 002.5-2.5v-15A2.5 2.5 0 0014.5 2zm-5 1h5a1.5 1.5 0 011.5 1.5v15a1.5 1.5 0 01-1.5 1.5h-5A1.5 1.5 0 018 18.5v-15A1.5 1.5 0 019.5 3zm3.5 8h-3v1h1.5v3H10v1h3v-5zM13.5 5h-3v1h1.5v2H10v1h3.5V5z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">View PDF</p>
                                </div>
                            </a>
                            @else
                                <a href="{{ $billProof }}" target="_blank">
                                    <img class="object-cover w-full h-full" src="{{ $billProof }}" alt="Bill Proof" />
                                </a>
                            @endif
                            <div class="absolute inset-0 flex justify-end items-end p-3 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition">
                                <a href="{{ $billProof }}" target="_blank" class="bg-white dark:bg-gray-700 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 shadow">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 4.5C6.75 4.5 3 12 3 12s3.75 7.5 9 7.5 9-7.5 9-7.5-3.75-7.5-9-7.5zM12 16.5c-2.25 0-4.5-1.5-4.5-4.5s2.25-4.5 4.5-4.5 4.5 1.5 4.5 4.5-2.25 4.5-4.5 4.5zM12 10.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                                    </svg>                  
                                </a>
                                <a href="{{ $billProof }}" download class="ml-2 bg-white dark:bg-gray-700 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 shadow">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                        <path d="M12 16l4-4h-3V4h-2v8H8l4 4zM5 20h14v-2H5v2z" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                @if($paymentProof)
                    <div class="group">
                        <x-label class="mb-2" value="{{ __('modules.utilityBills.paymentProofShow') }}" />
                        <div class="relative w-24 h-24 p-1 overflow-hidden rounded bg-gray-50 ring-gray-300 ring-1 dark:ring-gray-500">
                            @if(Str::endsWith($paymentProof, '.pdf'))
                            <a href="{{ $paymentProof }}" target="_blank">
                                <div class="flex items-center justify-center w-full h-full bg-gray-200">
                                    <svg class="w-12 h-12 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M14.5 2h-5A2.5 2.5 0 007 4.5v15A2.5 2.5 0 009.5 22h5a2.5 2.5 0 002.5-2.5v-15A2.5 2.5 0 0014.5 2zm-5 1h5a1.5 1.5 0 011.5 1.5v15a1.5 1.5 0 01-1.5 1.5h-5A1.5 1.5 0 018 18.5v-15A1.5 1.5 0 019.5 3zm3.5 8h-3v1h1.5v3H10v1h3v-5zM13.5 5h-3v1h1.5v2H10v1h3.5V5z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">View PDF</p>
                                </div>
                            </a>
                            @else
                                <a href="{{ $paymentProof }}" target="_blank">
                                    <img class="object-cover w-full h-full" src="{{ $paymentProof }}" alt="Payment Proof" />
                                </a>
                            @endif
                            <div class="absolute inset-0 flex justify-end items-end p-3 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition">
                                <a href="{{ $paymentProof }}" target="_blank" class="bg-white dark:bg-gray-700 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 shadow">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 4.5C6.75 4.5 3 12 3 12s3.75 7.5 9 7.5 9-7.5 9-7.5-3.75-7.5-9-7.5zM12 16.5c-2.25 0-4.5-1.5-4.5-4.5s2.25-4.5 4.5-4.5 4.5 1.5 4.5 4.5-2.25 4.5-4.5 4.5zM12 10.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" />
                                    </svg>                  
                                </a>
                                <a href="{{ $paymentProof }}" download class="ml-2 bg-white dark:bg-gray-700 p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600 shadow">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-4 h-4">
                                        <path d="M12 16l4-4h-3V4h-2v8H8l4 4zM5 20h14v-2H5v2z" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                    </div>
                @endif
            </div>
        </div>
        <div class="mb-6 mt-4 flex justify-center">
            <a class="min-h-[40px] w-24 rounded-xl bg-white hover:bg-gray-50 text-gray-700 border p-2 inline-flex items-center justify-center gap-1 transition-colors dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
                href="{{ route('utilitybills.print', $billAmountId) }}" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="currentColor"
                    viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1" />
                    <path
                        d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1" />
                </svg>
                <span class="text-sm font-medium">@lang('app.print')</span>
            </a>
        </div>

         <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button-cancel  wire:click="$dispatch('hideUtilityBillModal')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </div>
</div>
