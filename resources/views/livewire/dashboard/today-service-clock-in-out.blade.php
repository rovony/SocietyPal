<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <!-- Header with Action Button -->
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    @lang('modules.dashboard.serviceClockInOut')
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ now()->timezone(timezone())->format('l, d M Y') }}
                </p>
            </div>
            @if(user_can('Create Service Time Logging') || isRole() == 'Guard')
                <x-button type='button' 
                         wire:click="$set('showAddAttendance', true)"
                         class="inline-flex items-center bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 focus:bg-emerald-600">
                    <div class="flex items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="ml-2">@lang('app.clockIn')</span>
                    </div>
                </x-button>
            @endif
        </div>
    </div>

    <!-- Service List with Custom Scrollbar -->
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
            @forelse ($attendances as $item)
                <div class="relative group">
                    <!-- Service Card -->
                    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-100 
                              dark:border-gray-700 hover:border-emerald-200 dark:hover:border-emerald-800
                              transition-all duration-300 ease-in-out hover:shadow-lg">
                        
                        <!-- Service Info -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <x-avatar-image :src="$item->service->service_photo_url" :alt="$item->service->contact_person_name" :name="$item->service->contact_person_name"/>
                                
                                <div class="flex items-center mt-1 space-x-3">
                                    <!-- Clock In Time -->
                                    <div class="inline-flex items-center text-sm">
                                        <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                        <span class="ml-1.5 text-gray-600 dark:text-gray-300">
                                            {{ \Carbon\Carbon::parse($item->clock_in_time)->timezone(timezone())->format('h:i A') }}
                                        </span>
                                    </div>

                                    <!-- Clock Out Time -->
                                    @if($item->status != 'clock_in')
                                        <div class="inline-flex items-center text-sm">
                                            <svg class="w-4 h-4 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            <span class="ml-1.5 text-gray-600 dark:text-gray-300">
                                                {{ \Carbon\Carbon::parse($item->clock_out_time)->timezone(timezone())->format('h:i A') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Clock Out Button -->
                            @if($item->status == 'clock_in')
                                @if(user_can('Update Service Time Logging') || isRole() == 'Guard')
                                    <x-button wire:click="clockOut({{ $item->id }})" 
                                             class="inline-flex items-center bg-red-500 hover:bg-red-600 active:bg-red-700 focus:bg-red-600">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            <span class="ml-2">@lang('app.clockOut')</span>
                                        </div>
                                    </x-button>
                                @else
                                    <x-button disabled class="inline-flex items-center bg-gray-400 cursor-not-allowed">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            <span class="ml-2">@lang('app.clockOut')</span>
                                        </div>
                                    </x-button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full 
                                bg-gradient-to-br from-emerald-50 to-teal-50 
                                dark:from-emerald-900/40 dark:to-teal-900/40 mb-4">
                        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-300">
                        @lang('messages.noClockInOut')
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal -->
    <x-dialog-modal wire:model.live="showAddAttendance">
        <x-slot name="title">
            {{ __("modules.serviceManagement.addClockIn") }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.addAttendance')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showAddAttendance', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
</div>