<div class="card">
    <div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-lg shadow-lg p-8 border dark:border-gray-700 w-full">
        <div class="space-y-4">

            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.assets.assetName') }}</strong>
                <span>{{ $assetName }}</span>
            </p>

            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.assets.maintenanceDate') }}</strong>
                <span>{{ $maintenance_date }}</span>
            </p>

            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.assets.maintenanceSchedule') }}</strong>
                <span>{{ ucfirst($schedule) }}</span>
            </p>

            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.assets.status') }}</strong>
                <span>
                    @if(strtolower($status) === 'completed')
                        <x-badge type="success">{{ ucfirst($status) }}</x-badge>
                    @elseif(strtolower($status) === 'pending')
                        <x-badge type="warning">{{ ucfirst($status) }}</x-badge>
                    @else
                        <x-badge type="secondary">{{ ucfirst($status) }}</x-badge>
                    @endif
                </span>
            </p>

            <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.assets.amount') }}</strong>
                <span>₹ {{ number_format($amount, 2) }}</span>
            </p>

            @if($serviceProviderName)
                <p class="flex justify-between border-b pb-2 border-gray-300 dark:border-gray-600">
                    <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.assets.serviceProvider') }}</strong>
                    <span>{{ $serviceProviderName }}</span>
                </p>
            @endif

            @if($notes)
                <p class="border-b pb-2 border-gray-300 dark:border-gray-600">
                    <strong class="text-gray-600 dark:text-gray-400 block mb-1">{{ __('modules.assets.notes') }}</strong>
                    <span class="block text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $notes }}</span>
                </p>
            @endif

        </div>
    </div>
</div>
