<div>

    <form wire:submit="updateMaintenance">
        @csrf
        <div class="space-y-4">


            <div>
                <x-label for="maintenance_date" value="{{ __('modules.assets.maintenanceDate') }}" required />
                <x-datepicker class="w-full" wire:model="maintenance_date" id="maintenance_date" autocomplete="off" placeholder="{{ __('modules.assets.maintenanceDate') }}" />
                <x-input-error for="maintenance_date" class="mt-2" />
            </div>

            <div>
                <x-label for="schedule" value="{{ __('modules.assets.maintenanceSchedule') }}" required />
                <x-select id="schedule" class="block w-full mt-1" wire:model="schedule">
                    <option value="">{{ __('modules.assets.selectMaintenanceSchedule') }}</option>
                    <option value="weekly">{{ __('modules.assets.weekly') }}</option>
                    <option value="biweekly">{{ __('modules.assets.biweekly') }}</option>
                    <option value="monthly">{{ __('modules.assets.monthly') }}</option>
                    <option value="half-year">{{ __('modules.assets.halfYearly') }}</option>
                    <option value="yearly">{{ __('modules.assets.yearly') }}</option>
                </x-select>
                <x-input-error for="schedule" class="mt-2" />
            </div>

            <div>
                <x-label for="reminder" value="{{ __('modules.assets.enableAutoReminder') }}" />
                <x-checkbox id="reminder" wire:model="reminder" />
                <span class="ml-2 text-sm text-gray-600">{{ __('modules.assets.enableAutoReminderHelp') }}</span>
            </div>

            <div>
                <x-label for="status" value="{{ __('modules.assets.status') }}" required />
                <x-select id="status" class="block w-full mt-1" wire:model="status">
                    <option value="">{{ __('modules.assets.selectStatus') }}</option>
                    <option value="pending">{{ __('modules.assets.pending') }}</option>
                    <option value="completed">{{ __('modules.assets.completed') }}</option>
                    <option value="overdue">{{ __('modules.assets.overdue') }}</option>
                </x-select>
                <x-input-error for="status" class="mt-2" />
            </div>

            <div>
                <x-label for="amount" value="{{ __('modules.assets.amount') }}" />
                <x-input id="amount" class="block w-full mt-1" type="number" step="0.01" wire:model='amount' autocomplete="off" placeholder="{{ __('modules.assets.enterAmount') }}"/>
                <x-input-error for="amount" class="mt-2" />
            </div>

            <div>
                <x-label for="serviceId" :value="__('modules.serviceManagement.selectServiceProvider')" />
                <x-select id="serviceId" class="block w-full mt-1" wire:model="serviceId">
                    <option value="">@lang('modules.serviceManagement.selectServiceProvider')</option>
                    @foreach ($serviceManagement as $service)
                        <option value="{{ $service->id }}">{{ $service->contact_person_name}}</option>
                    @endforeach
                </x-select>
                <x-input-error for="serviceId" class="mt-2" />
            </div>


            <div>
                <x-label for="notes" value="{{ __('modules.assets.notes') }}" />
                <x-textarea id="notes" class="block w-full mt-1" wire:model='notes' />
                <x-input-error for="notes" class="mt-2" />
            </div>

            <div class="flex justify-end w-full pb-4 space-x-4 mt-9">
                <x-button>@lang('app.update')</x-button>
            </div>

        </div>
    </form>
</div>
