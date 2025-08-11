<div>
    <form wire:submit="updateApartment">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="apartment_id" value="{{ __('modules.settings.apartmentNumber') }}" required/>
                <x-input class="mt-1 block w-full" id="apartment_id" 
                value="{{ $apartmentRented->firstWhere('id', $apartment_id)->apartment_number ?? '' }}" 
                readonly 
            />
                <x-input-error for="apartment_id" class="mt-2" />
            </div>

            <div class="mt-2">
                <x-label for="status" value="{{ __('modules.settings.status') }}" />
                <x-select id="status" wire:model="status" class="block w-full mt-1">
                    <option value="current_resident">{{ __('modules.tenant.currentResident') }}</option>
                    <option value="left">{{ __('modules.tenant.left') }}</option>
                </x-select>
                <x-input-error for="status" class="mt-2" />
            </div>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <x-label for="contract_start_date" value="{{ __('modules.tenant.contractStartDate') }}" required/>
                    <x-datepicker class="w-full" wire:model.live="contract_start_date" id="contract_start_date" autocomplete="off" placeholder="{{ __('modules.tenant.contractStartDate') }}" />
                    <x-input-error for="contract_start_date" class="mt-2" />
                </div>

                <div class="w-1/2">
                    <x-label for="contract_end_date" value="{{ __('modules.tenant.contractEndDate') }}" required/>
                    <x-datepicker class="w-full" wire:model.live="contract_end_date" id="contract_end_date" autocomplete="off" placeholder="{{ __('modules.tenant.contractEndDate') }}" />
                    <x-input-error for="contract_end_date" class="mt-2" />
                </div>
            </div>

            <div class="flex space-x-4 mt-2">
                <div class="w-1/2">
                    <x-label for="rent_billing_cycle" value="{{ __('modules.tenant.rentBillingCycle') }}" />
                    <x-select id="rent_billing_cycle" class="block w-full mt-1" wire:model.live="rent_billing_cycle">
                        <option value="monthly">{{ __('modules.tenant.monthly') }}</option>
                        <option value="annually">{{ __('modules.tenant.annually') }}</option>
                    </x-select>
                    <x-input-error for="rent_billing_cycle" class="mt-2" />
                </div>
                <div class="w-1/2">
                    <x-label for="rent_amount" value="{{ __('modules.tenant.rentAmount') }}" />
                    <x-input id="rent_amount" class="block mt-1 w-full" type="number" step="0.01" wire:model='rent_amount' autocomplete="off"/>
                    <x-input-error for="rent_amount" class="mt-2" />
                </div>
            </div>

            <div class="flex space-x-4 mt-2">
                <div class="w-1/2">
                    <label for="move_in_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                        {{ __('modules.tenant.moveInDate') }}
                    </label>
                    <x-datepicker class="w-full" wire:model.live="move_in_date" id="move_in_date" autocomplete="off" placeholder="{{ __('modules.tenant.moveInDate') }}" />
                    <x-input-error for="move_in_date" class="mt-2" />
                </div>

                <div class="w-1/2">
                    <label for="move_out_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">
                        {{ __('modules.tenant.moveOutDate') }}
                    </label>
                    <x-datepicker class="w-full" wire:model.live="move_out_date" id="move_out_date" autocomplete="off" placeholder="{{ __('modules.tenant.moveOutDate') }}" />
                    <x-input-error for="move_out_date" class="mt-2" />
                </div>
            </div>
           
            @if($disableStatusSelect)
                <p class="mt-4 text-sm flex items-center text-gray-700 dark:text-gray-300">
                    <x-alert type="danger">@lang('messages.apartmentAlreadyRented')</x-alert>
                </p>
            @endif
            <div class="flex justify-end w-full pb-4 space-x-4 mt-9">
                <x-button>@lang('app.save')</x-button>
            </div>
        </div>
    </form>
</div>
