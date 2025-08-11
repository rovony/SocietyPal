<div>
    <form wire:submit.prevent="submitForm">
        @csrf

        <div class="mt-4">
            <x-label for="societyName" value="{{ __('saas.societyName') }}" required/>
            <x-input id="societyName" class="block mt-1 w-full" type="text" wire:model.defer='societyName'/>
            <x-input-error for="societyName" class="mt-2"/>
        </div>
        @includeIf('subdomain::super-admin.society.subdomain-field', ['society' => $society])
        <div class="mt-4">
            <x-label for="email" value="{{ __('saas.email') }}" required/>
            <x-input id="email" class="block mt-1 w-full" type="email" wire:model.defer='email'/>
            <x-input-error for="email" class="mt-2"/>
        </div>

        <div class="flex space-x-4 mt-4">
            <div class="w-1/2">
                <x-label for="phone" value="{{ __('saas.phone') }}" />
                <x-input id="phone" class="block mt-1 w-full" type="tel" wire:model='phone' />
                <x-input-error for="phone" class="mt-2" />
            </div>

            <div class="w-1/2">
                <x-label for="country" value="{{ __('saas.country') }}"/>
                <x-select id="country" class="mt-1 block w-full" wire:model="country">
                    @foreach ($countries as $item)
                        <option value="{{ $item->id }}" @selected($item->id == $country)>
                            {{ $item->countries_name }}
                        </option>
                    @endforeach
                </x-select>
                <x-input-error for="country" class="mt-2"/>
            </div>
        </div>

        <div class="mt-4">
            <x-label for="address" value="{{ __('saas.address') }}" required/>
            <x-textarea id="address" class="block mt-1 w-full" rows="3" wire:model.defer='address'/>
            <x-input-error for="address" class="mt-2"/>
        </div>

        <div class="mt-4">
            <x-label for="facebook" value="{{ __('saas.facebook_link') }}" />
            <x-input id="facebook" class="block mt-1 w-full" type="url"
               autofocus wire:model='facebook' />
            <x-input-error for="facebook" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-label for="instagram" value="{{ __('saas.instagram_link') }}" />
            <x-input id="instagram" class="block mt-1 w-full" type="url"
                autofocus wire:model='instagram' />
            <x-input-error for="instagram" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-label for="twitter" value="{{ __('saas.twitter_link') }}" />
            <x-input id="twitter" class="block mt-1 w-full" type="url"
               autofocus wire:model='twitter' />
            <x-input-error for="twitter" class="mt-2" />
        </div>


        <div class="mt-4">
            <x-label for="status" value="{{ __('app.status') }}"/>
            <x-select id="status" class="mt-1 block w-full" wire:model="status">
                <option value="1">{{ __('app.active') }}</option>
                <option value="0">{{ __('app.inactive') }}</option>
            </x-select>
            <x-input-error for="status" class="mt-2"/>
        </div>

        <div class="flex w-full pb-4 space-x-4 mt-6">
            <x-button>{{ __('app.update') }}</x-button>
            <x-button-cancel  wire:click="$dispatch('hideEditSociety')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
