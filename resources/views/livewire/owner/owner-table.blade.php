<div class="px-4 pt-2 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">
    @if ($showFilters)
        @include('owners.filters')
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
                    <x-dropdown-link href="javascript:;" wire:click="showSelectedDeleteOwner">
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
                                @if(user_can('Delete Owner'))
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </th>
                                @endif
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.user.name')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.user.email')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.user.phone')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.user.status')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-center text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.apartmentNumber')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='owner-list-{{ microtime() }}'>
                            @forelse ($owners as $owner)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='owner-{{ $owner->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                @if(user_can('Delete Owner'))
                                    <td class="p-4">
                                        <input type="checkbox" wire:model.live="selected" value="{{ $owner->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </td>
                                @endif
                                <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                    @if(user_can('Update Owner') || $owner->id == user()->id)
                                        <a href="{{ route('owners.show', $owner->id) }}" class="text-base font-semibold hover:underline dark:text-black-400">
                                            <x-avatar-image :src="$owner->profile_photo_url" :alt="$owner->name" :name="$owner->name"/>
                                        </a>
                                    @else
                                        <x-avatar-image :src="$owner->profile_photo_url" :alt="$owner->name" :name="$owner->name"/>
                                    @endif
                                </td>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $owner->email ?? '--' }}
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($owner->phone_number)
                                        @if($owner->country_phonecode)
                                            +{{ $owner->country_phonecode }}
                                        @endif
                                        {{ $owner->phone_number }}
                                    @else
                                        <span class="dark:text-gray-400">--</span>
                                    @endif
                                </td>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($owner->status == 'active')
                                        <x-badge type="success">{{ucFirst($owner->status)}}</x-badge>
                                    @elseif($owner->status == 'inactive')
                                        <x-badge type="danger">{{ucFirst($owner->status)}}</x-badge>
                                    @endif
                                </td>

                                <td class="p-4 text-base font-medium text-center text-gray-900 whitespace-nowrap dark:text-white">
                                    @if(($owner->apartment->isNotEmpty()) && (user_can('Show Owner') || $owner->id == user()->id))
                                        <div class="space-y-1">
                                            @foreach($owner->apartment as $apartment)
                                                <span class="block">{{ $apartment->apartment_number }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        --
                                    @endif
                                </td>

                                <td class="p-4 space-x-2 text-right whitespace-nowrap">
                                    @if(user_can('Update Owner') || $owner->id == user()->id)
                                        <x-secondary-button wire:click="showEditOwner({{ $owner->id }})" wire:key="owner-edit-{{ $owner->id . microtime() }}">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z">
                                                </path>
                                                <path fill-rule="evenodd"
                                                    d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            @lang('app.update')
                                        </x-secondary-button>
                                    @endif

                                    @if(user_can('Delete Owner') && $owner->id != user()->id)
                                        <x-danger-button  wire:click="showDeleteOwner({{ $owner->id }})"  wire:key='owner-del-{{ $owner->id . microtime() }}'>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </x-danger-button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                                <x-no-results :message="__('messages.noOwnerFound')" />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div wire:key='owner-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center w-full mb-4 sm:mb-0">
            {{ $owners->links() }}
        </div>
    </div>

    <x-right-modal wire:model.live="showEditOwnerModal">
        <x-slot name="title">
            {{ __("modules.user.viewUser") }}
        </x-slot>

        <x-slot name="content">
            @if ($selectedEditOwner)
            @livewire('forms.editOwner', ['user' => $selectedEditOwner], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditOwnerModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model.live="confirmDeleteOwnerModal">
        <x-slot name="title">
            @lang('modules.user.deleteUser')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteOwnerModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($selectedOwner)
            <x-danger-button class="ml-3" wire:click='deleteOwner({{ $selectedOwner->id }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="confirmSelectedDeleteOwnerModal">
        <x-slot name="title">
            @lang('modules.user.deleteUser')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmSelectedDeleteOwnerModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click='deleteSelected' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
         </x-slot>
    </x-confirmation-modal>
</div>
