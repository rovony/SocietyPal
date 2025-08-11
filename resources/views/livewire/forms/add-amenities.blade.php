<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="amenityName" value="{{ __('modules.settings.societyAmenitiesName') }}" required="true" />
                <x-input id="amenityName" class="block w-full mt-1" type="text" autofocus wire:model='amenityName' />
                <x-input-error for="amenityName" class="mt-2" />
            </div>

            <div>
                <x-label for="status" value="{{ __('modules.settings.societyAmenitiesStatus') }}" required="true" />
                <x-select id="status" class="block w-full mt-1" wire:model.live="status">
                    <option value="not_available">@lang('modules.settings.notAvailable')</option>
                    <option value="available">@lang('modules.settings.available')</option>
                </x-select>
                <x-input-error for="status" class="mt-2" />
            </div>
            @if ($status=='available')
                <div>
                    <x-label for="bookingStatus">
                        <div class="flex items-center">
                            <x-checkbox name="bookingStatus" id="bookingStatus" wire:model.live='bookingStatus'  />

                            <div class="ms-2">
                                @lang('modules.settings.societyAmenitiesBooking')
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            @if ($bookingStatus)
                <div class="grid grid-cols-3 gap-x-2">
                    <div>
                        <x-label for="startTime" value="{{__('modules.settings.startTime')}}" required="true"/>
                        <div class="relative">
                            <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input type="time" id="startTime" wire:model.live='startTime' class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500" min="00:00" max="23:59" value="00:00" />
                        </div>
                        <x-input-error for="startTime" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="endTime" value="{{__('modules.settings.endTime')}}" required="true" />
                        <div class="relative">
                            <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input type="time" id="endTime" wire:model.live='endTime' class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500" min="00:00" max="23:59" value="00:00" />
                        </div>
                        <x-input-error for="endTime" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="slotTime" value="{{__('modules.settings.slotTime')}}" required="true" />
                        <x-input type="number" wire:model.live='slotTime' step='5' min='0' class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500"  />
                        <x-input-error for="slotTime" class="mt-2" />

                    </div>
                </div>

                <div>

                    <x-label for="{{ __('modules.settings.societyMultipleBooking')}}">
                        <div class="flex items-center">
                            <x-checkbox name="multipleBookingStatus" id="multipleBookingStatus" wire:model.live='multipleBookingStatus'/>
                            <div class="ms-2">
                                @lang('modules.settings.societyMultipleBooking')
                            </div>
                        </div>
                    </x-label>
                </div>

                @if ($multipleBookingStatus)
                <div>
                    <x-label for="numberOfPerson" value="{{__('modules.settings.numberOfPersonForm')}}" required="true"/>
                    <x-input id="numberOfPerson" class="block w-full mt-1" min='0' type="number" wire:model='numberOfPerson' />
                    <x-input-error for="numberOfPerson" class="mt-2" />
                </div>
            @endif
            @endif

        </div>

        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideAddAmenities')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>

        </div>
    </form>
</div>
