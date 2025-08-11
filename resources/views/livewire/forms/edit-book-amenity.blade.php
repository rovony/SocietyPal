<div>
    <form wire:submit="submitBooking">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="amenity" value="{{ __('modules.bookAmenity.chooseAmenity') }}" required/>
                <x-select class="mt-1 block w-full" wire:model.live='selectedAmenity'>
                    <option value="">{{ __('modules.bookAmenity.selectAmenity') }}</option>
                    @foreach ($amenities as $amenity)
                        <option value="{{ $amenity->id }}">{{ $amenity->amenities_name }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="selectedAmenity" class="mt-2" />
            </div>

            <div>
                <x-label for="bookingDate" value="{{ __('modules.bookAmenity.bookingDate') }}" required/>
                <x-datepicker  class="w-full" wire:model.live="bookingDate" id="bookingDate" autocomplete="off" placeholder="{{ __('modules.bookAmenity.bookingDate') }}"  :disabled="!$selectedAmenity" :minDate="true"/>
                <x-input-error for="bookingDate" class="mt-2" />
            </div>

            @if ($bookingDate && !empty($availableSlots))
                <div>
                    <x-label for="bookingTime" value="{{ __('modules.bookAmenity.bookingTime') }}" required/>
                    <ul class="grid w-full lg:gap-6 gap-4 lg:grid-cols-6">
                        @foreach ($availableSlots as $slot)
                            <li wire:key='slot.{{ $loop->index }}.{{ microtime() }}'>
                                <input type="checkbox" id="slot{{ $loop->index }}" wire:model.live="bookingTime" 
                                value="{{ $slot['time'] }}" class="hidden peer"
                                @if(in_array($slot['time'], $bookingTime)) checked @endif 
                                @if($slot['is_disabled']) disabled @endif />
                                <label for="slot{{ $loop->index }}" class="inline-flex items-center justify-center w-full p-2 text-gray-500 bg-gray-50 border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-skin-base peer-checked:border-skin-base peer-checked:text-skin-base hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 @if($slot['is_disabled']) opacity-50 cursor-not-allowed @endif">
                                    <div class="block">
                                        <div class="w-full text-md font-medium">
                                            {{ \Carbon\Carbon::parse($slot['time'])->translatedFormat('h:i A') }}
                                        </div>
                                    </div>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                    @if ($bookingTime && $storedPersons && count($this->bookingTime) == 1)
                        <p class="mt-4 text-sm flex items-center text-gray-700 dark:text-gray-300">
                            <x-alert type="secondary">{{ __('modules.bookAmenity.totalPersons') }}: {{ $storedPersons }}</x-alert> 
                        </p>
                    @endif
                    <x-input-error for="bookingTime" class="mt-2" />
                </div>
            @endif
          
            @if ($showPersonsField && is_array($this->bookingTime) && count($this->bookingTime) == 1)
            <div>
                    <x-label for="persons" value="{{ __('modules.bookAmenity.numberOfpersons') }}" required/>
                    <x-input id="persons" type="number" min="1" wire:model.live="persons" class="block mt-1 w-full" />
                    <x-input-error for="persons" class="mt-2" />
                </div>
            @endif
        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6">
            <x-button>@lang('app.update')</x-button>
            <x-button-cancel wire:click="$dispatch('hideEditBookAmenity')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>