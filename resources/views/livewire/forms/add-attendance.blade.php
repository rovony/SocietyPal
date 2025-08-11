<div>
    <form wire:submit="SubmitForm">
        @csrf
        <div class="space-y-4">
            <div>
                <x-label for="service_type_id" value="{{ __('modules.serviceManagement.selectServiceType') }}" required/>
                <x-select class="mt-1 block w-full" wire:model.live='service_type_id'>
                    <option value="">@lang('modules.serviceManagement.selectServiceType')</option>
                    @foreach ($serviceTypes as $serviceType)
                        <option value="{{ $serviceType->id }}">{{ $serviceType->name }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="service_type_id" class="mt-2" />
            </div>
            
            <div>
                <x-label for="service_management_id" value="{{ __('modules.serviceManagement.selectServiceProvider') }}" required/>
                <x-select  class="mt-1 block w-full" wire:model.live='service_management_id'>
                    <option value="">@lang('modules.serviceManagement.selectServiceProvider')</option>
                    @foreach ($serviceProviders as $service)
                        <option value="{{ $service->id }}">{{ $service->contact_person_name }}</option>
                    @endforeach
                </x-select>
                <x-input-error for="service_management_id" class="mt-2" />
            </div>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <x-label for="clock_in_date" value="{{ __('modules.serviceManagement.clockInDate') }}" required="true"/>
                    <x-datepicker class="block w-full mt-1" wire:model.live="clock_in_date" id="clock_in_date" autocomplete="off" placeholder="{{ __('modules.serviceManagement.clockInDate') }}" :maxDate="true"/>
                    <x-input-error for="clock_in_date" class="mt-2" />
                </div>
                <div class="w-1/2">
                    <x-label for="clock_in_time" value="{{ __('modules.serviceManagement.clockInTime') }}" required="true"/>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input type="time" id="clock_in_time" wire:model.live='clock_in_time' class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500" min="00:00" max="23:59" value="00:00" />
                    </div>
                    <x-input-error for="clock_in_time" class="mt-2" />
                </div>
                
            </div>

            <div class="flex justify-center w-full pb-4 mt-9">
                <x-button>@lang('app.clockIn')</x-button>
            </div>
        </div>
    </form>
</div>
