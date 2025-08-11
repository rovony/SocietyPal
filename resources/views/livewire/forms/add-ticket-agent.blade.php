<div>
    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">
            <div>
                <x-search-dropdown id="selectedAgent" label="{{ __('modules.settings.societyTicketAgent') }}" model="selectedAgent" :options="$users->map(fn($a) => ['id' => $a->id, 'number' => $a->name])" placeholder="{{ __('modules.settings.societyTicketAgent') }}" :required="true"/>
            </div>
            <div>
                <x-label for="ticketTypeName" :value="__('modules.settings.societyTicketType')" />

                <div class="block w-full mt-1" x-data="{ isOpenTicket: @entangle('isOpenTicket'), selectedTicketTypes: @entangle('selectedTicketTypes') }" @click.away="isOpenTicket = false">
                    <!-- Dropdown for selecting users -->
                    <div class="flex items-center space-x-2">
                        <div class="relative flex-1">
                            <div @click="isOpenTicket = !isOpenTicket" class="p-2 bg-gray-100 border rounded cursor-pointer dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                                @lang('modules.settings.selectTicketType')
                            </div>

                            <ul x-show="isOpenTicket" x-transition class="absolute z-10 w-full mt-1 overflow-auto bg-white rounded-lg shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600">
                                @forelse ($ticketTypes as $ticketType)
                                    <li @click="$wire.toggleSelectType({ id: {{ $ticketType->id }}, type_name: '{{ addslashes($ticketType->type_name) }}' })"
                                        wire:key="{{ $loop->index }}"
                                        class="relative py-2 pl-3 text-gray-900 transition-colors duration-150 cursor-pointer select-none pr-9 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600 "
                                        :class="{ 'bg-gray-100': selectedTicketTypes.includes({{ $ticketType->id }}) }"
                                        role="option">

                                        <div class="flex items-center">
                                            <span class="block ml-3 truncate">{{ $ticketType->type_name }}</span>
                                            <span x-show="selectedTicketTypes.includes({{ $ticketType->id }})" class="absolute inset-y-0 right-0 flex items-center pr-4 text-black dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-gray-600 dark:focus:ring-gray-600" x-cloak>
                                                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                    </li>
                                    @empty
                                    <li class="relative py-2 pl-3 text-gray-500 cursor-default select-none pr-9 dark:text-gray-400">
                                        @lang('modules.settings.noUsersFound')
                                    </li>
                                @endforelse
                            </ul>

                        </div>
                    </div>
                    <!-- Display selected user names -->
                    <div class="mt-2 w-64 md:w-auto break-words">
                        <span class="text-sm text-gray-500 dark:text-gray-400">@lang('modules.settings.selectedTicketTypes')</span>
                        <span class="text-sm text-gray-900 dark:text-gray-400">
                            {{ implode(', ', $ticketType->whereIn('id', $selectedTicketTypes)->pluck('type_name')->toArray()) }}
                        </span>
                    </div>
                    <x-input-error for="selectedTicketTypes" class="mt-2" />
                </div>
            </div>
        </div>


        <div class="flex w-full pb-4 mt-6 space-x-4">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideAddTicketAgent')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
