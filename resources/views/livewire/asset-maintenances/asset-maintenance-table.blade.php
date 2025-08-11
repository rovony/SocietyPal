<div class="px-4 pt-2 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">
    <div class="flex items-center justify-end">
    @if ($showFilters)
        @include('asset-maintenance.filters')
    @endif
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
                    <x-dropdown-link href="javascript:;" wire:click="showSelectedDeleteAsset">
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
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    #
                                </th>

                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.assets.maintenanceDate')
                                </th>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.assets.asset')
                                </th>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.assets.maintenanceSchedule')
                                </th>

                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.assets.status')
                                </th>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.assets.amount')
                                </th>
                                <th scope="col"
                                    class="py-2.5 px-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>


                            </tr>
                        </thead>
                         <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>

                            @forelse ($maintenances as $maintenance)

                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='apartment-{{ $maintenance->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $loop->index+1 }}
                                </td>


                                <td class="py-2.5 px-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    <a class="text-base font-semibold hover:underline dark:text-black-400" href="javascript:;"   wire:click="showMaintenances({{ $maintenance->id }})">
                                       {{ $maintenance->maintenance_date ? \Carbon\Carbon::parse($maintenance->maintenance_date)->format('d M Y') : '--' }}
                                    </a>
                                </td>

                                <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $maintenance->asset->name ?? '--' }}
                                </td>

                                <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $maintenance->schedule ?? '--' }}
                                </td>

                                <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($maintenance->status == 'completed')
                                     <x-badge type="success">{{ucFirst($maintenance->status)}}</x-badge>
                                @elseif($maintenance->status == 'pending')
                                    <x-badge type="warning">{{ucFirst($maintenance->status)}}</x-badge>
                                @else
                                    <x-badge type="danger">{{ucFirst($maintenance->status)}}</x-badge>
                                @endif
                                </td>

                                <td class="py-2.5 px-4 text-sm text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $maintenance->amount ?? '--'}}
                                </td>
                                <td class="p-4 space-x-2 text-right whitespace-nowrap">
                                    <x-secondary-button wire:click="showEditMaintenance({{ $maintenance->id }})" wire:key="asset-maintenance-edits-{{ $maintenance->id . microtime() }}">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                </path>
                                                <path fill-rule="evenodd"
                                                    d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            @lang('app.update')
                                    </x-secondary-button>
                                    <x-danger-button wire:click="showDeleteAsset({{ $maintenance->id }})" wire:key='asset-del-{{ $maintenance->id . microtime() }}'>
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </x-danger-button>
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

    <x-dialog-modal wire:model.live="showMaintenanceDetailsModal">
        <x-slot name="title">
            {{ __("modules.assets.editMaintenanceSchedule") }}
        </x-slot>
        <x-slot name="content">
            @if ($selectedMaintenance)
                @livewire('assets.edit-asset-maintenance', ['maintenanceId' => $this->selectedMaintenance], key(microtime()))
            @endif
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showMaintenanceDetailsModal', false)" wire:loading.attr="disabled">
                {{ __("app.close") }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="showMaintenancesModal">
        <x-slot name="title">
            {{ __("modules.assets.maintenanceScheduleDetails") }}
        </x-slot>
        <x-slot name="content">
            @if ($MaintenanceId)
            @livewire('assets.show-maintenance-details', ['maintenanceId' => $this->MaintenanceId], key(microtime()))
        @endif
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showMaintenancesModal', false)" wire:loading.attr="disabled">
                {{ __("app.close") }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <x-confirmation-modal wire:model="confirmDeleteAssetModal">
        <x-slot name="title">
            @lang('modules.assets.deleteAssetMaintenanceSchedule')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteAssetModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($assetMaintenanceId)
            <x-danger-button class="ml-3" wire:click='deleteAsset({{ $assetMaintenanceId }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="confirmSelectedDeleteAssetModal">
        <x-slot name="title">
            @lang('modules.assets.deleteAssetMaintenanceSchedule')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmSelectedDeleteAssetModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click='deleteSelected' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
         </x-slot>
    </x-confirmation-modal>




</div>

