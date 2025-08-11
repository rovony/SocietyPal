<div>
    <form wire:submit.prevent="updateUser">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="name" value="{{ __('modules.user.name') }}" required/>
                <x-input id="name" class="block w-full mt-1" type="text" wire:model='name' autocomplete="off"/>
                <x-input-error for="name" class="mt-2" />
            </div>

            <div>
                <x-label for="email" value="{{ __('modules.user.email') }}" required/>
                <x-input id="email" class="block w-full mt-1" type="email" wire:model='email' autocomplete="off"/>
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

            </div>

            <div>
                <x-label for="role" value="{{  __('modules.user.role') }}" required/>
                    @if($user->id === $firstUserId || $user->id == user()->id)
                        <x-input id="role" class="block w-full mt-1" type="text" value="{{ $user->role->display_name }}" readonly />
                    @else
                        <x-select class="block w-full mt-1" wire:model='role'>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->display_name }}</option>
                            @endforeach
                        </x-select>
                    @endif
                <x-input-error for="role" class="mt-2" />
            </div>

            <div>
                <x-label for="status" value="{{ __('modules.user.status') }}" />
                @if($user->id === $firstUserId || $user->id == user()->id)
                    <x-input id="status" class="block w-full mt-1" type="text" value="{{ __('app.' . $user->status) }}" readonly />
                @else
                    <x-select id="status" class="block w-full mt-1" wire:model.live="status">
                        <option value="active">{{ __('modules.user.active') }}</option>
                        <option value="inactive">{{ __('modules.user.inactive') }}</option>
                    </x-select>
                @endif
                <x-input-error for="status" class="mt-2" />
            </div>

            <div x-data="{ photoName: null, photoPreview: null, hasNewPhoto: @entangle('hasNewPhoto').live , clearFileInput() { this.photoName = ''; this.photoPreview = ''; this.         hasNewPhoto = false; this.$refs.photo.value = ''; @this.set('teamLogo', ''); } }" class="col-span-6 sm:col-span-4">
                <input type="file" class="hidden"
                            wire:model="profilePhoto"
                            accept="image/png, image/gif, image/jpeg, image/webp"
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

                <x-label for="photoName" value="{{ __('Profile Photo') }}" />

                <x-secondary-button class="mt-2 me-2" type="button" x-on:click.prevent="$refs.profilePhoto.click()">
                    {{ __('modules.user.uploadProfilePicture') }}
                </x-secondary-button>

                @if ($user->profile_photo_path)
                <div class="mt-2" x-show="! photoPreview">
                    <img src="{{ $user->profile_photo_url }}" alt="{{ 'Hello' }}" class="object-cover w-20 h-20 overflow-hidden rounded-full">
                </div>
                @endif

                <div class="mt-2" x-show="photoPreview" style="display: none;">
                    <span class="block w-20 h-20 bg-center bg-no-repeat bg-cover rounded-full"
                            x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                    </span>
                </div>

                <x-secondary-button class="mt-2" type="button" x-on:click.prevent="clearFileInput()" x-show="hasNewPhoto" x-cloak>
                    {{ __('modules.user.removeProfilePicture') }}
                </x-secondary-button>

                @if ($user->profile_photo_path)
                    <x-danger-button type="button" class="mt-2" wire:click="removeProfilePhoto" x-on:click.prevent="clearFileInput()" x-show="!hasNewPhoto" x-cloak>
                        {{ __('modules.user.removeProfilePicture') }}
                    </x-danger-button>
                @endif
            </div>

            <div class="flex items-center mt-8 space-x-4">
                <x-button type="submit" class="text-white bg-blue-500 hover:bg-blue-600">
                    @lang('app.update')
                </x-button>
                <x-button-cancel  wire:click="$dispatch('hideEditUser')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>

            </div>
        </div>
    </form>
</div>
