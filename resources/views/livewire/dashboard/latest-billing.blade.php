<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Header Section -->
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <div>
            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400 flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                @lang('modules.dashboard.recentSubscriptionCharges')
            </h3>
        </div>
    </div>

    <!-- Content Section -->
    @if($latestInvoices->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 px-6">
            <p class="text-gray-500 dark:text-gray-400">
                @lang('messages.noInvoiceFound')
            </p>
        </div>
    @else
        <div class="max-h-64 overflow-y-auto overflow-x-hidden p-4
                [&::-webkit-scrollbar]:w-1.5
                [&::-webkit-scrollbar-track]:rounded-lg
                [&::-webkit-scrollbar-thumb]:rounded-lg
                [&::-webkit-scrollbar-track]:bg-gray-100
                dark:[&::-webkit-scrollbar-track]:bg-gray-700
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-thumb]:bg-gray-500
                hover:[&::-webkit-scrollbar-thumb]:bg-gray-400
                dark:hover:[&::-webkit-scrollbar-thumb]:bg-gray-400">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.billing.society')
                        </th>
                        <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.billing.packageDetails')
                        </th>
                        <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.billing.billingCycle')
                        </th>
                        <th scope="col" class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.billing.amount')
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @foreach($latestInvoices as $invoice)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                <a href="{{ route('superadmin.society.show', $invoice->society->slug) }}" class="underline underline-offset-1 font-medium" wire:navigate>{{ $invoice->society->name }}</a>
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $invoice->package->package_name }}<br>
                                @if ($invoice->package->package_type->value == 'trial')
                                <span class="bg-amber-500 text-white inline-flex text-xs font-medium items-center px-1 rounded gap-x-0.5 dark:bg-amber-600 border border-amber-500">
                                    <svg class="w-2.5 h-2.5 me-0.5 text-current" aria-hidden="true" height="16px" width="16px" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M10 0a10 10 0 1 0 10 10A10.01 10.01 0 0 0 10 0m3.982 13.982a1 1 0 0 1-1.414 0l-3.274-3.274A1 1 0 0 1 9 10V6a1 1 0 0 1 2 0v3.586l2.982 2.982a1 1 0 0 1 0 1.414"/></svg>
                                    @lang('modules.package.trial')
                                </span>
                                @elseif($invoice->package->package_type->value == 'lifetime')
                                <span class="bg-indigo-500 text-white inline-flex text-xs font-medium items-center px-1 rounded gap-x-0.5 dark:bg-indigo-600 border border-indigo-500">
                                    <svg class="w-3 h-3 text-current" width="24" height="24" fill="currentColor" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g stroke-width="0"/><g stroke-linecap="round" stroke-linejoin="round"/><path d="m31.89 14.55-4-8A1 1 0 0 0 27 6H5a1 1 0 0 0-.89.55l-4 8a.3.3 0 0 1 0 .09 2 2 0 0 0 0 .26S0 15 0 15v.05a1.3 1.3 0 0 0 .06.28s0 .05 0 .08a.8.8 0 0 0 .18.27l15 16a1 1 0 0 0 1.46 0l15-16a1 1 0 0 0 .19-1.13M16 8.89 19.2 14h-6.4Zm-5.08 4.34L7 8h7.2ZM17.8 8H25l-3.92 5.23Zm1.84 8L16 27.65 12.36 16Zm-5.89 11.14L3.31 16h7Zm8-11.14h7l-10.5 11.14Zm7.65-2H23l3.83-5.11ZM5.17 8.89 9 14H2.62ZM16 4a1 1 0 0 0 1-1V1a1 1 0 0 0-2 0v2a1 1 0 0 0 1 1m-5.71-.29a1 1 0 0 0 1.42 0 1 1 0 0 0 0-1.42l-1-1a1 1 0 0 0-1.42 1.42ZM21 4a1 1 0 0 0 .71-.29l1-1a1 1 0 1 0-1.42-1.42l-1 1a1 1 0 0 0 0 1.42A1 1 0 0 0 21 4" data-name="6. Diamond"/></svg>
                                    @lang('modules.package.lifetime')
                                </span>
                                @endif

                            </div>

                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ ucfirst($invoice->package_type) }}
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $invoice->total ? $invoice->currency->currency_symbol . $invoice->total : '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif
</div>