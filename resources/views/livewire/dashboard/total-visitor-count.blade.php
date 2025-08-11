<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 ">
    <!-- Header with Visitor Count -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    @lang('modules.dashboard.todayVisitor')
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ now()->format('l, d M Y') }}
                </p>
            </div>
            @if($visitor->count() > 0)
                <div class="flex items-center bg-indigo-100 dark:bg-indigo-900/50 px-4 py-2 rounded-lg">
                    <span class="text-sm font-medium text-indigo-800 dark:text-indigo-200">
                        {{ $visitor->count() }} @lang('modules.visitorManagement.visitors')
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Visitors List with Custom Scrollbar -->
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
            @forelse ($visitor as $item)
                <div class="group">
                    <a href="javascript:;"
                       wire:click="showVisitorDashboard({{ $item->id }})"
                       class="block relative overflow-hidden transition-all duration-300 ease-in-out hover:shadow-lg">

                        <!-- Background Pattern -->
                        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/5 via-purple-500/5 to-indigo-500/5
                                  dark:from-indigo-400/10 dark:via-purple-400/10 dark:to-indigo-400/10
                                  group-hover:opacity-100 transition-opacity duration-300"></div>

                        <!-- Content -->
                        <div class="relative p-4 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-lg border
                                  border-gray-100 dark:border-gray-700 hover:border-indigo-200 dark:hover:border-indigo-800
                                  transition-all duration-300 ease-in-out hover:shadow-lg">

                            <div class="flex items-center justify-between">
                                <!-- Left: Visitor Info -->
                                <div class="flex items-center space-x-3">
                                    <x-avatar-image :src="$item->visitor_photo_url" :alt="$item->visitor_name" :name="$item->visitor_name"/>

                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-800
                                                dark:from-indigo-900/50 dark:to-purple-900/50 dark:text-indigo-200">
                                        {{ $item->apartment->apartment_number }}
                                    </span>
                                </div>

                                <!-- Right: Time Info and View -->
                                <div class="flex items-center space-x-4">
                                    <!-- Time Info -->
                                    <div class="text-right space-y-1">
                                        <!-- In Time -->
                                        <div class="flex items-center justify-end text-sm">
                                            <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                            </svg>
                                            <span class="text-gray-600 dark:text-gray-300">
                                                {{ $item->in_time ? \Carbon\Carbon::parse($item->in_time)->format('h:i A') : '--' }}
                                            </span>
                                        </div>

                                        <!-- Out Time -->
                                        <div class="flex items-center justify-end text-sm">
                                            <svg class="w-4 h-4 text-red-500 dark:text-red-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            <span class="text-gray-600 dark:text-gray-300">
                                                {{ $item->out_time ? \Carbon\Carbon::parse($item->out_time)->format('h:i A') : '--' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- View Details -->
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <span class="text-indigo-600 dark:text-indigo-300 text-sm flex items-center">
                                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full
                                bg-gradient-to-br from-indigo-50 to-purple-50
                                dark:from-indigo-900/40 dark:to-purple-900/40 mb-4">
                        <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-300">
                        @lang('messages.noVisitorFound')
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    <x-right-modal wire:model.live="showVisitorModal">
        <x-slot name="title">
            {{ __("modules.visitorManagement.showVisitor") }}
        </x-slot>

        <x-slot name="content">
            @if ($showVisitor)
                @livewire('forms.showVisitor', ['visitor' => $showVisitor], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showVisitorModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>
</div>

