<div class="p-3 group flex flex-col border border-gray-200 dark:border-gray-700 shadow-sm rounded-lg hover:shadow-md transition bg-white dark:bg-gray-800">
    <div class="flex items-center">
        <div class="bg-gray-100 p-2 rounded-md">
            <svg class="w-4 h-4 text-gray-500 dark:text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 512.003 512.003">
                <path d="M256.001,238.426c65.738,0,119.219-53.48,119.219-119.219C375.221,53.475,321.739,0,256.001,0 S136.782,53.475,136.782,119.207C136.782,184.946,190.263,238.426,256.001,238.426z M256.001,38.705 c44.397,0,80.516,36.114,80.516,80.503c0,44.397-36.119,80.516-80.516,80.516s-80.516-36.119-80.516-80.516 C175.485,74.819,211.606,38.705,256.001,38.705z"></path>
                <path d="M256.001,253.692c-97.97,0-177.583,79.741-177.583,177.583v61.376c0,10.687,8.664,19.352,19.352,19.352h316.462 c10.687,0,19.352-8.664,19.352-19.352v-61.377C433.584,333.215,353.747,253.692,256.001,253.692z M185.045,473.298h-67.923 c0-38.412-8.95-115.482,67.923-161.362V473.298z M288.254,473.299h-64.506v-24.156h64.506V473.299z M288.254,410.44h-64.506 V296.184c4.229-1.01,8.533-1.823,12.901-2.434v13.23c0,10.687,8.664,19.352,19.352,19.352s19.352-8.664,19.352-19.352v-13.23 c4.368,0.612,8.672,1.424,12.901,2.434V410.44z M394.881,473.298h-67.923V311.935 C403.476,357.604,394.881,434.069,394.881,473.298z"></path>
            </svg>                   
        </div>
        <div class="grow ms-5">
            <a href="{{ route('owners.index') }}">
                <h3 wire:loading.class.delay='opacity-50'
                    @class(['font-semibold dark:group-hover:text-neutral-400 dark:text-neutral-200 text-gray-800 group-hover:text-skin-base'])>
                    @lang('menu.ownerManagement')
                </h3>
            </a>
            <p @class(['text-sm dark:text-neutral-200'])>{{ $ownerCount }}</p>
        </div>
    </div>
</div>
