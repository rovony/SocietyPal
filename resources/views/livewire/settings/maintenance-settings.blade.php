<div>
    <div class="p-4 mx-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
        <h3 class="mb-4 text-xl font-semibold dark:text-white">@lang('modules.settings.maintenanceSettings')</h3>
        <form wire:submit.prevent="submitForm">
            <div class="grid gap-6">
                <div class="mb-4">
                    <x-label for="maintenanceCostType" value="{{ __('modules.settings.maintenanceCostType') }}" class="mb-2 text-sm font-medium text-gray-900 dark:text-white" required="true" />
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-primary-500 dark:border-gray-700 dark:hover:border-primary-500 {{ $costType === 'fixedValue' ? 'bg-primary-50 border-primary-500 dark:bg-primary-900/50 dark:border-primary-500' : '' }}">
                            <input type="radio" wire:model.live="costType" value="fixedValue" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                            <div class="flex-1 ml-3">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('modules.settings.fixedValue') }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('modules.settings.fixedValueDescription') }}</p>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-primary-500 dark:border-gray-700 dark:hover:border-primary-500 {{ $costType === 'unitType' ? 'bg-primary-50 border-primary-500 dark:bg-primary-900/50 dark:border-primary-500' : '' }}">
                            <input type="radio" wire:model.live="costType" value="unitType" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600">
                            <div class="flex-1 ml-3">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('modules.settings.unitType') }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('modules.settings.unitTypeDescription') }}</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Fixed Value Section -->
                @if($costType === "fixedValue")
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th class="p-4 text-base font-semibold text-left text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ __('modules.settings.apartmentType') }}
                                    </th>
                                    <th class="p-4 text-base font-semibold text-left text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ __('modules.settings.maintenanceSetValue') }} ({{ society()->currency->currency_symbol }})
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($apartments as $index => $apartment)

                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key="member-{{ $apartment['id']. rand(1111, 9999) . microtime() }}" wire:loading.class.delay="opacity-10">
                                        <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $apartment['apartment_type'] }}
                                        </td>
                                        <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                            <x-input  id="apartment-{{ $apartment['id'] }}" wire:model.lazy="apartments.{{ $index }}.maintenance_value" type="number" step="0.01" />
                                            <x-input-error for="apartments.{{ $index }}.maintenance_value" class="mt-2" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div>
                            <x-button class="mt-3" wire:click="updateApartment({{ $index }})">@lang('app.save')</x-button>
                        </div>
                    </div>
                @endif

                <!-- Unit Type Section -->
                @if($costType === "unitType")
                    <div>
                        <div class="flex space-x-4">
                            <!-- Maintenance Unit Name Field -->
                            <div class="flex-1">
                                <x-label for="unitName" value="{{ __('modules.settings.maintenanceUnitName') }}" required="true" />
                                <x-input id="unitName" class="block w-full mt-1" type="text" placeholder="{{ __('placeholders.maintenanceUnitNamePlaceHolder') }}" autofocus wire:model="unitName" />
                                <x-input-error for="unitName" class="mt-2" />
                            </div>

                            <!-- Maintenance Unit Type Field -->
                            <div class="flex-1">
                                <x-label for="unitType" value="{{ __('modules.settings.maintenanceSetValue') }} ({{ society()->currency->currency_symbol }})" required="true" />
                                <x-input id="unitType" class="block w-full mt-1" type="number" step="0.01" placeholder="{{ __('placeholders.maintenanceUnitTypePlaceHolder') }}" autofocus wire:model="setValue" />
                                <x-input-error for="setValue" class="mt-2" />
                            </div>
                        </div>
                        <div>
                            <x-button class="mt-3" wire:click="updateUnitType">@lang('app.save')</x-button>
                        </div>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>

