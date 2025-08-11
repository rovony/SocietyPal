<div class="p-3 group flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:shadow-md transition bg-white dark:bg-gray-800">
    <div class="flex items-center">
        <div class="bg-gray-100 p-2 rounded-md">
            <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19L9 16M9 16L9 12M15 12V19M15 19L15 16M9 16H15M12 19V12M4 21H20M4 8L8 4L16 4L20 8V21H4V8Z"></path>
            </svg>                      
        </div>
        <div class="grow ms-5">
            <a href="{{ route('towers.index') }}">
                <h3 wire:loading.class.delay='opacity-50'
                    @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                    @lang('modules.dashboard.towerCount')
                </h3>
            </a>
            <p @class(['text-sm dark:text-neutral-200'])>{{ $towerCount }}</p>
        </div>
    </div>
</div>
