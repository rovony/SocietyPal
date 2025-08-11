<div>


    <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px">
            <li class="me-2">
                <a href="{{ route('events.index').'?tab=table' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'table'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'table')])>@lang('modules.event.tableView')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('events.index').'?tab=calender' }}"  onclick="event.preventDefault(); window.location.href=this.href;"
                    @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'calender'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'calender')])>@lang('modules.event.calendarView')</a>
            </li>

        </ul>
    </div>

    <div class="grid grid-cols-1 pt-6 dark:bg-gray-900">
        <div>
            @switch($activeSetting)
                @case('table')
                    @livewire('event.event-list')
                    @break
                @case('calender')
                    @livewire('event.calender-view')
                    @break
                @default
            @endswitch
        </div>


    </div>

</div>

