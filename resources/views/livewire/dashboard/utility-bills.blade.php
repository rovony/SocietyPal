<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header with Total Amount -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                @lang('menu.utilityBillsPaymentsDue')
            </h2>
            @if($utilityBills->count() > 0)
                <div class="flex items-center bg-red-100 dark:bg-red-900/50 px-4 py-2 rounded-lg">
                    <span class="text-sm font-medium text-red-800 dark:text-red-200">
                       {{ currency_format($utilityBills->sum('bill_amount')) }}
                    </span>
                    <span class="ml-2 text-xs text-red-800 dark:text-red-200">@lang('modules.utilityBills.totalDue')</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Bills Grid with Custom Scrollbar -->
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
        
        <div class="grid gap-3">
            @forelse ($utilityBills as $item)
                <div class="group relative">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500/5 to-blue-500/5 dark:from-purple-400/10 dark:to-blue-400/10 
                              rounded-lg transform scale-[98%] group-hover:scale-100 transition-transform duration-300"></div>
                    <a href="javascript:;" 
                       wire:click="showUtilityBill({{ $item->id }})"
                       class="relative block p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 
                              dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 
                              transition-all duration-300 ease-in-out hover:shadow-lg">
                        
                        <!-- Single Line Layout -->
                        <div class="flex items-center justify-between">
                            <!-- Left Section: Icon, Apartment & Bill Type -->
                            <div class="flex items-center space-x-3">
                                <!-- Icon based on bill type -->
                                <div class="p-2 bg-gradient-to-br from-purple-50 to-blue-50 
                                            dark:from-purple-900/40 dark:to-blue-900/40 rounded-xl">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M15 9H9m6 3H9m6 3H9m3-9H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-5z"/>
                                    </svg>
                                </div>
                                <!-- Apartment Number and Bill Type -->
                                <div class="flex items-left space-x-2">
                                    <h3 class="text-base font-medium text-gray-900 dark:text-white">
                                        {{ $item->apartment->apartment_number }}
                                    </h3>
                                    <span class="inline-flex items-left px-2.5 py-0.5 rounded-full text-xs font-medium
                                               bg-gradient-to-r from-purple-50 to-blue-50 text-purple-800
                                               dark:from-purple-900/40 dark:to-blue-900/40 dark:text-purple-200">
                                        {{ $item->billType->name ?? '--' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Middle Section: Date -->
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($item->bill_date)->format('d F Y') }}
                                </span>
                            </div>

                            <!-- Right Section: Amount, Status, and View -->
                            <div class="flex items-center space-x-4">
                                <!-- Amount and Status -->
                                <div class="text-right">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ currency_format($item->bill_amount) }}
                                    </span>
                                    <x-badge type="danger" class="ml-2">{{ __('app.' . $item->status) }}</x-badge>
                                </div>

                                <!-- View Button -->
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <span class="text-purple-600 dark:text-purple-300 text-sm flex items-center">
                                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full 
                                bg-gradient-to-br from-purple-50 to-blue-50 
                                dark:from-purple-900/40 dark:to-blue-900/40 mb-4">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">
                        @lang('messages.noUtilityBillsFound')
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    <x-right-modal wire:model.live="showUtilityBillModal">
        <x-slot name="title">
            {{ __("modules.utilityBills.showUtilityBill") }}
        </x-slot>

        <x-slot name="content">
            @if ($selectedUtilityBills)
                @livewire('forms.showUtility-bills', ['utilityBill' => $selectedUtilityBills], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showUtilityBillModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>
</div>
