<div class="px-4 pt-2 xl:grid-cols-3 xl:gap-4 bg-white dark:bg-gray-900">
    @if ($showFilters)
        @include('tickets.filters')
    @endif
    <div class="flex items-center justify-end">
        @if($showActions)
            <x-dropdown dropdownClasses="z-50">
                <x-slot name="trigger">
                    <span class="inline-flex">
                        <button type="button"
                            class="inline-flex items-center justify-center p-2 text-sm font-medium text-gray-500 transition duration-150 ease-in-out bg-white border border-gray-300 hover:text-gray-700 focus:outline-none hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 mb-2">
                            @lang('app.action')
                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                    </span>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link href="javascript:;" wire:click="showSelectedDeleteTicket">
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
                                @if(user_can('Delete Tickets'))
                                    <th scope="col" class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                    </th>
                                @endif
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.tickets.ticketNumber')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.tickets.subject')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.tickets.requesterName')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.tickets.agent')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.tickets.ticketType')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-left text-gray-500 uppercase dark:text-gray-400">
                                    @lang('modules.settings.status')
                                </th>
                                <th scope="col"
                                    class="p-4 text-xs font-medium text-gray-500 uppercase dark:text-gray-400 text-right">
                                    @lang('app.action')
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700" wire:key='ticket-list-{{ microtime() }}'>
                            @forelse ($tickets as $ticket)
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700" wire:key='ticket-{{ $ticket->id . rand(1111, 9999) . microtime() }}' wire:loading.class.delay='opacity-10'>
                                    @if(user_can('Delete Tickets'))
                                        <td class="p-4">
                                            <input type="checkbox" wire:model.live="selected" value="{{ $ticket->id }}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                        </td>
                                    @endif
                                    <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        <a href="{{ route('tickets.show', $ticket->id) }}"
                                        class="text-base hover:underline dark:text-black-400">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </td>
                                    <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        <a href="{{ route('tickets.show', $ticket->id) }}"
                                            class="text-base hover:underline dark:text-black-400">
                                            {{ Str::words($ticket->subject, 5, '...') }}
                                        </a>
                                    </td>
                                    <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $ticket->user->name ?? '--' }}
                                    </td>
                                    <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $ticket->agent->name ?? '--' }}
                                    </td>
                                    <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $ticket->ticketType->type_name ?? '--' }}
                                    </td>
                                    <td class="p-4 text-base text-gray-900 whitespace-nowrap dark:text-white">
                                        <div class="flex items-center">
                                            @if(isset($ticket->status))
                                                @if($ticket->status === 'open')
                                                    <x-badge type="danger">{{ ucfirst($ticket->status) }}</x-badge>
                                                @elseif($ticket->status === 'pending')
                                                    <x-badge type="warning">{{ ucfirst($ticket->status) }}</x-badge>
                                                @elseif($ticket->status === 'resolved')
                                                    <x-badge type="success">{{ ucfirst($ticket->status) }}</x-badge>
                                                @elseif($ticket->status === 'closed')
                                                    <x-badge type="secondary">{{ ucfirst($ticket->status) }}</x-badge>
                                                @endif
                                            @else
                                                --
                                            @endif
                                        </div>
                                    </td>

                                <td class="p-4 space-x-2 text-right whitespace-nowrap">
                                    @if(user_can('Delete Tickets') || $ticket->user_id == user()->id)
                                        <x-danger-button  wire:click="showDeleteTicket({{ $ticket->id }})"  wire:key='ticket-del-{{ $ticket->id . microtime() }}'>
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
                                <x-no-results :message="__('messages.noTicketFound')" />
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div wire:key='ticket-table-paginate-{{ microtime() }}'
        class="sticky bottom-0 right-0 items-center w-full p-4 bg-white border-t border-gray-200 sm:flex sm:justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center mb-4 sm:mb-0 w-full">
            {{ $tickets->links() }}
        </div>
    </div>

    <x-confirmation-modal wire:model="confirmDeleteTicketModal">
        <x-slot name="title">
            @lang('modules.tickets.deleteTicket')
        </x-slot>

        <x-slot name="content">
            @lang('app.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmDeleteTicketModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($ticket)
            <x-danger-button class="ml-3" wire:click='deleteTicket({{ $ticket->id }})' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>

    <x-confirmation-modal wire:model="confirmSelectedDeleteTicketModal">
        <x-slot name="title">
            @lang('modules.tickets.deleteTicket')
        </x-slot>

        <x-slot name="content">
            @lang('app.deleteMessage')
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmSelectedDeleteTicketModal')" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            @if ($ticket)
            <x-danger-button class="ml-3" wire:click='deleteSelected' wire:loading.attr="disabled">
                {{ __('Delete') }}
            </x-danger-button>
            @endif
         </x-slot>
    </x-confirmation-modal>
</div>

