<div class="ticket-detail-container">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.js"></script>

    <div class="p-8 grid grid-cols-12 gap-8"> <!-- Use grid layout with 12 columns -->

        <!-- Left Part: Ticket Replies (8 parts) -->
        <div class="col-span-8">
            <div class="flex items-center justify-between mb-0">
                <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-300">
                    @lang('modules.tickets.ticket')#{{ $ticketId }}
                </h1>

                <div>
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
            </div>

            <div class="mb-4">
                <h4 class="text-xl text-gray-600 dark:text-gray-400">
                    {{ ucfirst($ticket->subject) }}
                </h4>
            </div>

            <div class="overflow-y-auto max-h-[28rem] thin-scrollbar">
            @forelse ($ticketReplies as $reply)
            <div class="flex flex-col items-start bg-gray-100 dark:bg-gray-800 mb-2 rounded-lg gap-2.5 p-3" wire:key="reply-{{ $loop->index }}">
                <div class="flex justify-between w-full">
                    <div class="flex gap-2">
                        <img class="w-8 h-8 rounded-full" src="{{$reply->user->profile_photo_url}}" alt="{{$reply->user->name}}">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{$reply->user->name}}</span>
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{$reply->created_at->diffForHumans()}}</span>
                        </div>
                    </div>

                    @if(user_can('Delete Tickets') || $reply->user->id == user()->id)
                        <div>
                            <x-dropdown dropdownClasses="z-50">
                                <x-slot name="trigger">
                                    <span class="inline-flex">
                                        <button type="button"
                                            class="inline-flex items-center justify-center p-2 text-sm font-medium text-gray-500 dark:text-gray-200 transition duration-150 ease-in-out bg-gray-100 dark:bg-gray-700 border border-gray-300 rounded-md hover:text-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 4 15">
                                                <path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                                            </svg>
                                        </button>
                                    </span>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link href="javascript:;" wire:click="deleteReply({{ $reply->id }})">
                                        <span class="inline-flex items-center justify-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            @lang('app.delete')
                                        </span>
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif
                </div>
                <div class="break-words trix-content p-3 text-sm  text-gray-900 dark:text-white">
                  {!! nl2br($reply->message) !!}
                </div>
            @if ($reply->files->isNotEmpty())
                <div class="grid grid-cols-4 gap-3 mt-2">
                    @foreach ($reply->files as $file)
                        @php
                            $isImage = in_array(pathinfo($file->filename, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'bmp']);
                        @endphp

                        @if ($isImage)
                            <!-- Show image with equal spacing -->
                            <div class="flex items-center justify-center w-28 h-28 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <a href="{{ asset_url_local_s3($file->hashname) }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset_url_local_s3($file->hashname) }}"
                                         title="{{ $file->filename }}"
                                         class="max-h-full max-w-full object-contain text-blue-600 dark:text-blue-400 text-sm underline" />
                                </a>
                            </div>
                        @else
                            <!-- Show file icon with tooltip and equal spacing -->
                            <div class="relative group">
                                <a href="{{ asset_url_local_s3($file->hashname) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   title="{{ $file->filename }}"
                                   class="flex items-center justify-center w-28 h-28 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                    <svg class="w-12 h-12 text-gray-800 dark:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 3v4a1 1 0 0 1-1 1H5m4 6 2 2 4-4m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z"/>
                                    </svg>
                                </a>
                                <!-- Tooltip -->
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 translate-y-2 opacity-0 group-hover:opacity-100 group-hover:translate-y-0 bg-gray-900 text-white text-xs rounded py-1 px-2 transition-all duration-200">
                                    {{ $file->filename }}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
            </div>
        @empty

            <p class="mt-2 text-center text-gray-500 dark:text-gray-400">@lang('app.noRecordsFound')</p>
        @endforelse
        </div>

            <form wire:submit.prevent="submitReply" x-data="{ content: @entangle('replyMessage').live }">
                <div class="px-4 py-2 bg-white dark:bg-gray-900  rounded-t-lg">
                    <p class="text-sm font-semibold text-gray-800 dark:text-white">
                        To: {{ $ticket->user->name }}
                    </p>
                </div>
                <div class="px-4 py-2 bg-white dark:bg-gray-300">
                    <label class="sr-only">Write a reply</label>
                    <input id="replyMessage" name="replyMessage" wire:model="replyMessage" type="hidden" />

                    <div wire:ignore>
                        <trix-editor
                            class="trix-content text-sm dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600"
                            x-on:trix-change="$wire.replyMessage = $event.target.value"
                            x-ref="trixEditor"
                            x-init="
                                window.addEventListener('reset-trix-editor', () => {
                                    $refs.trixEditor.editor.loadHTML('');
                                });
                            ">
                        </trix-editor>
                    </div>
                    <x-input-error for="replyMessage" class="mt-2" />
                </div>


                <div class="my-3">
                    <label class="flex items-center cursor-pointer text-blue-600 dark:text-blue-400">
                        <i class="fa fa-paperclip font-bold mr-2"></i>
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            <path d="M7 8v8a5 5 0 1 0 10 0V6.5a3.5 3.5 0 1 0-7 0V15a2 2 0 0 0 4 0V8"/>
                        </svg>
                        @lang('modules.tickets.uploadFile')
                        <input type="file" wire:model="uploadedFiles" multiple class="hidden">
                    </label>

                    @if ($uploadedFiles)
                        <div class="mt-2 space-y-2">
                            @foreach ($uploadedFiles as $index => $file)
                                <div class="flex items-center text-gray-800 dark:text-gray-200 text-sm">
                                    <svg class="w-5 h-5 mr-2 text-gray-500 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15.172 7l-6.586 6.586a2 2 0 002.828 2.828L18 12.828m0 0l-6.586 6.586a2 2 0 01-2.828 0L6.586 14M18 12.828l-6.586-6.586a2 2 0 00-2.828 0L6.586 9.172" />
                                    </svg>
                                    <span class="flex-1">{{ $file->getClientOriginalName() }}</span>
                                    <!-- Cross Button -->
                                    <button type="button" wire:click="removeFile({{ $index }})" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @error('uploadedFiles.*') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Status Dropdown -->
                <div class="relative inline-block text-left">
                    <button type="button" id="dropdownReplyButton" data-dropdown-toggle="replyDropdown"
                        class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800">
                        @lang('modules.tickets.submit')
                        <svg class="w-2.5 h-2.5 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M1 1l4 4 4-4" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="replyDropdown" class="hidden z-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                            <li>
                                <button type="button" wire:click="submitReply('open')"
                                    class="block px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    @lang('modules.tickets.submitAsOpen')
                                </button>
                            </li>
                            <li>
                                <button type="button" wire:click="submitReply('pending')"
                                    class="block px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    @lang('modules.tickets.submitAsPending')
                                </button>
                            </li>
                            <li>
                                <button type="button" wire:click="submitReply('resolved')"
                                    class="block px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    @lang('modules.tickets.submitAsResolved')
                                </button>
                            </li>
                            <li>
                                <button type="button" wire:click="submitReply('closed')"
                                    class="block px-4 py-2 w-full text-left hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
                                    @lang('modules.tickets.submitAsClosed')
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Part: Ticket Update Fields (4 parts) -->
        <div class="col-span-4">
            <!-- Here you can add fields to update the ticket -->
            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg shadow-lg">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                    @lang('modules.tickets.details')
                </h2>
                <!-- Example fields to update ticket information -->
                <div class="mb-4">
                    <x-label for="type_id" value="{{ __('modules.tickets.type') }}" />

                    <x-select class="mt-1 block w-full" wire:model='type_id' wire:change="updateAgents">
                        <option value="">--</option>
                        @foreach ($ticketTypes as $ticketType)
                            <option value="{{ $ticketType->id }}">{{ $ticketType->type_name }}</option>
                        @endforeach
                    </x-select>

                    <x-input-error for="type_id" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-label for="agent_id" value="{{ __('modules.tickets.agent') }}" />

                    <x-select class="mt-1 block w-full" wire:model='agent_id'>
                        <option value="">--</option>
                        @foreach ($agents as $agent)
                            <option value="{{ $agent->ticket_agent_id }}">{{ $agent->user->name }}</option>
                        @endforeach
                    </x-select>

                    <x-input-error for="agent_id" class="mt-2" />
                </div>

                <div class="mb-4">
                    <x-label for="status" value="{{ __('modules.settings.status') }}" />
                    <x-select id="status" class="block w-full mt-1" wire:model.live="status">
                        <option value="">--</option>
                        <option value="open">{{ __('modules.tickets.open') }}</option>
                        <option value="pending">{{ __('modules.tickets.pending') }}</option>
                        <option value="resolved">{{ __('modules.tickets.resolved') }}</option>
                        <option value="closed">{{ __('modules.tickets.closed') }}</option>
                    </x-select>
                    <x-input-error for="status" class="mt-2" />
                </div>

                <button type="button" wire:click="updateTicket"
                    class="w-full px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800">
                    @lang('modules.tickets.update')
                </button>

            </div>
        </div>

    </div>
</div>
