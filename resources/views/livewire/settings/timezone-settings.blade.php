<div
    class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm 2xl:col-span-2 dark:border-gray-700 sm:p-6 dark:bg-gray-800">
    @if(isRole() == 'Admin')
        <x-cron-message :modal="false" :showModal="false" />
    @endif

    <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.appSettings')</h3>

    <form wire:submit="submitForm">
        <div class="grid gap-6">

            <div>
                <x-label for="societyCountry" :value="__('modules.settings.societyCountry')" />
                <x-select id="societyCountry" class="block w-full mt-1" wire:model.live="societyCountry">
                    @foreach ($countries as $item)
                    <option value="{{ $item->id }}">{{ $item->countries_name }}</option>
                    @endforeach
                </x-select>
            </div>

            <div>
                <x-label for="societyTimezone" :value="__('modules.settings.societyTimezone')" />
                <x-select id="societyTimezone" class="block w-full mt-1" wire:model="societyTimezone">
                    @foreach ($timezones as $tz)
                        @php
                            $dateTime = new DateTime('now', new DateTimeZone($tz));
                            $offset = $dateTime->format('P');
                        @endphp
                        <option value="{{ $tz }}">{{ $tz }} (UTC {{ $offset }})</option>
                    @endforeach
                </x-select>
                <x-input-error for="societyTimezone" class="mt-2" />
            </div>

            <div>
                <x-label for="societyCurrency" :value="__('modules.settings.societyCurrency')" />
                <x-select id="societyCurrency" class="block w-full mt-1" wire:model="societyCurrency">
                    @foreach ($currencies as $item)
                        <option value="{{ $item->id }}">{{ $item->currency_name . ' ('.$item->currency_code.')' }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="societyCurrency" class="mt-2" />
            </div>

            <!-- Pwa Settings Section -->
            <div class="p-6 rounded-lg md:col-span-2 bg-gray-50 dark:bg-gray-700/50">
                <h4 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                    @lang('modules.settings.pwaSettings')
                </h4>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <div class="flex items-center p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="flex-1">
                            <x-label for="pwaAlertShow" :value="__('modules.settings.enbalePwaApp')" class="!mb-1" />
                            <p class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.enablePwadescription')</p>
                        </div>
                        <x-checkbox name="pwaAlertShow" id="pwaAlertShow" wire:model='pwaAlertShow' class="ml-4" />
                    </div>
                </div>

            </div>
            <div>
                <x-button>@lang('app.save')</x-button>
            </div>
        </div>
    </form>
</div>
