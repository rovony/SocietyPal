<div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.js"></script>

    <form wire:submit="submitForm">
        @csrf
        <div class="space-y-4">

            <div>
                
                @if($users->count() == 1)
                    <x-label for="user_id" value="{{ __('modules.tickets.requestedBy') }}" required/>
                    <x-input id="user_id" class="block mt-1 w-full" type="text" value="{{ $users->first()->name }}" readonly />
                    <input type="hidden" name="user_id" wire:model="user_id" value="{{ $users->first()->id }}" />
                @else
                    <x-search-dropdown id="user_id" label="{{ __('modules.tickets.requestedBy') }}" model="user_id" :options="$users->map(fn($a) => ['id' => $a->id, 'number' => $a->name])" placeholder="Select user" :required="true"/>
                @endif
            </div>

            <div>
                <x-label for="type_id" value="{{ __('modules.tickets.type') }}" required/>
                <x-select class="mt-1 block w-full" wire:model="type_id" wire:change="updateAgents">
                    <option value="">--</option>
                    @foreach ($ticketTypes as $ticketType)
                        <option value="{{ $ticketType->id }}">{{ $ticketType->type_name }}</option>
                    @endforeach
                </x-select>

                <x-input-error for="type_id" class="mt-2" />
            </div>

            <div>
                <x-search-dropdown id="agent_id" label="{{ __('modules.tickets.agent') }}" model="agent_id" :options="$agents->map(fn($a) => ['id' => $a->ticket_agent_id, 'number' => $a->user->name])" placeholder="Select Agent" :required="true"/>
            </div>

            <div>
                <x-label for="status" value="{{ __('modules.settings.status') }}" required/>
                <x-select id="status" class="block w-full mt-1" wire:model.live="status">
                    <option value="">--</option>
                    <option value="open">{{ __('modules.tickets.open') }}</option>
                    <option value="pending">{{ __('modules.tickets.pending') }}</option>
                    <option value="resolved">{{ __('modules.tickets.resolved') }}</option>
                    <option value="closed">{{ __('modules.tickets.closed') }}</option>

                </x-select>
                <x-input-error for="status" class="mt-2" />
            </div>

            <div>
                <x-label for="subject" value="{{ __('modules.tickets.subject') }}" required/>
                <x-input id="subject" class="block mt-1 w-full" type="text" autofocus wire:model='subject' autocomplete="off"/>
                <x-input-error for="subject" class="mt-2" />
            </div>

            <div>
                <x-label for="reply" value="{{ __('modules.notice.description') }}" required
                    class="block font-medium text-sm text-gray-700 dark:text-gray-300" />

                <input id="reply" name="reply" wire:model="reply" type="hidden" />

                <div wire:ignore class="border border-gray-400 rounded-md mt-2 dark:border-gray-600 dark:bg-gray-300 pt-2">
                    <trix-editor
                        class="trix-content text-sm border-l-0 border-r-0 border-t border-b-0 focus:ring-0 dark:text-gray-300 dark:bg-gray-800 dark:border-gray-600"
                        x-on:trix-change="$wire.reply = $event.target.value"
                        x-ref="trixEditor"
                        data-gramm="false"
                        x-init="
                            window.addEventListener('reset-trix-editor', () => {
                                $refs.trixEditor.editor.loadHTML('');
                            });
                        ">
                    </trix-editor>
                </div>
                <x-input-error for="reply" class="mt-2" />
            </div>

            <div>
                <x-label for="files" value="{{ __('modules.tickets.fileUpload') }}"/>
                <input
                    id="files"
                    type="file"
                    wire:model="files"
                    multiple
                    class="block mt-1 w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none"
                />
                <x-input-error for="files.*" class="mt-2" />
            </div>


        </div>
        <div class="flex w-full pb-4 space-x-4 mt-6">
            <x-button>@lang('app.save')</x-button>
            <x-button-cancel  wire:click="$dispatch('hideAddTicket')" wire:loading.attr="disabled">@lang('app.cancel')</x-button-cancel>
        </div>
    </form>
</div>
