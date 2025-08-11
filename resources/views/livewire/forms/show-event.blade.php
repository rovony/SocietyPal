<div class="max-w-4xl mx-auto mt-10 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    <!-- Header Section -->
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex flex-col space-y-4">
            <!-- Title -->
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ $event->event_name }}
            </h2>
            @if(user_can('Update Event') && !in_array($event->status, ['completed', 'cancelled']))
                <div class="space-x-2">
                    <x-button
                        wire:click="setEventStatus('completed', {{ $event->id }})"
                        wire:loading.attr="disabled"
                        class="text-white bg-red-600 hover:bg-red-700">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>@lang('app.mark_as_completed')</span>
                        </div>
                    </x-button>

                    <button type="button"
                        wire:click="setEventStatus('cancelled', {{ $event->id }})"
                        wire:loading.attr="disabled" class="inline-flex justify-center text-gray-500 items-center bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        @lang('app.mark_as_cancelled')
                    </button>
                </div>
            @endif

            <!-- Meta Information -->
            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ \Carbon\Carbon::parse($event->start_date_time)->timezone(timezone())->translatedFormat('d M Y, h:i A') }}
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ \Carbon\Carbon::parse($event->end_date_time)->timezone(timezone())->translatedFormat('d M Y, h:i A') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Description Section -->
    <div class="p-6">
        <div class="grid grid-cols-2 mb-6">
            <div>
                <x-label value="{{__('modules.event.where')}}"/>
                <p class="mt-2 text-gray-600 dark:text-gray-400">{{ ucfirst($event->where) ?? '--'}}</p>
            </div>
        </div>

        <div class="prose dark:prose-invert max-w-none">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ __('modules.notice.description') }}
            </h3>
            <div class="leading-relaxed text-gray-600 dark:text-gray-300">
                {{ $event->description }}
            </div>
        </div>

        <div class="grid grid-cols-2 mt-2 mb-2">
            <div>
                {{-- <x-label value="{{__('app.status')}}"/> --}}
                @if($event->status == 'completed')
                <x-badge type="success">{{ucFirst($event->status)}}</x-badge>
           @elseif($event->status == 'pending')
               <x-badge type="warning">{{ucFirst($event->status)}}</x-badge>
           @else
               <x-badge type="danger">{{ucFirst($event->status)}}</x-badge>
           @endif            </div>
        </div>

    <!-- Footer Actions -->
    <div class="px-6 py-4 border-t border-gray-200 rounded-b-lg bg-gray-50 dark:bg-gray-800/50 dark:border-gray-700">
        <x-button-cancel
            wire:click="$dispatch('hideEventDetail')"
            wire:loading.attr="disabled"
            class="inline-flex items-center justify-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            @lang('app.cancel')
        </x-button-cancel>
    </div>
</div>
