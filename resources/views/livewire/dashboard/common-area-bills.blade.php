<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header with Total Amount -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                @lang('menu.commonAreaBillsPaymentsDue')
            </h2>
            @if($commonAreaBills->count() > 0)
                <div class="flex items-center bg-orange-100 dark:bg-orange-900/50 px-4 py-2 rounded-lg">
                    <span class="text-sm font-medium text-orange-800 dark:text-orange-100">
                        {{ currency_format($commonAreaBills->sum('bill_amount')) }}
                    </span>
                    <span class="ml-2 text-xs text-orange-700 dark:text-orange-100">@lang('modules.utilityBills.totalDue')</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Bills List with Custom Scrollbar -->
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
            @forelse ($commonAreaBills as $item)
                <div class="group">
                    <a href="javascript:;" 
                       wire:click="showCommonAreaBill({{ $item->id }})"
                       class="block relative overflow-hidden transition-all duration-300 ease-in-out hover:shadow-lg">
                        
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-500/5 via-yellow-500/5 to-orange-500/5 
                                  dark:from-orange-400/20 dark:via-yellow-400/20 dark:to-orange-400/20
                                  animate-gradient-x"></div>

                        <!-- Content -->
                        <div class="relative p-4 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-lg border 
                                  border-gray-100 dark:border-gray-600 hover:border-orange-200 dark:hover:border-orange-700 
                                  transition-all duration-300 ease-in-out hover:shadow-lg">
                            
                            <div class="flex items-center justify-between">
                                <!-- Left: Bill Type with Icon -->
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-gradient-to-br from-orange-50 to-yellow-50 
                                                dark:from-orange-900/50 dark:to-yellow-900/50 rounded-lg">
                                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M15 9H9m6 3H9m6 3H9m3-9H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-5z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item->billType->name ?? '--' }}
                                        </h3>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                   bg-gradient-to-r from-orange-50 to-yellow-50 text-orange-800
                                                   dark:from-orange-900/50 dark:to-yellow-900/50 dark:text-orange-100">
                                            {{ \Carbon\Carbon::parse($item->bill_date)->format('d F Y') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Right: Amount, Status and View -->
                                <div class="flex items-center space-x-4">
                                    <!-- Amount and Status -->
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                            {{ currency_format($item->bill_amount) }}
                                        </div>
                                        <x-badge type="danger">{{ __('app.' . $item->status) }}</x-badge>
                                    </div>

                                    <!-- View Details -->
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <span class="text-orange-600 dark:text-orange-200 text-sm flex items-center">
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
                                bg-gradient-to-br from-orange-50 to-yellow-50 
                                dark:from-orange-900/50 dark:to-yellow-900/50 mb-4">
                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-300">
                        @lang('messages.noCommonAreaBillsFound')
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    <x-right-modal wire:model.live="showCommonAreaBillModal">
        <x-slot name="title">
            {{ __("modules.commonAreaBill.showCommonAreaBill") }}
        </x-slot>

        <x-slot name="content">
            @if ($selectedCommonAreaBills)
                @livewire('forms.showCommon-area-bill', ['commonAreaBill' => $selectedCommonAreaBills], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showCommonAreaBillModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>
</div>
