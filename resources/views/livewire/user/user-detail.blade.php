<div class="px-4 pt-6 bg-white  xl:grid-cols-3 xl:gap-4 dark:bg-gray-900">

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
                <a href="{{ route('users.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">@lang('modules.user.users')</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">@lang('modules.user.userDetails')</span>
                </div>
            </li>
            </ol>
        </nav>
    </div>
    <div>
        <div class="items-center justify-between block p-4 bg-white sm:flex dark:bg-gray-800 dark:border-gray-700">
            <div class="w-full mb-1">
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    @lang('modules.user.userDetails')</h1>

            </div>
        </div>

        <div class="px-4 pt-6 xl:gap-4 dark:bg-gray-900 ">
            <div class="col-span-full">
                <div class="grid mb-4 lg:grid-cols-2 lg:gap-6">

                    <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                        <div class="flex items-center space-x-4 sm:space-x-4">
                            <img class="w-20 h-20 mb-4 rounded-lg sm:mb-0 " src="{{ $user->profile_photo_url }}" alt="Profile">
                            <div class="space-y-3">
                                <dl class="flex flex-col gap-1 sm:flex-row">
                                    <dt class="min-w-40">
                                        <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.user.name')</span>
                                    </dt>
                                    <dd>
                                        <ul>
                                            <li
                                                class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                                {{ $user->name }} ({{ $user->role->display_name }})
                                                <div class="ml-2">
                                                    @if($user->status == 'active')
                                                        <x-badge type="success">{{ucFirst($user->status)}}</x-badge>
                                                    @elseif($user->status == 'inactive')
                                                        <x-badge type="danger">{{ucFirst($user->status)}}</x-badge>
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
                                                {{ $user->email }}
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
                                            <li
                                                class="inline-flex items-center text-sm text-gray-800 me-1 dark:text-neutral-200">
                                                @if($user->phone_number)
                                                    @if($user->country_phonecode)
                                                        +{{ $user->country_phonecode }}
                                                    @endif
                                                    {{ $user->phone_number }}
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
                            @if(user()->role->display_name == 'Admin' && $user->id != user()->id)
                                <x-secondary-button wire:click="impersonate({{$user->id}})"  class="mt-3" wire:loading.attr="disabled">
                                    {{ __('app.impersonate') }}
                                </x-secondary-button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
