<div class="max-w-4xl mx-auto mt-10 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header Section -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col space-y-4">
            <!-- Title -->
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ $notice->title }}
            </h2>

            <!-- Meta Information -->
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 space-x-4">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $notice->created_at->timezone(timezone())->translatedFormat('D, d M Y') }}
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $notice->created_at->timezone(timezone())->translatedFormat('h:i A') }}
                </div>
            </div>

            <!-- Roles Badges -->
            @if (user_can('Show Notice Board'))
                <div class="flex flex-wrap gap-2">
                    @foreach ($notice->roles as $role)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                   bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-800
                                   dark:from-indigo-900/50 dark:to-purple-900/50 dark:text-indigo-200">
                            {{ $role->display_name }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Description Section -->
    <div class="p-6">
        <div class="prose dark:prose-invert max-w-none">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ __('modules.notice.description') }}
            </h3>
            <div class="text-gray-600 dark:text-gray-300 leading-relaxed">
                {{ $notice->description }}
            </div>
        </div>
    </div>

    <!-- Footer Actions -->
    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
        <x-button-cancel 
            wire:click="$dispatch('hideNoticeDetail')" 
            wire:loading.attr="disabled"
            class="inline-flex items-center justify-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            @lang('app.cancel')
        </x-button-cancel>
    </div>
</div>