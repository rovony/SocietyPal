<div class="flex flex-col mb-12">
    <div class="overflow-x-auto ">
        @if(user_can('Create Tenant'))
            <div class="mb-4">
                <x-button type='button' wire:click="$set('showAddApartmentTenant', true)">@lang('app.add')</x-button>
            </div>
        @endif
        <div class="inline-block min-w-full align-middle">
            <div class="overflow-hidden shadow">
                <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                #
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.apartmentNumber')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.settings.ownerName')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.tenant.contractStartDate')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.tenant.contractEndDate')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('app.status')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.tenant.rentAmount')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.tenant.rentBillingCycle')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.tenant.moveInDate')
                            </th>
                            <th scope="col"
                                class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                @lang('modules.tenant.moveOutDate')
                            </th>
                            @if(user_can('Update Tenant') || user_can('Delete Tenant'))
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>

                        @forelse ($tenant->apartments as $apartment)
                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='apartment-{{ $apartment->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $loop->index+1 }}
                            </td>

                            <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                <a class="text-base font-semibold hover:underline dark:text-black-400" href="{{ route('apartments.show' , $apartment->id)}}">
                                    {{$apartment->apartment_number}}
                                </a>
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $apartment->user->name ?? '--' }}
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $apartment->pivot->contract_start_date ? \Carbon\Carbon::parse($apartment->pivot->contract_start_date)->format('d M Y') : '--' }}
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $apartment->pivot->contract_end_date ? \Carbon\Carbon::parse($apartment->pivot->contract_end_date)->format('d M Y') : '--' }}
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                @if(isset($apartment->pivot->status) && $apartment->pivot->status === 'current_resident')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                        @lang('modules.tenant.currentResident')
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                        {{ ucfirst($apartment->pivot->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $apartment->pivot->rent_amount ? currency_format($apartment->pivot->rent_amount) : '--' }}
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ ucFirst($apartment->pivot->rent_billing_cycle) }}
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $apartment->pivot->move_in_date ? \Carbon\Carbon::parse($apartment->pivot->move_in_date)->format('d M Y') : '--' }}
                            </td>
                            <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $apartment->pivot->move_out_date ? \Carbon\Carbon::parse($apartment->pivot->move_out_date)->format('d M Y') : '--' }}
                            </td>
                            @if(user_can('Update Tenant') || user_can('Delete Tenant'))
                            <td class="p-4 space-x-2 whitespace-nowrap text-right">
                                @if(user_can('Update Tenant'))
                                    <x-secondary-button wire:click="showEditApartmentTenant({{ $apartment->id }})" wire:key="apartment-tenant-edit-{{ $apartment->id . microtime() }}">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                            </path>
                                            <path fill-rule="evenodd"
                                                d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </x-secondary-button>
                                @endif

                                @if(user_can('Delete Tenant'))
                                    <x-danger-button  wire:click="showDeleteApartmentTenant({{ $apartment->id }})"  wire:key='apartment-tenant-del-{{ $apartment->id . microtime() }}'>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </x-danger-button>
                                @endif
                            </td>
                        @endif
                        </tr>
                        @empty
                            <x-no-results :message="__('messages.noApartmentFound')" />
                        @endforelse

                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <x-dialog-modal wire:model.live="showAddApartmentTenant">
        <x-slot name="title">
            {{ __("modules.settings.addApartmentManagement") }}
        </x-slot>
        <x-slot name="content">
            @livewire('tenants.add-apartment-tenant',['tenantId' => $tenantId], key(str()->random(50)))
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showAddApartmentTenant', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showEditApartmentTenantModal">
        <x-slot name="title">
            {{ __("modules.settings.editSocietyApartmentManagement") }}
        </x-slot>
        <x-slot name="content">
            @if ($selectedApartmentForEdit)
                @livewire('tenants.editApartmentTenant', ['tenantId' => $tenantId, 'apartmentId' => $selectedApartmentForEdit], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditApartmentTenantModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <x-confirmation-modal wire:model="showDeleteApartmentTenantModal">
        <x-slot name="title">
            @lang('modules.settings.deleteApartmentManagement')
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('showDeleteApartmentTenantModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>
            @if ($selectedApartment)
            <x-danger-button class="ml-3" wire:click='deleteApartmentTenant({{ $selectedApartment->id }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>
</div>
