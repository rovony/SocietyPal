<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Header Section -->
    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
        <div>
            <h3 class="text-base font-normal text-gray-500 dark:text-gray-400 flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                @lang('modules.dashboard.pendingOfflineRequests')
            </h3>
        </div>
        
        @if($totalPending > 0)
            <div class="bg-yellow-50 dark:bg-yellow-900/30 px-3 py-1.5 rounded-lg">
                <span class="text-yellow-800 dark:text-yellow-300 font-medium text-sm">
                    {{ $totalPending }} {{ Str::plural('Request', $totalPending) }}
                </span>
            </div>
        @endif
    </div>

    <!-- Content Section -->
    @if($pendingRequests->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 px-6">
            <p class="text-gray-500 dark:text-gray-400">
                @lang('messages.noOfflinePaymentRequestFound')
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
                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('app.id')
                        </th>
                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.billing.society')
                        </th>
                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('modules.billing.packageDetails')
                        </th>
                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-400">
                            @lang('app.status')
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @foreach($pendingRequests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                #{{ $request->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                <div class="flex items-center">
                                    <span>{{ $request->society->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                <div>
                                    <span class="font-medium">{{ $request->package->package_name }}</span>
                                    <span class="ml-1 text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                        {{ ucfirst($request->package->package_type->value) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-yellow-100 uppercase text-yellow-800 text-xs font-medium px-2.5 py-1 rounded-full dark:bg-yellow-900 dark:text-yellow-300">
                                    @lang('app.pending')
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($totalPending > 5)
            <div class="py-4 px-6 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('superadmin.offline-plan-request') }}" 
                   class="inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors">
                    @lang('modules.dashboard.viewAll') {{ $totalPending }} @lang('modules.dashboard.requests')
                    <svg class="w-4 h-4 ml-1.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        @endif
    @endif
</div>