<div>
    <div class="p-4 bg-white block sm:flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full mb-1">
            <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                @lang('saas.societyDetails')</h1>

        </div>
    </div>

    <div class="px-4 pt-6 xl:gap-4 dark:bg-gray-900">

        <div class="grid lg:grid-cols-2 lg:gap-6 mb-4">
            <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800 flex items-center">
                <div class="flex sm:space-x-4 space-x-4 items-center">
                    <img class="rounded-lg w-20 h-20" src="{{ $society->logoUrl }}" alt="{{ $society->name }}">
                    <div>
                        <h3 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">{{ $society->name }}
                            @if ($society->license_type != 'paid')
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">FREE</span>
                            @else
                                <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">PAID</span>
                            @endif
                        </h3>
                        @if(module_enabled('Subdomain'))
                            <div class="mb-2">
                                <a href="https://{{ $society->sub_domain }}" target="_blank" class="underline flex items-center gap-1 underline-offset-1 font-normal dark:text-white">https://{{ $society->sub_domain }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5"/>
                                        <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z"/>
                                    </svg>
                                </a>
                            </div>
                     @endif
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {!! nl2br($society->address) !!}
                        </div>
                    </div>
                    <div>
                        <x-secondary-button class="lg:ms-3 inline-flex items-center gap-1" wire:click="impersonate({{$society->id}})" wire:loading.attr="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-4 h-4" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="m4.736 1.968-.892 3.269-.014.058C2.113 5.568 1 6.006 1 6.5 1 7.328 4.134 8 8 8s7-.672 7-1.5c0-.494-1.113-.932-2.83-1.205l-.014-.058-.892-3.27c-.146-.533-.698-.849-1.239-.734C9.411 1.363 8.62 1.5 8 1.5s-1.411-.136-2.025-.267c-.541-.115-1.093.2-1.239.735m.015 3.867a.25.25 0 0 1 .274-.224c.9.092 1.91.143 2.975.143a30 30 0 0 0 2.975-.143.25.25 0 0 1 .05.498c-.918.093-1.944.145-3.025.145s-2.107-.052-3.025-.145a.25.25 0 0 1-.224-.274M3.5 10h2a.5.5 0 0 1 .5.5v1a1.5 1.5 0 0 1-3 0v-1a.5.5 0 0 1 .5-.5m-1.5.5q.001-.264.085-.5H2a.5.5 0 0 1 0-1h3.5a1.5 1.5 0 0 1 1.488 1.312 3.5 3.5 0 0 1 2.024 0A1.5 1.5 0 0 1 10.5 9H14a.5.5 0 0 1 0 1h-.085q.084.236.085.5v1a2.5 2.5 0 0 1-5 0v-.14l-.21-.07a2.5 2.5 0 0 0-1.58 0l-.21.07v.14a2.5 2.5 0 0 1-5 0zm8.5-.5h2a.5.5 0 0 1 .5.5v1a1.5 1.5 0 0 1-3 0v-1a.5.5 0 0 1 .5-.5"/>
                            </svg>
                            {{ __('app.impersonate') }}
                        </x-secondary-button>
                    </div>

                </div>
            </div>
            <div class="p-4 mb-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">@lang('modules.society.currentPackage')</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300"><span class="font-semibold">@lang('modules.package.packageName'):</span> {{ $society->package?->package_name ?? __('messages.noPackageFound') }}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1"><span class="font-semibold">@lang('modules.package.packageType'):</span> {{ ucfirst($society->package?->package_type->value) }} ({{ ucfirst($society?->package_type) }})</p>
                        @if ($society->package?->package_type->value == 'trial')
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                            <span class="font-semibold">@lang('modules.package.trialExpireOn'):</span> {{ $society?->trial_ends_at ? \Carbon\Carbon::parse($society->trial_ends_at)->format('d M, Y') : '--' }}
                        </p>
                        @elseif ($society->package?->package_type->value != 'lifetime')
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                            <span class="font-semibold">@lang('modules.package.licenceExpiresOn'):</span> {{ optional($society->license_expire_on)->format('d M, Y') ?? '--' }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <div class="grid lg:grid-cols-2 lg:gap-6 mb-4">
            <div
            class="p-4  bg-white border border-gray-200 rounded-lg shadow-sm  dark:border-gray-700 sm:p-6 dark:bg-gray-800">
                <!-- List -->
                <div class="space-y-3">
                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('app.id')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    {{ $society->id }}
                                </li>
                            </ul>
                        </dd>
                    </dl>
                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('app.status')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    @if ($society->is_active == true)
                                        <span class="bg-green-100 uppercase text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">@lang('app.active')</span>
                                    @else
                                        <span class="bg-red-100 uppercase text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">@lang('app.inactive')</span>
                                    @endif
                                </li>
                            </ul>
                        </dd>
                    </dl>
                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.user.phone')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    {{ $society->phone_number }}
                                </li>
                            </ul>
                        </dd>
                    </dl>

                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.user.email')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    {{ $society->email }}
                                </li>
                            </ul>
                        </dd>
                    </dl>

                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.societyTimezone')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    {{ $society->timezone }}
                                </li>
                            </ul>
                        </dd>
                    </dl>

                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.societyCountry')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    <img class="h-3.5 w-3.5 rounded-full mr-2"
                                    src="{{ $society->country->flagUrl }}" alt="">
                                    {{ $society->country->countries_name }}
                                </li>
                            </ul>
                        </dd>
                    </dl>

                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('modules.settings.societyCurrency')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    {{ $society?->currency?->currency_name }} ({{ $society?->currency?->currency_code }})
                                </li>
                            </ul>
                        </dd>
                    </dl>
                    <dl class="flex flex-col sm:flex-row gap-1">
                        <dt class="min-w-40">
                            <span class="block text-sm text-gray-500 dark:text-neutral-500">@lang('app.dateTime')</span>
                        </dt>
                        <dd>
                            <ul>
                                <li
                                    class="me-1 inline-flex items-center text-sm text-gray-800 dark:text-neutral-200">
                                    {{ $society->created_at->timezone(global_setting()->timezone ?? 'Asia/Kolkata')->translatedFormat('D, d M Y, h:i A') }}
                                </li>
                            </ul>
                        </dd>
                    </dl>

                </div>
                <!-- End List -->
            </div>

            @if ($societyAdmin)
                <div class="w-full bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 flex  flex-col items-center justify-center">
                    <h5 class="mb-6  text-lg font-medium text-gray-900 dark:text-white">@lang('saas.firstAdmin')</h5>

                    <div class="flex flex-col items-center">
                        <img class="w-24 h-24 mb-3 rounded-full" src="{{ $societyAdmin->profile_photo_url }}" alt="Bonnie image"/>
                        <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">{{ $societyAdmin->name }}</h5>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $societyAdmin->email }}</span>
                        <div class="flex mt-4 md:mt-6">
                            <x-button wire:click="$set('showPasswordModal', true)">@lang('saas.changePassword')</x-button>
                        </div>
                    </div>
                </div>
            @else
                <div class="w-full bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 flex  flex-col items-center justify-center">
                    <h5 class="mb-6  text-lg font-medium text-gray-900 dark:text-white">No Admin Found</h5>
                </div>
            @endif

        </div>


    </div>
    <div class="flex flex-col mb-12">
        <h3 class="px-4 mb-4 text-xl font-semibold dark:text-white">@lang('saas.payments')</h3>

        <livewire:billing.invoice-table :societyId='$society->id' :search='$search' key='payment-table-{{ microtime() }}' />

    </div>
    <x-dialog-modal wire:model.live="showPasswordModal">
        <x-slot name="title">
            @lang('saas.changePassword')
        </x-slot>

        <x-slot name="content">
            <form wire:submit="submitForm">
                @csrf

                <div class="space-y-4">
                    <div>
                        <x-label for="password" value="{{ __('Password') }}"/>
                        <x-input id="password" class="block mt-1 w-full" type="password" autocomplete="new-password"
                                wire:model='password'/>
                        <x-input-error for="password" class="mt-2"/>
                    </div>
                    <x-button>@lang('app.save')</x-button>
                </div>
            </form>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('showPasswordModal')" wire:loading.attr="disabled">@lang('app.cancel')</x-secondary-button>
        </x-slot>
</x-dialog-modal>

</div>
