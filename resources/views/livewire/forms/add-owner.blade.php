<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                <x-label for="name" value="{{ __('modules.user.name') }}" required/>
                <x-input id="name" class="block w-full mt-1" type="text" autofocus wire:model='name' autocomplete="off"/>
                <x-input-error for="name" class="mt-2" />
            </div>

            <div>
                <x-label for="email" value="{{ __('modules.user.email') }}" required/>
                <x-input id="email" class="block w-full mt-1" type="email" autofocus wire:model='email' autocomplete="off"/>
                <x-input-error for="email" class="mt-2" />
            </div>

            <div>
                <x-label for="phone" value="{{ __('modules.user.phone') }}" />

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

                    <!-- Phone Input -->
                    <x-input id="phone" class="w-1/2" type="tel" min="0" wire:model="phone" autocomplete="off" placeholder="Phone" />
                </div>

                <x-input-error for="phone" class="mt-2" />
            </div>
            <!-- Tower and Floor Selection -->
            <div class="flex space-x-4">
                <div class="w-1/2">
                    <x-label for="tower" :value="__('modules.settings.selectTower')" required="true" />
                    <x-select id="tower" class="block w-full mt-1" wire:model.live="selectedTower">
                        <option value="">@lang('modules.settings.selectTower')</option>
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

            <!-- Apartment Selection -->
            <div>
                <x-label for="apartment" :value="__('modules.tenant.selectApartment')" required="true" />
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

            <!-- Selected Apartments Display -->
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

            <x-input-error for="selectedApartmentsArray" class="mt-2" />

            <!-- Upload Profile Picture with Preview -->
            <div x-data="{ photoName: null, photoPreview: null }" x-on:photo-removed.window="photoName = null; photoPreview = null;">
                <input type="file" id="profilePhoto" class="hidden" wire:model="profilePhoto" accept="image/png, image/gif, image/jpeg, image/webp"
                    x-ref="profilePhoto"
                    x-on:change="
                        photoName = $refs.profilePhoto.files[0].name;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            photoPreview = e.target.result;
                        };
                        reader.readAsDataURL($refs.profilePhoto.files[0]);
                    "/>

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.profilePhoto.click()">
                    {{ __('modules.user.uploadProfilePicture') }}
                </x-secondary-button>

                <div class="mt-2" x-show="photoPreview">
                    <span class="block w-20 h-20 bg-center bg-no-repeat bg-cover rounded-full"
                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <div class="mt-2" x-show="photoPreview">
                    <x-danger-button type="button" wire:click="removeProfilePhoto">
                        {{ __('modules.user.removeProfilePicture') }}
                    </x-danger-button>
                </div>

                <x-input-error for="profilePhoto" class="mt-2" />
            </div>
        </div>

        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel wire:click="$dispatch('hideAddOwner')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
