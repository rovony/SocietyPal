<div>
    <div
        class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.societyInformation')</h3>
        <x-help-text class="mb-6">@lang('modules.settings.generalHelp')</x-help-text>

        <form wire:submit="submitForm">
            <div class="grid gap-6">
                <div>
                    <x-label for="societyName" value="{{ __('modules.settings.societyName') }}" required="true" />
                    <x-input id="societyName" class="block w-full mt-1" type="text" placeholder="{{ __('placeholders.societyNamePlaceHolder') }}" autofocus wire:model='societyName' />
                    <x-input-error for="societyName" class="mt-2" />
                </div>

                <div>
                    <x-label for="societyPhoneNumber" value="{{ __('modules.settings.societyPhoneNumber') }}" required="true" />
                    <x-input id="societyPhoneNumber" class="block w-full mt-1 rtl:text-right" placeholder="{{ __('placeholders.societyPhoneNumberPlaceHolder') }}" type="tel" wire:model='societyPhoneNumber' />
                    <x-input-error for="societyPhoneNumber" class="mt-2" />
                </div>

                <div>
                    <x-label for="societyEmailAddress" value="{{ __('modules.settings.societyEmailAddress') }}" required="true" />
                    <x-input id="societyEmailAddress" class="block w-full mt-1" placeholder="{{ __('placeholders.societyEmailAddressPlaceHolder') }}" type="email"  wire:model='societyEmailAddress' />
                    <x-input-error for="societyEmailAddress" class="mt-2" />
                </div>

                <div>
                    <x-label for="societyAddress" value="{{ __('modules.settings.societyAddress') }}" required="true" />
                    <x-textarea class="block w-full mt-1" placeholder="{{ __('placeholders.societyAddressPlaceHolder') }}" wire:model='societyAddress' rows='3' />
                    <x-input-error for="societyAddress" class="mt-2" />
                </div>

                <div>
                    <x-button>@lang('app.save')</x-button>
                </div>
            </div>
        </form>
    </div>

</div>
