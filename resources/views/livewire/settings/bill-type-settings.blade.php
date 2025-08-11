<div class="px-4 pt-2 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">

    <div class="mb-4">
        <x-button type='button' wire:click="showAddBillType" >@lang('modules.settings.addBillType')</x-button>
    </div>

    <div class="flex flex-col">

        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden shadow">
                    <table class="min-w-full divide-y divide-gray-200 table-fixed dark:divide-gray-600">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.billType')
                                </th>

                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.billCategory')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='member-list-{{ microtime() }}'>
                            @forelse ($billTypes as $item)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='member-{{ $item->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                    <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $item->name }}
                                    </td>

                                    <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $item->bill_type_category }}
                                    </td>

                                    <td class="p-4 space-x-2 text-right whitespace-nowrap">
                                        <x-secondary-button wire:click='showEditBillType({{ $item->id }})' wire:key='member-edit-{{ $item->id . microtime() }}'
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

                                        <x-danger-button  wire:click="showDeleteBillType({{ $item->id }})"  wire:key='member-del-{{ $item->id . microtime() }}'>
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
                                <x-no-results :message="__('messages.noBillTypeFound')" />
                            @endforelse

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <x-right-modal wire:model.live="showEditBillTypeModal">
        <x-slot name="title">
            {{ __("modules.settings.editbillType") }}
        </x-slot>

        <x-slot name="content">
            @if ($billType)
            @livewire('forms.editBillType', ['billType' => $billType], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditBillTypeModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-right-modal wire:model.live="showAddBillTypeModal">
        <x-slot name="title">
            {{ __("modules.settings.addBillType") }}
        </x-slot>

        <x-slot name="content">
            @livewire('forms.addBillType')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showAddBillTypeModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model="confirmDeleteBillTypeModal">
        <x-slot name="title">
            @lang('modules.settings.deletebillType')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteBillTypeModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($billType)
            <x-danger-button class="ml-3" wire:click='deleteBillType({{ $billType->id }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>


</div>
