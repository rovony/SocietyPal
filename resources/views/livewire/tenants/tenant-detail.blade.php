<div class="px-4 pt-6 bg-white  xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">
    @assets
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js" defer></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
    @endassets

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
                <a href="{{ route('tenants.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">@lang('modules.tenant.tenants')</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">@lang('modules.tenant.tenantDetails')</span>
                </div>
            </li>
            </ol>
        </nav>
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">@lang('modules.tenant.profile')</h1>
    </div>

    <div class="px-4 pt-6 xl:gap-4 dark:bg-gray-900">
        <div class="col-span-full">
            <div class="grid mb-4 lg:grid-cols-2 lg:gap-6">
                <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                    <div class="flex items-center space-x-4 sm:space-x-4">
                        <img class="w-20 h-20 mb-4 rounded-lg sm:mb-0 " src="{{ $tenant->user->profile_photo_url }}" alt="Profile">
                        <div class="space-y-3">
                            <dl class="flex flex-col gap-1 sm:flex-row">
                                <dt class="min-w-40">
                                    <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.user.name')</span>
                                </dt>
                                <dd>
                                    <ul>
                                        <li
                                            class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                            {{ $tenant->user->name }} ({{ $tenant->user->role->display_name }})
                                            <div class="ml-2">
                                                @if($tenant->user->status == 'active')
                                                    <x-badge type="success">{{ucFirst($tenant->user->status)}}</x-badge>
                                                @elseif($tenant->user->status == 'inactive')
                                                    <x-badge type="danger">{{ucFirst($tenant->user->status)}}</x-badge>
                                                @endif
                                            </div>
                                        </li>
                                    </ul>
                                </dd>
                            </dl>

                            <dl class="flex flex-col gap-1 sm:flex-row">
                                <dt class="min-w-40">
                                    <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.user.email')</span>
                                </dt>
                                <dd>
                                    <ul>
                                        <li
                                            class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                            {{ $tenant->user->email }}
                                        </li>
                                    </ul>
                                </dd>
                            </dl>

                            <dl class="flex flex-col gap-1 sm:flex-row">
                                <dt class="min-w-40">
                                    <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.user.phone')</span>
                                </dt>
                                <dd>
                                    <ul>
                                        <li class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                            @if($tenant->user->phone_number)
                                                +{{ $tenant->user->country_phonecode }} {{ $tenant->user->phone_number }}
                                            @else
                                                <span class="font-bold dark:text-gray-400">--</span>
                                            @endif
                                        </li>
                                    </ul>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div>
                        @if(user()->role->display_name == 'Admin')
                            <x-secondary-button wire:click="impersonate({{$tenant->user->id}})"  class="mt-3" wire:loading.attr="disabled">
                                {{ __('app.impersonate') }}
                            </x-secondary-button>
                        @endif
                    </div>
                </div>

                <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                    <div class="flex items-center space-x-4 sm:space-x-4">
                        <div class="space-y-3">
                            <dl class="flex flex-col gap-1 sm:flex-row">
                                <dt class="min-w-40">
                                    <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.apartmentNumber')</span>
                                </dt>
                                <dd>
                                    <ul>
                                        <li class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                            {{ $tenant->apartments->pluck('apartment_number')->implode(', ') }}
                                        </li>
                                    </ul>
                                </dd>
                            </dl>
                            <dl class="flex flex-col gap-1 sm:flex-row">
                                <dt class="min-w-40">
                                    <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.parkingCode')</span>
                                </dt>
                                <dd>
                                    <ul>
                                        <li class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                            {{ $parkings->isNotEmpty() ? $parkings->pluck('parking_code')->implode(',  ') : '--' }}
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

    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" role="tablist">
            <li class="me-2">
                <a href="javascript:;" wire:click="setActiveTab('apartment')"
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300", 'border-transparent' => ($activeTab != 'apartment'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeTab == 'apartment')])>@lang('menu.apartmentManagement')</a>
            </li>
            <li class="me-2">
                <a href="javascript:;" wire:click="setActiveTab('rent')"
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300", 'border-transparent' => ($activeTab != 'rent'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeTab == 'rent')])>@lang('menu.rentManagement')</a>
            </li>
            <li class="me-2">
                <a href="javascript:;" wire:click="setActiveTab('documents')"
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300", 'border-transparent' => ($activeTab != 'documents'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeTab == 'documents')])>@lang('modules.tenant.tenantDocuments')</a>
            </li>
        </ul>
    </div>

    <!-- Tab content -->
    <div id="profile-tabs-content">
        @if ($activeTab === 'apartment')
            <livewire:tenants.show-apartments :tenantId="$tenantId" />
        @elseif ($activeTab === 'rent')
            @include('tenants.show-rents')
        @elseif ($activeTab === 'documents')
            <livewire:tenants.tenant-document :tenantId="$tenantId" />
        @endif
    </div>

</div>
