<div>
    <form wire:submit.prevent="updateTenant">
        @csrf
        <div class="space-y-4">

            <div>

                <x-label for="name" value="{{ __('modules.tenant.name') }}" required/>
                <x-input id="name" class="block w-full mt-1" type="text" autofocus wire:model='name' autocomplete="off"/>
                <x-input-error for="name" class="mt-2" />
            </div>

            <div>
                <x-label for="email" value="{{ __('modules.tenant.email') }}" required/>
                <x-input id="email" class="block w-full mt-1" type="email" autofocus wire:model='email' autocomplete="off"/>
                <x-input-error for="email" class="mt-2" />
            </div>

            <div>
                <x-label for="phone" value="{{ __('modules.user.phone') }}" />

                <div class="flex items-center mt-1 space-x-2">
                    <!-- Show selected flag -->
                    @if(isset($selectedCountry) && $selectedCountry->flagUrl)
                        <img src="{{ $selectedCountry->flagUrl }}" class="w-5 h-5 border border-gray-200 rounded-full" alt="Flag">
                    @endif

                    <!-- Country Code Dropdown -->
                    <x-select id="country_code" class="" wire:model="countryCode" wire:change="updateCountryName">
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

            <div x-data="{ photoName: null, photoPreview: @if($tenant && $tenant->user && $tenant->user->profile_photo_path) '{{($tenant->user->profile_photo_url) }}' @else null @endif }" x-on:photo-removed.window="photoName = null; photoPreview = null;">
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
                    {{ __('modules.tenant.uploadProfilePicture') }}
                </x-secondary-button>

                <div class="mt-2" x-show="photoPreview">
                    <span class="block w-20 h-20 bg-center bg-no-repeat bg-cover rounded-full"
                        x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <div class="mt-2" x-show="photoPreview">
                    <x-danger-button type="button" wire:click="removeProfilePhoto">
                        {{ __('modules.tenant.removeProfilePicture') }}
                    </x-danger-button>
                </div>

                <x-input-error for="profilePhoto" class="mt-2" />
            </div>

        </div>
        <p class="flex items-center mt-4 text-sm text-gray-700 dark:text-gray-300">
            <x-alert type="info">@lang('messages.updateApartmentFromDetailsPage')</x-alert>
        </p>
        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideAddTenant')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
