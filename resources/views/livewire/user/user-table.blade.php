<div class="px-4 pt-2 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">
    @if ($showFilters)
        @include('users.filters')
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
                    <x-dropdown-link href="javascript:;" wire:click="showSelectedDeleteUser">
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
                                @if(user_can('Delete User'))
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
                                    @lang('modules.user.role')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.user.status')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-right text-gray-500 uppercase dark:text-gray-400">
                                    @lang('app.action')
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='user-list-{{ microtime() }}'>
                            @forelse ($users as $user)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='user-{{ $user->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                @if(user_can('Delete User'))
                                <td class="p-4">
                                    @if(user_can('Delete User') && $user->id != user()->id && $user->id != $firstUserId)
                                        <input type="checkbox" wire:model.live="selected" value="{{ $user->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    @else
                                        --
                                    @endif
                                </td>
                                @endif
                                <td class="p-4 text-base font-semibold text-gray-900 whitespace-nowrap dark:text-white">
                                    <a href="{{ route('users.show', $user->id) }}" class="text-base font-semibold hover:underline dark:text-black-400">
                                        <x-avatar-image :src="$user->profile_photo_url" :alt="$user->name" :name="$user->name"/>
                                    </a>
                                </td>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $user->email ?? '--' }}
                                </td>
                                <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($user->phone_number)
                                            @if($user->country_phonecode)
                                                +{{ $user->country_phonecode }}
                                            @endif
                                            {{ $user->phone_number }}
                                        @else
                                            <span class="font-bold dark:text-gray-400">--</span>
                                    @endif
                                </td>
                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @if ($user->id == auth()->id())
                                        <span class="text-xs">@lang('messages.cannotEditOwnRole')</span>
                                    @elseif ($user->id === $firstUserId)
                                        <span>{{ $user->role->display_name }}</span>
                                    @elseif(user_can('Update User'))
                                        <button wire:key='user-role-{{ $user->id . microtime() }}' id="dropdownHoverButton{{ $user->id }}" data-dropdown-toggle="dropdownHover{{ $user->id }}" data-dropdown-trigger="click" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25" type="button">
                                            {{ $user->role->display_name }}
                                            <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                            </svg>
                                        </button>

                                        <!-- Dropdown menu -->
                                        <div wire:key='user-role-{{ $user->id . microtime() }}' id="dropdownHover{{ $user->id }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownHoverButton{{ $user->id }}">
                                                @foreach ($roles as $role)
                                                <li>
                                                    <a href="javascript:;" wire:click="setUserRole('{{ $role->id }}', {{ $user->id }})" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">{{ $role->display_name }}</a>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        {{ $user->role->display_name }}
                                    @endif
                                </td>

                                <td class="p-4 text-base font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    @if($user->status == 'active')
                                        <x-badge type="success">{{ucFirst($user->status)}}</x-badge>
                                    @elseif($user->status == 'inactive')
                                        <x-badge type="danger">{{ucFirst($user->status)}}</x-badge>
                                    @endif
                                </td>

                                <td class="p-4 space-x-2 text-right whitespace-nowrap">
                                    @if(user_can('Update User') || $user->id == user()->id)
                                        <x-secondary-button wire:click="showEditUser({{ $user->id }})" wire:key="user-edit-{{ $user->id . microtime() }}">
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

                                    @if(user_can('Delete User') && $user->id != user()->id && $user->id != $firstUserId)
                                        <x-danger-button  wire:click="showDeleteUser({{ $user->id }})"  wire:key='user-del-{{ $user->id . microtime() }}'>
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
                                <x-no-results :message="__('messages.noUserFound')" />
                            @endforelse

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div wire:key='user-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center w-full mb-4 sm:mb-0">
            {{ $users->links() }}
        </div>
    </div>

    <x-right-modal wire:model.live="showEditUserModal">
        <x-slot name="title">
            {{ __("modules.user.viewUser") }}
        </x-slot>

        <x-slot name="content">
            @if ($seletedUser)
            @livewire('forms.editUser', ['user' => $seletedUser], key(str()->random(50)))
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showEditUserModal', false)" wire:loading.attr="disabled">
                {{ __('Close') }}
            </x-secondary-button>
        </x-slot>
    </x-right-modal>

    <x-confirmation-modal wire:model="confirmDeleteUserModal">
        <x-slot name="title">
            @lang('modules.user.deleteUser')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteUserModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($seletedUser)
            <x-danger-button class="ml-3" wire:click='deleteUser({{ $seletedUser->id }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="confirmSelectedDeleteUserModal">
        <x-slot name="title">
            @lang('modules.user.deleteUser')?
        </x-slot>

        <x-slot name="content">
            @lang('messages.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmSelectedDeleteUserModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click='deleteSelected' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
         </x-slot>
    </x-confirmation-modal>
</div>
