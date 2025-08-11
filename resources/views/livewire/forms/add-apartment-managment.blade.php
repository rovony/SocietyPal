<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <x-label for="tower" :value="__('modules.settings.selectTower')" required="true" />
                    <x-select id="tower" class="block w-full mt-1" wire:model.live="selectedTower">
                        <option value="">@lang('modules.settings.societyTower')</option>
                        @foreach ($towers as $tower)
                            <option value="{{ $tower->id }}">{{ $tower->tower_name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="selectedTower" class="mt-2" />
                </div>

                <div class="w-1/2">
                   <x-label for="floor" :value="__('modules.settings.selectFloor')" required="true" />
                    <x-select id="floor" class="block w-full mt-1" wire:model.live="selectedFloor">
                        <option value="">@lang('modules.settings.selectFloor')</option>
                        @foreach ($floors as $floor)
                            <option value="{{ $floor->id }}">{{ $floor->floor_name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="selectedFloor" class="mt-2" />
                </div>
            </div>

            <div>
                <x-label for="apartmentNumber" value="{{ __('modules.settings.apartmentNumber') }}" required="true" />
                <x-input id="apartmentNumber" class="block w-full mt-1" type="text" wire:model='apartmentNumber' />
                <x-input-error for="apartmentNumber" class="mt-2" />
            </div>

            <div>
                <x-label for="selectedParkingCodes" :value="__('modules.settings.selectParkingCode')" />
                <div x-data="{ isOpen: @entangle('isOpen'), selectedParkingCodes: @entangle('selectedParkingCodes') }" @click.away="isOpen = false">
                    <!-- Container for input and button -->
                    <div class="flex items-center space-x-2">
                        <!-- Dropdown for selecting users -->
                        <div class="relative flex-1">
                            <div @click="isOpen = !isOpen" class="p-2 bg-gray-100 border rounded cursor-pointer dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                                @lang('modules.settings.selectParkingCodes')
                            </div>

                            <ul x-show="isOpen" x-transition class="absolute z-10 w-full mt-1 overflow-auto bg-white rounded-lg shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                                @forelse ($parkingCodes as $parkingCode)

                                    <li @click="$wire.toggleSelectType({ id: {{ $parkingCode->id }}, parking_code: '{{ addslashes($parkingCode->parking_code) }}' })"
                                        wire:key="{{ $loop->index }}"
                                        class="relative py-2 pl-3 text-gray-900 transition-colors duration-150 cursor-pointer select-none pr-9 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600 "
                                        :class="{ 'bg-gray-100': selectedParkingCodes.includes({{ $parkingCode->id }}) }"
                                        role="option">

                                        <div class="flex items-center">
                                            <span class="block ml-3 truncate">{{ $parkingCode->parking_code }}</span>
                                            <span x-show="selectedParkingCodes.includes({{ $parkingCode->id }})" class="absolute inset-y-0 right-0 flex items-center pr-4 text-black dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600" x-cloak>
                                                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                    </li>
                                @empty
                                    <x-no-results :message="__('messages.noParkingManagementFound')" />
                                @endforelse
                            </ul>
                        </div>

                        <!-- Add Parking Button -->
                         <button type="button" class="ml-2 inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600" wire:click="$toggle('showAddParkingModal')">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Display selected parking codes -->
                    <div x-show="selectedParkingCodes.length > 0" x-transition class="mt-2 w-64 md:w-auto break-words">
                        <span class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.selectedParkingCodes')</span>
                        <span class="text-sm text-gray-900 dark:text-gray-400">
                            {{ implode(', ', $parkingCodes->whereIn('id', $selectedParkingCodes)->pluck('parking_code')->toArray()) }}
                        </span>
                    </div>

                    <x-input-error for="selectedParkingCodes" class="mt-2" />
                </div>
            </div>


            <div>
                <x-label for="apartmentArea" value="{{ __('modules.settings.apartmentArea') . ' ' . '(' .$unitType.')' }}" required="true" />
                <x-input id="apartmentArea" class="block w-full mt-1" type="number" wire:model='apartmentArea' />
                <x-input-error for="apartmentArea" class="mt-2" />
            </div>

            <div>
                <x-label for="apartmentId" :value="__('modules.settings.apartmentType')" required="true" />
                <x-select id="apartmentId" class="block w-full mt-1" wire:model="apartmentId">
                    <option value="">@lang('modules.settings.selectApartmentType')</option>
                    @foreach ($apartment as $item)
                        <option value="{{ $item->id }}">{{ $item->apartment_type}}</option>
                    @endforeach
                </x-select>
                <x-input-error for="apartmentId" class="mt-2" />
            </div>

            <div>
                <x-label for="status" value="{{ __('modules.settings.status') }}" required="true" />
                <x-select id="status" class="block w-full mt-1" wire:model.live="status">
                    <option value="">@lang('modules.settings.selectStatus')</option>
                    <option value="not_sold">{{ __('modules.settings.notSold') }}</option>
                    <option value="occupied">{{ __('modules.settings.occupied') }}</option>
                    <option value="available_for_rent">{{ __('modules.settings.availableForRent') }}</option>
                </x-select>
                <x-input-error for="status" class="mt-2" />
            </div>

            @if($status == "occupied")
                <div>
                    <x-label for="userId" :value="__('modules.user.selectOwner')" required="true"/>
                    <x-select id="userId" class="block w-full mt-1" wire:model="userId">
                        <option value="">@lang('modules.user.selectOwner')</option>
                        @foreach ($user as $item)
                            <option value="{{ $item->id }}">{{ $item->name}}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="userId" class="mt-2" />
                </div>
            @endif

            @if($status == "available_for_rent")
            <div>
                <x-label for="userId" :value="__('modules.user.selectOwner')"/>
                <x-select id="userId" class="block w-full mt-1" wire:model="userId">
                    <option value="">@lang('modules.user.selectOwner')</option>
                    @foreach ($user as $item)
                        <option value="{{ $item->id }}">{{ $item->name}}</option>
                    @endforeach
                </x-select>
                <x-input-error for="userId" class="mt-2" />
            </div>
        @endif
        </div>


        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideAddApartmentManagement')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
    <x-dialog-modal wire:model.live="showAddParkingModal" maxWidth="xl">
        <x-slot name="title">
            {{ __("modules.settings.addParking") }}
        </x-slot>

        <x-slot name="content">
            <form wire:submit="addModalParking">
                @csrf
                <div class="space-y-4">
                    <div>
                        <x-label for="parkingCode" value="{{ __('modules.settings.societyParkingCode') }}" required="true" />
                        <x-input id="parkingCode" class="block w-full mt-1" type="text" wire:model.live='parkingCode' />
                        <x-input-error for="parkingCode" class="mt-2" />
                    </div>
                </div>
                <div class="flex w-full pb-4 mt-6 space-x-4">
                    <x-button>@lang('app.save')</x-button>
                </div>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button  wire:click="$set('showAddParkingModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
</div>
