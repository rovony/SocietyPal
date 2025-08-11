<div class="px-4 pt-2 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">
    @if ($showFilters)
        @include('service-management.filters')
    @endif
    <div class="flex items-center justify-end">
        @if($showActions)
            <x-dropdown dropdownClasses="z-50">
                <x-slot name="trigger">
                    <span class="inline-flex">
                        <button type="button"
                            class="inline-flex items-center justify-center p-2 mb-2 text-sm font-medium text-gray-500 transition duration-150 ease-in-out bg-white border border-gray-300 hover:text-gray-700 focus:outline-none hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                            @lang('app.action')
                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </span>
                </x-slot>
                <x-slot name="content">
                        <x-dropdown-link href="javascript:;" wire:click="showSelectedDelete">
                            <span class="inline-flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                @lang('app.delete')
                            </span>
                        </x-dropdown-link>
                </x-slot>
            </x-dropdown>
        @endif
    </div>
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                @if (user_can('Delete Service Provider'))
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </th>
                                @endif
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.serviceManagement.contactPerson')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.serviceManagement.service')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.serviceManagement.contactNumber')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.serviceManagement.status')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.dailyHelpAvailability')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.serviceManagement.price')
                                </th>
                                @if(user_can('Update Service Provider') || user_can('Delete Service Provider'))
                                    <th scope="col"
                                        class="p-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                        @lang('app.action')
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>
                            @forelse ($serviceData as $item)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='member-{{ $item->id }}'>
                                @if (user_can('Delete Service Provider'))
                                    <td class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        <input type="checkbox" wire:model.live="selected" value="{{ $item->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </td>
                                @endif
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    @if (user_can('Show Service Provider'))
                                    <a href="javascript:;" wire:click="showServiceManagement({{ $item->id }})" class="text-base font-semibold hover:underline dark:text-gray-400">
                                        <x-avatar-image :src="$item->service_photo_url" :alt="$item->contact_person_name" :name="$item->contact_person_name"/>
                                    </a>
                                    @else
                                        <x-avatar-image :src="$item->service_photo_url" :alt="$item->contact_person_name" :name="$item->contact_person_name"/>
                                    @endif
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->serviceType->name }}
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    @if ($item->phone_number)
                                        @if ($item->country_phonecode)
                                            +{{ $item->country_phonecode }} {{ $item->phone_number }}
                                        @else
                                            {{ $item->phone_number }}
                                        @endif
                                    @else
                                        --
                                    @endif
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($item->status === 'available')
                                        <x-badge type="success">{{ __('app.' . $item->status) }}</x-badge>
                                    @else
                                        <x-badge type="danger">{{ __('app.' . $item->status) }}</x-badge>
                                    @endif
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($item->daily_help == 1)
                                        <x-badge type="success">@lang('app.yes')</x-badge>
                                    @else
                                        <x-badge type="danger">@lang('app.no')</x-badge>
                                    @endif
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    <div class="text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        {{ $item->price ?   currency_format($item->price) : __('modules.serviceManagement.notDisclosed') }}
                                    </div>
                                    <div class="mt-1">
                                        <x-badge type="secondary">{{ __('app.' . $item->payment_frequency) }}</x-badge>
                                    </div>
                                </td>
                                @if(user_can('Update Service Provider') || user_can('Delete Service Provider'))
                                    <td class="p-4 space-x-2 text-right whitespace-nowrap">
                                        @if (user_can('Update Service Provider'))
                                            <x-secondary-button wire:click='showEditServiceManagement({{ $item->id }})' wire:key='member-edit-{{ $item->id . microtime() }}'
                                                wire:key='editmenu-item-button-{{ $item->id }}'>
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                    </path>
                                                    <path fill-rule="evenodd"
                                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                @lang('app.update')
                                            </x-secondary-button>
                                        @endif
                                        @if (user_can('Delete Service Provider'))
                                            <x-danger-button  wire:click="showDeleteServiceManagement({{ $item->id }})"  wire:key='member-del-{{ $item->id . microtime() }}'>
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
                                <x-no-results :message="__('messages.noServiceFound')" />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div wire:key='service-management-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-4 sm:mb-0">
            {{ $serviceData->links() }}
        </div>
    </div>

    <x-right-modal wire:model.live="showEditServiceManagementModal">
        <x-slot name="title">
            {{ __("modules.serviceManagement.editServiceManagement") }}
        </x-slot>

        <x-slot name="content">
            @if ($serviceManagementEdit)
        @livewire('forms.editServiceProviderManagement', ['serviceManagement' => $serviceManagementEdit], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditServiceManagementModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-right-modal wire:model.live="showServiceManagementModal">
        <x-slot name="title">
            {{ __("modules.serviceManagement.showService") }}
        </x-slot>

        <x-slot name="content">
            @if ($serviceManagementShow)
                @livewire('forms.showServiceProviderManagement', ['serviceManagementShow' => $serviceManagementShow], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showServiceManagementModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model="confirmDeleteServiceManagementModal">
        <x-slot name="title">
            @lang('modules.serviceManagement.deleteService')
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteServiceManagementModal',false)" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($serviceManagement)
            <x-danger-button class="ml-3" wire:click='deleteServiceManagement({{ $serviceManagement->id }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="confirmSelectedDeleteServiceManagementModal">
        <x-slot name="title">
            @lang('modules.serviceManagement.deleteService')
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmSelectedDeleteServiceManagementModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click='deleteSelected' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
         </x-slot>
    </x-confirmation-modal>

</div>

