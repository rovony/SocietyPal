<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header with Counter -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    @lang('menu.rentPaymentsDue')
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ now()->format('l, d M Y') }}
                </p>
            </div>
            @if($rents->count() > 0)
                <div class="flex items-center bg-red-100 dark:bg-red-900/50 px-4 py-2 rounded-lg">
                    <span class="text-sm font-medium text-red-800 dark:text-red-200">
                        {{ $rents->count() }} @lang('modules.tickets.pending')
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Content with Custom Scrollbar -->
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
            @forelse ($rents as $rent)
                <div class="group">
                    <a href="javascript:;" 
                       wire:click="showRentDetails({{ $rent->id }})" 
                       class="block relative overflow-hidden transition-all duration-300 ease-in-out hover:shadow-lg">
                        
                        <!-- Content -->
                        <div class="relative p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 
                                  dark:border-gray-700 hover:border-red-200 dark:hover:border-red-800
                                  transition-all duration-300 ease-in-out hover:shadow-lg">
                            
                            <div class="flex items-center justify-between">
                                <!-- Left: Apartment Info -->
                                <div class="flex items-center space-x-3">
                                    <div class="p-2 bg-gradient-to-br from-red-50 to-orange-50 
                                                dark:from-red-900/40 dark:to-orange-900/40 rounded-lg">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">
                                                {{ $rent->apartment->apartment_number }}
                                            </h3>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $rent->tenant->user->name }}
                                            </span>
                                        </div>
                                        <div class="flex items-center mt-1">
                                            <svg class="w-4 h-4 text-red-500 dark:text-red-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-sm text-gray-600 dark:text-gray-300">
                                                {{ ucfirst($rent->rent_for_month) . ' ' . $rent->rent_for_year }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: Amount and View -->
                                <div class="flex items-center space-x-4">
                                    <!-- Amount and Status -->
                                    <div class="text-right">
                                        <div class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                            {{ currency_format($rent->rent_amount) }}
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                   bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            {{ ucfirst($rent->status) }}
                                        </span>
                                    </div>

                                    <!-- View Details -->
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <span class="text-red-600 dark:text-red-300 text-sm flex items-center">
                                            
                                            <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" 
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
                                bg-gradient-to-br from-red-50 to-orange-50 
                                dark:from-red-900/40 dark:to-orange-900/40 mb-4">
                        <svg class="w-8 h-8 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-300">
                        @lang('messages.noRentFound')
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    <x-right-modal wire:model.live="showRentModal">
        <x-slot name="title">
            {{ __("modules.rent.viewRentDetails") }}
        </x-slot>

        <x-slot name="content">
            @if ($showRent)
                @livewire('forms.showRent', ['rent' => $showRent], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showRentModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>
</div>
