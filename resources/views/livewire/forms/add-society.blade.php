<div>


    <form wire:submit="submitForm">
        @csrf

        <div class="mt-4">
            <x-label for="SocietyName" value="{{ __('saas.societyName') }}" required/>
            <x-input id="SocietyName" class="block mt-1 w-full" type="text" wire:model='SocietyName'/>
            <x-input-error for="SocietyName" class="mt-2"/>
        </div>

        @includeIf('subdomain::super-admin.society.subdomain-field')

        <div class="mt-4">
            <x-label for="contactName" value="{{ __('saas.fullName') }}" required/>
            <x-input id="contactName" class="block mt-1 w-full" type="text" wire:model='contactName'/>
            <x-input-error for="contactName" class="mt-2"/>
        </div>

        <div class="mt-4">
            <x-label for="email" value="{{ __('saas.email') }}" required/>
            <x-input id="email" class="block mt-1 w-full" type="text" wire:model='email'/>
            <x-input-error for="email" class="mt-2"/>
        </div>

        <div class="mt-4">
            <x-label for="password" value="{{ __('saas.password') }}" required/>
            <x-input id="password" class="block mt-1 w-full" type="password" autocomplete="new-password"
                wire:model='password' />
            <x-input-error for="password" class="mt-2" />
        </div>

        <div class="flex space-x-4 mt-4">
            <div class="w-1/2">
                <x-label for="phoneNumber" value="{{ __('saas.phone') }}" />
                <x-input id="phoneNumber" class="block mt-1 w-full" type="tel" wire:model='phoneNumber' />
                <x-input-error for="phoneNumber" class="mt-2" />
            </div>

            <div class="w-1/2">
                <x-label for="country" value="{{ __('saas.country') }}"/>
                <x-select id="societyCountry" class="mt-1 block w-full" wire:model="country">
                    @foreach ($countries as $item)
                        <option value="{{ $item->id }}">{{ $item->countries_name }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="country" class="mt-2"/>
            </div>
        </div>


        <div class="mt-4">
            <x-label for="facebook" value="{{ __('saas.facebook_link') }}"/>
            <x-input id="facebook" class="block mt-1 w-full" type="url"
               autofocus wire:model='facebook' />
            <x-input-error for="facebook" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-label for="instagram" value="{{ __('saas.instagram_link') }}"/>
            <x-input id="instagram" class="block mt-1 w-full" type="url"
                autofocus wire:model='instagram' />
            <x-input-error for="instagram" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-label for="twitter" value="{{ __('saas.twitter_link') }}"/>
            <x-input id="twitter" class="block mt-1 w-full" type="url"
               autofocus wire:model='twitter' />
            <x-input-error for="twitter" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-label for="address" value="{{ __('saas.address') }}" required/>
            <x-textarea id="address" class="block mt-1 w-full" rows="2" wire:model='address'/>
            <x-input-error for="address" class="mt-2"/>
        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideAddSociety')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>


</div>
