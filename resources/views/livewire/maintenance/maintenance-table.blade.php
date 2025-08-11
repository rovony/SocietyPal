
<div class="px-4 pt-2 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900 ">
    @if ($showFilters)
        @include('maintenance.filters')
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
                    <x-dropdown-link href="javascript:;" wire:click="showSelectedDeleteMaintenance">
                        <span class="inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            @lang('app.delete')
                        </span>
                    </x-dropdown-link>
                    <x-dropdown-link href="javascript:;" wire:click="publishedAll">
                        <span class="inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M16.293 5.293a1 1 0 00-1.414 0L8 11.586 5.121 8.707a1 1 0 10-1.414 1.414l3.536 3.535a1 1 0 001.414 0l7.121-7.121a1 1 0 000-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            @lang('app.publish')
                        </span>
                    </x-dropdown-link>
                </x-slot>
            </x-dropdown>
        @endif
    </div>
    <div class="flex flex-col">
        <div>
            <div class="md:inline-block min-w-full align-middle">
                <div class="shadow overflow-x-auto h-screen md:h-auto md:overflow-x-visible">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                @if(user_can('Delete Maintenance'))
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </th>
                                @endif
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.year')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.month')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.maintenance.totalAdditionalCost')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.status')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>

                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>
                            @forelse ($maintenances as $item)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='maintenance-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                @if(user_can('Delete Maintenance'))
                                    <td class="p-4">
                                        <input type="checkbox" wire:model.live="selected" value="{{ $item->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </td>
                                @endif
                                <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $item->year }}
                                </td>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @if(user_can('Show Maintenance'))
                                    <a href="{{ route('maintenance.show', $item->id) }}" class="text-base font-semibold hover:underline dark:text-black-400">
                                        {{ ucwords(strtolower($item->month)) }}
                                    </a>
                                    @else
                                        {{ ucwords(strtolower($item->month)) }}
                                    @endif
                                </td>

                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @php
                                        $totalCost = is_numeric($item->total_additional_cost) ? $item->total_additional_cost : 0;
                                        $totalApartments = $item->maintenanceApartments->count();
                                        $cost = $totalCost * $totalApartments;
                                    @endphp
                                    {{ currency_format($cost) }}
                                </td>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($item->status === 'published')
                                        <x-badge type="success">{{ucFirst($item->status)}}</x-badge>
                                    @else
                                        <x-badge type="danger">{{ucFirst($item->status)}}</x-badge>
                                    @endif
                                </td>
                                <td class="py-2.5 px-4 space-x-2 whitespace-nowrap text-right">
                                    <x-dropdown align="right">
                                        <x-slot name="trigger">
                                            <button type="button"
                                                 class="inline-flex items-center px-3 py-2 border uppercase dark:border-gray-400 text-sm leading-4 font-medium rounded-md text-gray-500 hover:text-gray-700 dark:hover:text-gray-400 focus:outline-none transition ease-in-out duration-150">
                                                <span>@lang('app.action')</span>
                                                <svg class="w-2.5 h-2.5 ms-1" height="24" width="24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                     fill="none" viewBox="0 0 10 6">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                </svg>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">

                                            <x-dropdown-link href="{{ route('maintenance.show', $item->id) }}" class="text-yellow-500 dark:text-yellow-400">
                                                @lang('app.view')
                                            </x-dropdown-link>

                                            @if($item->status == "draft")

                                                <x-dropdown-link wire:click="showPublished({{ $item->id }})" class="text-green-500 dark:text-green-400">
                                                    @lang('app.publish')
                                                </x-dropdown-link>

                                                @if(user_can('Update Maintenance'))
                                                    <x-dropdown-link wire:click="showEditMaintenance({{ $item->id }})" wire:key='member-edit-{{ $item->id . microtime() }}'>
                                                        @lang('app.update')
                                                    </x-dropdown-link>
                                                @endif
                                                @if(user_can('Delete Maintenance'))
                                                    <x-dropdown-link wire:click="showDeleteMaintenance({{ $item->id }})"  wire:key='member-del-{{ $item->id . microtime() }}' class="text-red-600 dark:text-red-400">
                                                        @lang('app.delete')
                                                    </x-dropdown-link>
                                                @endif
                                            @endif
                                            

                                        </x-slot>
                                    </x-dropdown>
                                </td>
                            </tr>
                            @empty
                                <x-no-results :message="__('messages.noMaintenanceFound')" />
                            @endforelse

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    <div wire:key='maintenance-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-4 sm:mb-0">
            {{ $maintenances->links() }}
        </div>
    </div>


    <x-right-modal wire:model.live="showEditMaintenanceModal">
        <x-slot name="title">
            {{ __("modules.maintenance.editMaintenance") }}
        </x-slot>

        <x-slot name="content">
            @if ($maintenance)
            @livewire('forms.editMaintenance', ['maintenance' => $maintenance], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditMaintenanceModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model="confirmDeleteMaintenanceModal">
        <x-slot name="title">
            @lang('modules.maintenance.deleteMaintenance')
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteMaintenanceModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($maintenance)
            <x-danger-button class="ml-3" wire:click='deleteMaintenance({{ $maintenance->id }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="confirmSelectedDeleteMaintenanceModal">
        <x-slot name="title">
            @lang('modules.maintenance.deleteMaintenance')
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
            <div class="flex items-center p-4 mt-4 mb-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <div>
                  <span class="font-medium"><strong>@lang('modules.maintenance.deleteMaintenanceMessage')</strong></span>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmSelectedDeleteMaintenanceModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click='deleteSelected' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
         </x-slot>
    </x-confirmation-modal>

</div>

