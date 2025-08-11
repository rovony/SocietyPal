<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    @lang('modules.dashboard.notices')
                </h2>
            </div>
        </div>
    </div>

    <!-- Notices List with Custom Scrollbar -->
    <div class="max-h-[320px] overflow-y-auto overflow-x-hidden p-4
                [&::-webkit-scrollbar]:w-1.5
                [&::-webkit-scrollbar-track]:rounded-lg
                [&::-webkit-scrollbar-thumb]:rounded-lg
                [&::-webkit-scrollbar-track]:bg-gray-100
                dark:[&::-webkit-scrollbar-track]:bg-gray-700
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-thumb]:bg-gray-500
                hover:[&::-webkit-scrollbar-thumb]:bg-gray-400
                dark:hover:[&::-webkit-scrollbar-thumb]:bg-gray-400">
        
        <div class="space-y-3">
            @forelse ($notices as $item)
                <div class="group">
                    <a href="javascript:;" 
                       wire:click="showNoticeDetail({{ $item->id }})"
                       class="block relative overflow-hidden transition-all duration-300 ease-in-out hover:shadow-lg">
                        
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 via-sky-500/5 to-blue-500/5 
                                  dark:from-blue-400/10 dark:via-sky-400/10 dark:to-blue-400/10
                                  group-hover:opacity-100 transition-opacity duration-300"></div>

                        <!-- Content -->
                        <div class="relative p-4 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-lg border 
                                  border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800
                                  transition-all duration-300 ease-in-out hover:shadow-lg">
                            
                            <div class="flex items-start justify-between">
                                <!-- Left: Notice Info -->
                                <div class="flex-1">
                                    <!-- Notice Title and Icon -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-gradient-to-br from-blue-50 to-sky-50 
                                                        dark:from-blue-900/50 dark:to-sky-900/50 rounded-lg">
                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                                </svg>
                                            </div>
                                            <h3 class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $item->title }}
                                            </h3>
                                        </div>

                                        <!-- View Arrow -->
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 ml-3">
                                            <span class="text-blue-600 dark:text-blue-300 text-sm flex items-center">
                                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" 
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Roles Tags -->
                                    @if (user_can('Show Notice Board'))
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach($item->roles as $role)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                           bg-gradient-to-r from-blue-50 to-sky-50 text-blue-800
                                                           dark:from-blue-900/50 dark:to-sky-900/50 dark:text-blue-200">
                                                    {{ $role->display_name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full 
                                bg-gradient-to-br from-blue-50 to-sky-50 
                                dark:from-blue-900/40 dark:to-sky-900/40 mb-4">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-300">
                        @lang('messages.noNoticeFound')
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    <x-right-modal wire:model.live="showNoticeDetailModal">
        <x-slot name="title">
            {{ __("modules.notice.viewNotice") }}
        </x-slot>

        <x-slot name="content">
            @if ($showNotice)
                @livewire('forms.showNotice', ['notice' => $showNotice], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showNoticeDetailModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>
</div>
