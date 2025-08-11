<div class="px-4 pt-6 bg-white xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">

    <div class="mb-4 col-span-full xl:mb-2">
        <nav class="flex mb-5" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                @lang('menu.dashboard')
                </a>
            </li>
            <li>
                <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <a href="{{ route('assets.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">@lang('modules.assets.asset')</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">@lang('modules.assets.assetDetails')</span>
                </div>
            </li>
            </ol>
        </nav>
    </div>
    <div>
        <div class="items-center justify-between block p-4 bg-white sm:flex dark:bg-gray-800 dark:border-gray-700">
            <div class="w-full mb-1">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    @lang('modules.assets.assetDetails')</h1>

            </div>
        </div>
        <div class="px-4 pt-6 xl:gap-4 dark:bg-gray-900">
            <div class="col-span-full">
                <div class="grid mb-4 lg:grid-cols-2 lg:gap-6">
                    <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <div class="flex items-center space-x-4 sm:space-x-4">
                            <div class="space-y-3">
                                <dl class="flex flex-col gap-1 sm:flex-row">
                                    <dt class="min-w-40">
                                        <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.assets.name')</span>
                                    </dt>
                                    <dd>
                                        <ul>
                                            <li
                                                class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                                {{ $asset->name }}

                                            </li>
                                        </ul>
                                    </dd>
                                </dl>

                                <dl class="flex flex-col gap-1 sm:flex-row">
                                    <dt class="min-w-40">
                                        <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.assets.category')</span>
                                    </dt>
                                    <dd>
                                        <ul>
                                            <li
                                                class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                                {{ $asset->category->name }}
                                            </li>
                                        </ul>
                                    </dd>
                                </dl>

                                <dl class="flex flex-col gap-1 sm:flex-row">
                                    <dt class="min-w-40">
                                        <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.assets.ownerName')</span>
                                    </dt>
                                    <dd>
                                        <ul>
                                            <li
                                                class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                                @if($asset->apartments->user->name??'')
                                                    {{ $asset->apartments->user->name }}
                                                @else
                                                    <span class="font-bold dark:text-gray-400">--</span>
                                                @endif
                                            </li>
                                        </ul>
                                    </dd>
                                </dl>

                                <dl class="flex flex-col gap-1 sm:flex-row">
                                    <dt class="min-w-40">
                                        <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.assets.condition')</span>
                                    </dt>
                                    <dd>
                                        <ul>
                                            <li
                                                class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                                {{ $asset->condition }}
                                            </li>
                                        </ul>
                                    </dd>
                                </dl>



                            </div>
                        </div>


                    </div>
                    <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <div class="flex items-center space-x-4 sm:space-x-4">
                            <div class="space-y-3">
                                <dl class="flex flex-col gap-1 sm:flex-row">
                                    <dt class="min-w-40">
                                        <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.assets.tower')</span>
                                    </dt>
                                    <dd>
                                        <ul>
                                            <li
                                                class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                                {{ $asset->towers->tower_name }}
                                            </li>
                                        </ul>
                                    </dd>
                                </dl>
                                <dl class="flex flex-col gap-1 sm:flex-row">
                                    <dt class="min-w-40">
                                        <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.assets.floor')</span>
                                    </dt>
                                    <dd>
                                        <ul>
                                            <li
                                                class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                                {{ $asset->floors->floor_name ?? '--' }}
                                            </li>
                                        </ul>
                                    </dd>
                                </dl>

                                <dl class="flex flex-col gap-1 sm:flex-row">
                                    <dt class="min-w-40">
                                        <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.assets.apartmentNumber')</span>
                                    </dt>
                                    <dd>
                                        <ul>
                                            <li
                                                class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                                {{ $asset->apartments->apartment_number ?? '--' }}
                                            </li>
                                        </ul>
                                    </dd>
                                </dl>

                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
            <li class="me-2">
                <a href="javascript:;" wire:click="setActiveTab('maintenance')"
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300", 'border-transparent' => ($activeTab != 'maintenance'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeTab == 'maintenance')])>@lang('modules.assets.maintenanceSchedule')</a>
            </li>
            <li class="me-2">
                <a href="javascript:;" wire:click="setActiveTab('issue')"
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300", 'border-transparent' => ($activeTab != 'issue'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeTab == 'issue')])>@lang('modules.assets.IssueReport')</a>
            </li>
        </ul>
    </div>
    <!-- Tab content -->
    <div id="profile-tabs-content">
        @if ($activeTab === 'maintenance')
            <livewire:assets.show-maintenance :assetId="$assetId" />
        @elseif ($activeTab === 'issue')
            <livewire:assets.show-issues :assetId="$assetId" />
        @endif
    </div>

</div>
