<div class="p-3 group flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:shadow-md transition bg-white dark:bg-gray-800">
    <div class="flex items-center">
        <div class="bg-gray-100 p-2 rounded-md">
            <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6,9.3L3.9,5.8l1.4-1.4l3.5,2.1v1.4l3.6,3.6c0,0.1,0,0.2,0,0.3L11.1,13L7.4,9.3H6z M21,17.8c-0.3,0-0.5,0-0.8,0  c0,0,0,0,0,0c-0.7,0-1.3-0.1-1.9-0.2l-2.1,2.4l4.7,5.3c1.1,1.2,3,1.3,4.1,0.1c1.2-1.2,1.1-3-0.1-4.1L21,17.8z M24.4,14  c1.6-1.6,2.1-4,1.5-6.1c-0.1-0.4-0.6-0.5-0.8-0.2l-3.5,3.5l-2.8-2.8l3.5-3.5c0.3-0.3,0.2-0.7-0.2-0.8C20,3.4,17.6,3.9,16,5.6  c-1.8,1.8-2.2,4.6-1.2,6.8l-10,8.9c-1.2,1.1-1.3,3-0.1,4.1l0,0c1.2,1.2,3,1.1,4.1-0.1l8.9-10C19.9,16.3,22.6,15.9,24.4,14z"></path>
            </svg>                 
        </div>
        <div class="grow ms-5">
            <a href="{{ route('maintenance.index') }}">
                <h3 wire:loading.class.delay='opacity-50'
                    @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                    @lang('modules.dashboard.maintenanceDues')
                </h3>
            </a>
            <p @class(['text-sm dark:text-neutral-200'])>{{ $maintenanceDues }}</p>
        </div>
    </div>
</div>
