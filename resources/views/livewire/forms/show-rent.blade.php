<div class="bg-white dark:bg-gray-800 space-y-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <div class="mt-2">
                <x-user :user="$tenant->user" :tenantId="$tenant->id" />
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('modules.rent.rentFor')</p>
            <p class="text-base font-medium text-gray-700 dark:text-gray-200 mt-1">
                {{ ucfirst($rent->rent_for_month) . ' ' . $rent->rent_for_year ?? '--' }}
            </p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('modules.settings.apartmentNumber')</p>
            <p class="text-base font-medium text-gray-700 dark:text-gray-200 mt-1">
                {{ $rent->apartment->apartment_number }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('modules.tenant.rentAmount')</p>
            <p class="text-base font-medium text-gray-700 dark:text-gray-200 mt-1">
                {{ currency_format($rent_amount) ?? __('modules.rent.noAmount') }}
            </p>
        </div>
         <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('modules.settings.status')</p>
            <div class="mt-2">
                @if($status === 'paid')
                    <x-badge type="success">{{ __('modules.rent.paid') }}</x-badge>
                @else
                    <x-badge type="danger">{{ __('modules.rent.unpaid') }}</x-badge>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($payment_date)
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">@lang('modules.rent.paymentDate')</p>
                <p class="text-base font-medium text-gray-700 dark:text-gray-200 mt-1">
                    {{ \Carbon\Carbon::parse($payment_date)->format('d F Y') }}
                </p>
            </div>
        @endif
        @if($paymentProof)
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">@lang('modules.utilityBills.paymentProofShow')</p>
                <div class="relative w-full md:w-24 h-24 rounded-xl overflow-hidden shadow group">
                    <a href="{{ $paymentProof }}" target="_blank">
                        <img src="{{ $paymentProof }}" class="object-cover w-full h-full" alt="Payment Proof">
                    </a>
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
