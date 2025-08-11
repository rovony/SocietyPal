<div>

    <div class="flex flex-col px-4 my-4">
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-7 sm:gap-4">
            @foreach ($tabs as $tab)
                <a @class(['group flex flex-col border shadow-sm rounded-lg hover:shadow-md transition dark:bg-gray-700 dark:border-gray-600', 'bg-skin-base dark:bg-skin-base' => ($activeTab == $tab), 'bg-white' => ($activeTab != $tab)]) wire:click="showTab('{{ $tab }}')" href="javascript:void(0);">
                    <div class="p-3">
                        <div class="flex items-center justify-center">
                            <h3
                            @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-sm', 'text-gray-800 group-hover:text-skin-base' => ($activeTab != $tab), 'text-white group-hover:text-white' => ($activeTab == $tab)])>
                                {{ $tab }}
                            </h3>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if ($activeTab == "Ticket Type")
            <div class="w-full">
                <div class="flex items-center gap-4 my-4">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('modules.settings.ticketTypeSetting')</h1>
                </div>
            </div>

            <livewire:settings.ticket-type-settings key='ticket-item-{{ microtime() }}' />

        @endif

        @if ($activeTab == "Ticket Agent")
            <div class="w-full">
                <div class="flex items-center gap-4 my-4">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('modules.settings.ticketAgentSetting')</h1>
                </div>
            </div>

        <livewire:settings.ticket-agent-settings key='ticket-item-{{ microtime() }}' />

        @endif
    </div>

</div>
