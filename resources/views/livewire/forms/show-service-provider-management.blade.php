<div class="card">
    <div class="w-full p-8 text-gray-800 bg-white border rounded-lg shadow-lg dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700">
        <div class="space-y-4">

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.serviceManagement.serviceType') }}</strong>
                <span>{{ $this->serviceManagementShow->serviceType->name }}</span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.serviceManagement.contactPersonName') }}</strong>
                <span>{{ $this->serviceManagementShow->contact_person_name }}</span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.serviceManagement.contactNumber') }}</strong>
                <span>
                    @if ($this->serviceManagementShow->phone_number)
                        @if ($this->serviceManagementShow->country_phonecode)
                            +{{ $this->serviceManagementShow->country_phonecode }} {{ $this->serviceManagementShow->phone_number }}
                        @else
                            {{ $this->serviceManagementShow->phone_number }}
                        @endif
                    @else
                        --
                    @endif
                </span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.settings.status') }}</strong>
                <span>
                    @if($this->serviceManagementShow->status === 'available')
                        <x-badge type="success">{{ __('app.' .$this->serviceManagementShow->status) }}</x-badge>
                    @else
                        <x-badge type="danger">{{ __('app.' .$this->serviceManagementShow->status) }}</x-badge>
                    @endif
                </span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.serviceManagement.price') }}</strong>
                <span>
                    @if($this->serviceManagementShow->price != 0)
                        {{ currency_format($this->serviceManagementShow->price) }}
                    @else
                        @lang('modules.serviceManagement.notDisclosed')
                    @endif
                </span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.serviceManagement.paymentFrequency') }}</strong>
                <span>
                    <x-badge type="secondary">{{ __('app.'.$this->serviceManagementShow->payment_frequency) }}</x-badge>
                </span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.settings.dailyHelp') }}</strong>
                <span>
                    @if($this->serviceManagementShow->daily_help == 1)
                        @lang('app.yes')
                    @else
                        @lang('app.no')
                    @endif
                </span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.serviceManagement.websiteLink') }}</strong>
                <span>
                    @if ($this->serviceManagementShow->website_link)
                        <a href="{{ $this->serviceManagementShow->website_link }}" target="_blank" class="text-blue-600 break-all dark:text-blue-400 hover:underline">
                            {{ $this->serviceManagementShow->website_link }}
                        </a>
                    @else
                        <span class="text-xs text-gray-500 dark:text-gray-400">--</span>
                    @endif
                </span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.serviceManagement.companyName') }}</strong>
                <span>{{ $this->serviceManagementShow->company_name ?? '--'}}</span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.settings.apartment') }}</strong>
                <span>
                    @if($this->serviceManagementShow->apartmentManagements->pluck('apartment_number')->join(', '))
                        {{ $this->serviceManagementShow->apartmentManagements->pluck('apartment_number')->join(', ') }}
                    @else
                        <span class="dark:text-gray-400">--</span>
                    @endif
                </span>
            </p>

            <p class="flex justify-between pb-2 border-b border-gray-300 dark:border-gray-600">
                <strong class="text-gray-600 dark:text-gray-400">{{ __('modules.serviceManagement.description') }}</strong>
                <span class="flex-1 text-right">{{ $this->serviceManagementShow->description }}</span>
            </p>
        </div>

        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button-cancel wire:click="$dispatch('hideShowServiceManagement')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </div>
</div>
