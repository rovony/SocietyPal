<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">
            <div>
                <x-search-dropdown id="serviceSettingsId" label="{{ __('modules.serviceManagement.serviceType') }}" model="serviceSettingsId" :options="$serviceSettings->map(fn($a) => ['id' => $a->id, 'number' => $a->name])" :required="true"/>
            </div>

            <x-label for="daily_help">
                <div class="flex items-center cursor-pointer">
                    <x-checkbox name="daily_help" id="daily_help" wire:model.live='daily_help'/>
                    <div class="ms-2">
                        @lang('modules.settings.dailyHelp')
                    </div>
                </div>
            </x-label>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <x-label for="tower" :value="__('modules.settings.selectTower')"/>
                    <x-select id="tower" class="block w-full mt-1" wire:model.live="selectedTower">
                        <option value="">@lang('modules.settings.selectTower')</option>
                        @foreach ($towers as $tower)
                            <option value="{{ $tower->id }}">{{ $tower->tower_name }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error for="selectedTower" class="mt-2" />
                </div>

                <div class="w-1/2">
                    <x-label for="floor" :value="__('modules.settings.selectFloor')"/>
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
                <x-label for="apartment" :value="__('modules.tenant.selectApartment')"/>
                <x-select id="apartment" class="block w-full mt-1" wire:model.live="selectedApartment">
                    <option value="">@lang('modules.tenant.selectApartment')</option>
                    @foreach ($apartments as $apartment)
                        @if(!in_array($apartment->id, $selectedApartmentsArray))
                            <option value="{{ $apartment->id }}">{{ $apartment->apartment_number }}</option>
                        @endif
                    @endforeach
                </x-select>
                <x-input-error for="selectedApartment" class="mt-2" />
            </div>

            @if(count($selectedApartmentsArray) > 0)
            <div class="mt-4">
                <label>@lang('modules.settings.selectedApartment')</label>
                <div class="mt-2 space-x-2">
                    @foreach($selectedApartmentsArray as $apartmentId)
                        @php
                            $apartment = \App\Models\ApartmentManagement::with(['towers', 'floors'])->find($apartmentId);
                        @endphp
                        @if($apartment)
                            <span class="inline-flex items-center px-3 py-1 text-white bg-blue-500 rounded-full">
                                {{ $apartment->apartment_number }}
                                <button type="button" wire:click="removeApartment({{ $apartmentId }})" class="ml-2 text-white hover:text-red-200">
                                    ×
                                </button>
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex space-x-4">
                <div class="w-full">
                    <x-label for="contactPersonName" value="{{ __('modules.serviceManagement.contactPersonName') }}" required="true" />
                    <x-input id="contactPersonName" class="block w-full mt-1" type="text" wire:model='contactPersonName' />
                    <x-input-error for="contactPersonName" class="mt-2" />
                </div>

            </div>
            <div class="flex space-x-4">
                <div class="w-full">
                    <x-label for="phoneNumber" value="{{ __('modules.serviceManagement.contactNumber') }}" required="true" />
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
                    <x-input id="phoneNumber" class="block w-full mt-1" type="tel" wire:model='phoneNumber' />
                    <x-input-error for="phoneNumber" class="mt-2" />
                </div>


            </div>
            </div>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <x-label for="companyName" value="{{ __('modules.serviceManagement.companyName') }}"/>
                    <x-input id="companyName" class="block w-full mt-1" type="text" wire:model='companyName' />
                    <x-input-error for="companyName" class="mt-2" />
                </div>

                <div class="w-1/2">
                    <x-label for="websiteLink" value="{{ __('modules.serviceManagement.websiteLink') }}" />
                    <x-input id="websiteLink" class="block w-full mt-1" type="text" wire:model='websiteLink' />
                    <x-input-error for="websiteLink" class="mt-2" />
                </div>
            </div>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <x-label for="paymentFrequency" value="{{ __('modules.serviceManagement.paymentFrequency') }}" required="true"/>
                    <x-select id="paymentFrequency" class="block w-full mt-1" wire:model.live="paymentFrequency">
                        <option value="per_visit">{{ __('modules.serviceManagement.perVisit') }}</option>
                        <option value="per_hour">{{ __('modules.serviceManagement.perHour') }}</option>
                        <option value="per_day">{{ __('modules.serviceManagement.perDay') }}</option>
                        <option value="per_week">{{ __('modules.serviceManagement.perWeek') }}</option>
                        <option value="per_month">{{ __('modules.serviceManagement.perMonth') }}</option>
                        <option value="per_year">{{ __('modules.serviceManagement.perYear') }}</option>
                    </x-select>
                    <x-input-error for="paymentFrequency" class="mt-2" />
                </div>

                <div class="w-1/2">
                    <x-label for="price" value="{{ __('modules.serviceManagement.price') }}" />
                    <x-input id="price" class="block w-full mt-1" step="0.01" min='0' type="number" wire:model='price' />
                    <x-input-error for="price" class="mt-2" />
                </div>
            </div>

            <div class="mt-2">
                <x-label for="description" value="{{ __('modules.serviceManagement.description') }}" />
                <x-textarea id="description" class="block w-full mt-1" type="text" wire:model='description' />
                <x-input-error for="description" class="mt-2" />
            </div>

            <div class="mt-2">
                <x-label for="status" value="{{ __('modules.serviceManagement.status') }}"/>
                <x-select id="status" class="block w-full mt-1" wire:model.live="status">
                    <option value="not_available">{{ __('modules.settings.notAvailable') }}</option>
                    <option value="available">{{ __('modules.settings.available') }}</option>
                </x-select>
                <x-input-error for="status" class="mt-2" />
            </div>
        </div>

        <div x-data="{ photoName: null, photoPreview: null, hasNewPhoto: @entangle('hasNewPhoto').live , clearFileInput() { this.photoName = ''; this.photoPreview = ''; this.hasNewPhoto = false; this.$refs.photo.value = ''; @this.set('teamLogo', ''); } }" class="col-span-6 mt-2 sm:col-span-4">
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
            <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.profilePhoto.click()">
                {{ __('modules.visitorManagement.uploadPhoto') }}
            </x-secondary-button>

            @if ($serviceManagement->service_photo_url)
            <!-- Current Profile Photo -->
            <div class="mt-2" x-show="! photoPreview">
                <img src="{{ $serviceManagement->service_photo_url }}" alt="{{ $serviceManagement->contact_person_name }}" class="object-cover w-20 h-20 overflow-hidden rounded-full">
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

            @if ($serviceManagement->service_photo_url)
                <x-danger-button type="button" class="mt-2" wire:click="removeProfilePhoto" x-on:click.prevent="clearFileInput()" x-show="!hasNewPhoto" x-cloak>
                    {{ __('modules.visitorManagement.removePhoto') }}
                </x-danger-button>
            @endif
        </div>

        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel wire:click="$dispatch('hideEditServiceManagement')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
