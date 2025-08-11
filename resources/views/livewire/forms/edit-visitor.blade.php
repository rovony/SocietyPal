<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div class="grid grid-cols-2 gap-x-4">
                <div>
                    <x-label for="tower" :value="__('modules.settings.selectTower')" required="true" />
                    <x-select id="tower" class="block w-full mt-1" wire:model.live="selectedTower">
                        <option value="">@lang('modules.settings.selectTower')</option>
                        @foreach ($towers as $tower)
                            <option value="{{ $tower->id }}">{{ $tower->tower_name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="selectedTower" class="mt-2" />
                </div>
                <div>
                    <x-label for="floors" :value="__('modules.settings.selectFloor')" required="true" />
                    <x-select id="floors" class="block w-full mt-1" wire:model.live="selectedFloor">
                        <option value="">@lang('modules.settings.selectFloor')</option>
                        @foreach ($floors as $floor)
                            <option value="{{ $floor->id }}">{{ $floor->floor_name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="selectedFloor" class="mt-2" />
                </div>
            </div>

            <div>
                <x-label for="apartmentId" :value="__('modules.settings.societyApartmentNumber')" required="true" />
                <x-select id="apartmentId" class="block w-full mt-1" wire:model.live="apartmentId">
                    <option value="">@lang('modules.settings.selectApartmentNumber')</option>
                    @foreach ($apartments as $item)
                        <option value="{{ $item->id }}">{{ $item->apartment_number }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="apartmentId" class="mt-2" />
            </div>

            @if($selectedApartment)
                <div class="p-4 border-2 border-gray-500 rounded-lg bg-gray-40">
                    @php
                        $currentTenant = $selectedApartment->tenants->firstWhere('pivot.status', 'current_resident');
                    @endphp

                    @if ($selectedApartment->status === 'rented' && $currentTenant)
                        <p class="block text-sm font-medium">
                            @lang('modules.visitorManagement.tenantName')
                            {{ $currentTenant->user->name }}
                        </p>
                        <p class="block text-sm font-medium">
                            @lang('modules.visitorManagement.tenantNumber')
                            {{ $currentTenant->user->phone_number }}
                        </p>
                    @else
                        <p class="block text-sm font-medium">
                            @lang('modules.visitorManagement.apartmentOwnerName')
                            {{ $selectedApartment->user ? $selectedApartment->user->name : __('modules.visitorManagement.noOwnerName') }}
                        </p>
                        <p class="block text-sm font-medium">
                            @lang('modules.visitorManagement.apartmentOwnerNumber')
                            {{ $selectedApartment->user ? $selectedApartment->user->phone_number : __('modules.visitorManagement.noOwnerNumber') }}
                        </p>
                    @endif
                </div>
            @endif


            <div class="grid grid-cols-2 gap-x-4">
                <div>
                    <x-label for="visitorName" value="{{ __('modules.visitorManagement.visitorName') }}" />
                    <x-input id="visitorName" class="block w-full mt-1" type="text" wire:model='visitorName' />
                    <x-input-error for="visitorName" class="mt-2" />
                </div>
            </div>

            <div>
                <div >
                    <x-label for="mobileNumber" value="{{ __('modules.visitorManagement.visitorMobile') }}" required="true"/>
                    <div class="flex items-center mt-1 space-x-2">
                        <!-- Show selected flag -->
                        @if($selectedCountry && $selectedCountry->flagUrl)
                            <img src="{{ $selectedCountry->flagUrl }}" class="w-5 h-5 border border-gray-200 rounded-full" alt="Flag">
                        @endif

                        <!-- Country Code Dropdown -->
                        <x-select id="country_code" class="" wire:model="countryCode" wire:change="updateCountryName" >
                            <option value="">+Code</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->phonecode }}">
                                    +{{ $country->phonecode }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-input id="mobileNumber" class="block w-full mt-1" type="number" wire:model='mobileNumber' />

                        <x-input-error for="mobileNumber" class="mt-2" />
                </div>
             </div>
            </div>
            <div class="grid grid-cols-2 gap-x-4">
                <div>
                    <x-label for="visitorTypeId" :value="__('modules.visitorManagement.visitorType')" required="true" />
                    <x-select id="visitorTypeId" class="block w-full mt-1" wire:model.live="visitorTypeId">
                        <option value="">@lang('modules.visitorManagement.selectVisitorType')</option>
                        @foreach ($visitorTypes as $item)
                            <option value="{{ $item->id }}">{{ $item->name}}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="visitorTypeId" class="mt-2" />
                </div>
                <div>
                    <x-label for="purposeOfVisit" value="{{ __('modules.visitorManagement.purposeOfVisit') }}"/>
                    <x-input id="purposeOfVisit" class="block w-full mt-1" type="text" wire:model='purposeOfVisit' />
                    <x-input-error for="purposeOfVisit" class="mt-2" />
                </div>
            </div>

            <div>
                <x-label for="visitorAddress" value="{{ __('modules.visitorManagement.visitorAddress') }}" />
                <x-textarea id="visitorAddress" class="block w-full mt-1" type="text" wire:model='visitorAddress' />
                <x-input-error for="visitorAddress" class="mt-2" />
            </div>

            <div class="grid grid-cols-2 gap-x-4">
                <div>
                    <x-label for="dateOfVisit" value="{{ __('modules.visitorManagement.dateOfVisit') }}"/>
                    <x-datepicker class="block w-full mt-1" wire:model.live="dateOfVisit" id="dateOfVisit" autocomplete="off" placeholder="{{ __('modules.visitorManagement.dateOfVisit') }}" />
                    <x-input-error for="dateOfVisit" class="mt-2" />
                </div>
                <div>
                    <x-label for="inTime" value="{{ __('modules.visitorManagement.inTime') }}" required="true"/>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input type="time" id="inTime" wire:model.live='inTime' class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500" min="00:00" max="23:59" value="00:00" />
                    </div>
                    <x-input-error for="inTime" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-x-4">
                <div>
                    <x-label for="dateOfExit" value="{{ __('modules.visitorManagement.dateOfExit') }}"/>
                    <x-datepicker class="block w-full mt-1" wire:model.live="dateOfExit" id="dateOfExit" autocomplete="off" placeholder="{{ __('modules.visitorManagement.dateOfExit') }}" />
                    <x-input-error for="dateOfExit" class="mt-2" />
                </div>
                <div>
                    <x-label for="outTime" value="{{ __('modules.visitorManagement.outTime') }}"/>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input type="time" id="outTime" wire:model.live='outTime' class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500" min="00:00" max="23:59" value="00:00" />
                    </div>
                    <x-input-error for="outTime" class="mt-2" />
                </div>
            </div>

            <div x-data="{ photoName: null, photoPreview: null, hasNewPhoto: @entangle('hasNewPhoto').live , clearFileInput() { this.photoName = ''; this.photoPreview = ''; this.hasNewPhoto = false; this.$refs.photo.value = ''; @this.set('teamLogo', ''); } }" class="col-span-6 sm:col-span-4">
                <!-- Profile Photo File Input -->
                <input type="file" class="hidden"
                            wire:model="photo"
                            accept="image/*"
                            x-ref="profilePhoto"
                            x-on:change="
                                    photoName = $refs.profilePhoto.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.profilePhoto.files[0]);
                                    hasNewPhoto = true;
                            " />

                <x-label for="photo" value="{{ __('Visitor Photo') }}" />

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.profilePhoto.click()">
                    {{ __('modules.visitorManagement.uploadPhoto') }}
                </x-secondary-button>

                @if ($visitor->visitor_photo_url)
                <!-- Current Profile Photo -->
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $visitor->visitor_photo_url }}" alt="{{ $visitor->visitor_name }}" class="object-cover w-20 h-20 overflow-hidden rounded-full">
                </div>
                @endif

                <!-- New Profile Photo Preview -->
                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block w-20 h-20 bg-center bg-no-repeat bg-cover rounded-full"
                            x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <!-- Show the appropriate button based on the state -->
                <x-secondary-button class="mt-2" type="button" x-on:click.prevent="clearFileInput()" x-show="hasNewPhoto" x-cloak>
                    {{ __('modules.visitorManagement.removePhoto') }}
                </x-secondary-button>

                @if ($visitor->visitor_photo_url)
                    <x-danger-button type="button" class="mt-2" wire:click="removeProfilePhoto" x-on:click.prevent="clearFileInput()" x-show="!hasNewPhoto" x-cloak>
                        {{ __('modules.visitorManagement.removePhoto') }}
                    </x-danger-button>
                @endif
            </div>
        </div>



        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideEditVisitor')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
