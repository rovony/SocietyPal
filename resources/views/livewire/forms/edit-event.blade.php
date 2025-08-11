<div>
    <form wire:submit.prevent="submitForm">
        @csrf

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label for="event_name" value="{{ __('modules.event.eventName') }}" required/>
                    <x-input id="event_name" class="block mt-1 w-full" type="text" wire:model='event_name' autocomplete="off"/>
                    <x-input-error for="event_name" class="mt-2" />
                </div>

                <div>
                    <x-label for="where" value="{{ __('modules.event.where') }}" required/>
                    <x-input id="where" class="block mt-1 w-full" type="text" wire:model='where' autocomplete="off"/>
                    <x-input-error for="where" class="mt-2" />
                </div>
            </div>

            <div>
                <x-label for="description" :value="__('modules.notice.description')" required/>
                <x-textarea class="block mt-1 w-full" :placeholder="__('placeholders.eventDescriptionPlaceholder')"
                    wire:model='description' rows='3' />
                <x-input-error for="description" class="mt-2" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label for="start_on_date" value="{{ __('modules.event.startsDate') }}" required/>
                    <x-input id="start_on_date" class="block mt-1 w-full" type="date" wire:model='start_on_date' autocomplete="off"/>
                    <x-input-error for="start_on_date" class="mt-2" />
                </div>
                <div>
                    <x-label for="start_on_time" value="{{ __('modules.event.startsTime') }}" required/>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input type="time" id="start_on_time" wire:model.live='start_on_time' class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500" min="00:00" max="23:59" />
                    </div>
                    <x-input-error for="start_on_time" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label for="end_on_date" value="{{ __('modules.event.endsDate') }}" required/>
                    <x-input id="end_on_date" class="block mt-1 w-full" type="date" wire:model='end_on_date' autocomplete="off"/>
                    <x-input-error for="end_on_date" class="mt-2" />
                </div>
                <div>
                    <x-label for="end_on_time" value="{{ __('modules.event.endsTime') }}" required/>
                    <div class="relative mt-1">
                        <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input type="time" id="end_on_time" wire:model.live='end_on_time' class="bg-gray-50 border leading-none border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-gray-500 dark:focus:border-gray-500" min="00:00" max="23:59" />
                    </div>
                    <x-input-error for="end_on_time" class="mt-2" />
                </div>
            </div>

            <div>
                <x-label for="status" value="{{ __('modules.user.status') }}" />
                <x-select id="status" class="block w-full mt-1" wire:model.live="status">
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="completed">{{ __('Completed') }}</option>
                    <option value="cancelled">{{ __('Cancelled') }}</option>
                </x-select>
                <x-input-error for="status" class="mt-2" />
            </div>


            <div class="flex space-x-4 mb-6">
                <label class="flex-1 flex items-center p-3 rounded-lg bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                    <input type="radio" wire:model.live="userRoleWise" value="role-wise" name="userRoleWise"
                           class="w-4 h-4 text-skin-base bg-gray-100 border-gray-300 focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                    <div class="ml-3">
                        <span class="block text-sm font-medium">@lang('Role')</span>
                    </div>
                </label>

                <label class="flex-1 flex items-center p-3 rounded-lg bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                    <input type="radio" wire:model.live="userRoleWise" value="user-wise" name="userRoleWise"
                           class="w-4 h-4 text-skin-base bg-gray-100 border-gray-300 focus:ring-skin-base dark:focus:ring-skin-base dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                    <div class="ml-3">
                        <span class="block text-sm font-medium">@lang('User')</span>
                    </div>
                </label>
            </div>
            @if ($userRoleWise === 'role-wise')
            <div>
                <x-label for="selectedRoles" :value="__('modules.user.role')" required/>
                <div class="mt-1">
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($roles as $role)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model.live='selectedRoles' value="{{ $role->id }}" class="mr-2" />
                                <span>{{ $role->display_name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <x-input-error for="selectedRoles" class="mt-2" />
            </div>
            @elseif ($userRoleWise === 'user-wise')
            <div>
                <x-label for="selectedUserNames" :value="__('app.name')" />
                <div x-data="{ isOpen: @entangle('isOpen'), selectedUserNames: @entangle('selectedUserNames') }" @click.away="isOpen = false">
                    <!-- Container for input and button -->
                    <div class="flex items-center space-x-2">
                        <!-- Dropdown for selecting users -->
                        <div class="relative flex-1">
                            <div @click="isOpen = !isOpen" class="p-2 bg-gray-100 border rounded cursor-pointer dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                                @lang('modules.settings.selectUser')
                            </div>

                            <ul x-show="isOpen" x-transition class="absolute z-10 w-full mt-1 overflow-auto bg-white rounded-lg shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                                @forelse ($users as $user)

                                    <li @click="$wire.toggleSelectType({ id: {{ $user->id }}, name: '{{ addslashes($user->name) }}' })"
                                        wire:key="{{ $loop->index }}"
                                        class="relative py-2 pl-3 text-gray-900 transition-colors duration-150 cursor-pointer select-none pr-9 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600 "
                                        :class="{ 'bg-gray-100': selectedUserNames.includes({{ $user->id }}) }"
                                        role="option">

                                        <div class="flex items-center">
                                            <span class="block ml-3 truncate">{{ $user->name }}</span>
                                            <span x-show="selectedUserNames.includes({{ $user->id }})" class="absolute inset-y-0 right-0 flex items-center pr-4 text-black dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600" x-cloak>
                                                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                    </li>
                                @empty
                                    <x-no-results :message="__('messages.noUserFound')" />
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Display selected parking codes -->
                    <div class="mt-2 w-64 md:w-auto break-words">
                        <span class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.selectUser')</span>
                        <span class="text-sm text-gray-900 dark:text-gray-400">
                            {{ implode(', ', collect($users)->whereIn('id', $selectedUserNames)->pluck('name')->toArray()) }}
                        </span>
                    </div>

                    <x-input-error for="selectedUserNames" class="mt-2" />
                </div>
            </div>
            @endif

            <div class="flex w-full pb-4 space-x-4 mt-6">
                <x-button>@lang('app.update')</x-button>
                <x-button-cancel wire:click="$dispatch('hideEditEvent')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
            </div>
        </div>
    </form>
</div>
