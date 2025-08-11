<div class="relative" x-data="{ showNotifications: @entangle('showNotifications') }">
    <!-- Notification Button -->
    <button @click="showNotifications = !showNotifications" class="relative text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405C17.433 14.337 17 13.17 17 12V8a5 5 0 00-10 0v4c0 1.17-.433 2.337-1.595 3.595L4 17h5m6 0a3 3 0 01-6 0"></path>
        </svg>

        <!-- Red Dot for New Notifications -->
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 transform translate-x-1 -translate-y-1 w-2.5 h-2.5 bg-red-500 rounded-full"></span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div
        x-show="showNotifications"
        @click.away="showNotifications = false"
        x-transition
        x-cloak
        class="absolute right-0 mt-2 w-96 bg-white divide-y divide-gray-200 rounded-lg shadow-lg dark:bg-gray-800 dark:divide-gray-700"
    >
        <div class="px-4 py-3 text-gray-900 dark:text-white flex justify-between">
            <p class="text-lg font-semibold">@lang('modules.notifications.notifications')</p>
            @if($notifications->isNotEmpty())
                <button wire:click="markAsRead" class="font-semibold text-skin-base hover:text-skin-base/[.8] text-sm">@lang('modules.notifications.markAllAsRead')</button>
            @endif
        </div>
        <ul class="py-1 text-gray-800 dark:text-gray-300 max-h-60 overflow-y-auto
            [&::-webkit-scrollbar]:w-1.5
            [&::-webkit-scrollbar-track]:rounded-xl
            [&::-webkit-scrollbar-thumb]:rounded-xl
            [&::-webkit-scrollbar-track]:bg-gray-300
            dark:[&::-webkit-scrollbar-track]:bg-gray-600
            [&::-webkit-scrollbar-thumb]:bg-skin-base/[.5]
            hover:[&::-webkit-scrollbar-thumb]:bg-skin-base/[.8]">
            @forelse($notifications as $notification)
                <li class="px-4 py-3 flex items-start space-x-3 hover:bg-blue-100 dark:hover:bg-gray-700">
                    <a href="{{ $notification->data['url'] ?? '#' }}" class="w-full block">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $notification->data['message'] }}
                            </p>
                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                </li>
                <hr class="border-t border-gray-200 dark:border-gray-700">
            @empty
                <li class="px-4 py-2 text-gray-500 dark:text-gray-400 text-center">@lang('modules.notifications.noNotifications')</li>
            @endforelse
        </ul>
    </div>
</div>
