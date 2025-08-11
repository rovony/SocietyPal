<style>
    .invalid-feedback {
        color: red;
        font-size: 12px;
    }

    .alert{
        padding: 10px;
        border-radius: 5px;
    }

    .alert-success {
        color: #027302;
        font-size: 12px;
        background-color: #d1fadf;
        border: 1px solid #027302;
    }

    .alert-danger {
        color: #a50505;
        font-size: 12px;
        background-color: #f8d7da;
        border: 1px solid #a50505;
    }

    .blur-code {
        filter: blur(3px);
    }

    .page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        backdrop-filter: blur(3px);
    }

    .page-loader.show {
        display: flex;
    }

    .loader-container {
        background: white;
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        text-align: center;
        animation: slideDown 0.3s ease-out;
    }

    .loader-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    .loader-text {
        margin-top: 1rem;
        color: #2d3748;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .loader-text svg {
        width: 16px;
        height: 16px;
        animation: pulse 1.5s ease-in-out infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes pulse {
        0% { opacity: 0.5; }
        50% { opacity: 1; }
        100% { opacity: 0.5; }
    }

    @keyframes slideDown {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    .loader-spinner {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-left-color: #000;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        animation: spin 1s linear infinite;
    }

</style>

<div class="mb-10 space-y-6">
    @if(count($allModules) > 0)
    <div class="flex mb-4">
        <x-primary-link href="{{ route('superadmin.custom-modules.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white uppercase bg-blue-600 border border-transparent rounded-md">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg> @lang('app.moduleSettingsInstall')
        </x-primary-link>
    </div>
    @endif

    <div id="update-area" class="hidden p-6 my-5 bg-white rounded-lg shadow-sm">
        {{__('app.loading')}}
    </div>

    <div class="hidden p-4 mb-4 text-red-600 rounded-lg bg-red-50" id="custom-module-alert"></div>

    <!-- Page Loader -->
    <div id="page-loader" class="page-loader">
        <div class="loader-container">
            <div class="loader-spinner animate-spin"></div>
            <div class="loader-text">
                <svg class="animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Updating module status...
            </div>
        </div>
    </div>

    @if(session('subdomain_module_activated') == 'activated')
        <div class="p-4 mb-4 border-l-4 border-blue-500 rounded bg-blue-50 ">
            <!-- Success Header -->
            <div class="flex items-center gap-3 mb-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-blue-800">{{ __('modules.custom.subdomainModuleActivated') }}</h3>
                    <p class="text-sm text-blue-600">{{ __('modules.custom.systemUpgraded') }}</p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="space-y-4">
                <!-- New Login URL -->
                <div class="flex items-center gap-3 p-3 bg-white border border-blue-100 rounded dark:bg-gray-800 dark:text-white">
                    <div class="flex-grow">
                        <span class="text-sm text-gray-600 dark:text-white">{{ __('modules.custom.superadminUrl') }}</span>
                        <code class="px-2 py-1 ml-2 text-sm rounded bg-gray-50 dark:bg-gray-200 dark:text-black">{{ url('/') }}/super-admin-login</code>
                    </div>
                    <button class="copy-url p-2 hover:bg-gray-100 rounded dark:text-black dark:bg-gray-800 data-url="{{ url('/') }}/super-admin-login">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                    </button>
                </div>

                <!-- Important Info -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 border-l-4 rounded-r bg-amber-50 border-amber-400">
                        <span class="font-medium text-amber-800">{{ __('modules.custom.importantChanges') }}</span>
                        <ul class="mt-1 ml-4 text-sm list-disc text-amber-700">
                            <li>{{ __('modules.custom.publicLoginPage') }}</li>
                            <li>{{ __('modules.custom.companySpecificSubdomainLogins') }}</li>
                            <li>{{ __('modules.custom.updateUserAccessPoints') }}</li>
                        </ul>
                    </div>

                    <div class="p-3 border-l-4 border-red-400 rounded-r bg-red-50">
                        <span class="font-medium text-red-800">{{ __('modules.custom.requiredActions') }}</span>
                        <ul class="mt-1 ml-4 text-sm text-red-700 list-disc">
                            <li>{{ __('modules.custom.configureDnsSettings') }}</li>
                            <li>{{ __('modules.custom.setupWildcardSubdomains') }}</li>
                            <li>{{ __('modules.custom.updateSavedBookmarks') }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Help Link -->
                <div class="text-sm text-gray-600">
                    {{ __('modules.custom.needHelp') }} <a href="https://youtu.be/4mLyhf43_wI" class="text-blue-600 hover:text-blue-800" target="_blank">{{ __('modules.custom.watchSetupGuide') }}</a>
                </div>
            </div>
        </div>
    @endif

    @includeIf('languagepack::module-activated-alert')
    @include('custom-modules.sections.universal-bundle')


    <div class="bg-white border rounded-lg mb-100 dark:bg-gray-800">
        <x-table class="custom-modules-table dark:bg-gray-800" headType="bg-gray-50" id="custom-modules-table">
            <x-slot name="thead" class="dark:bg-gray-800">
                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">@lang('app.name')</th>
                @if (!$universalBundle)
                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">@lang('app.purchaseCode')</th>
                @endif
                <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">@lang('app.moduleVersion')</th>
                @if (!$universalBundle)
                <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">@lang('app.notify')</th>
                @endif
                <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">@lang('app.status')</th>
            </x-slot>

            @forelse ($allModules as $key=>$module)
                @php
                    $moduleKey = strtolower($module);
                    $fetchSetting = null;

                    // Get module config settings
                    $settingClass = config([strtolower($module) . '.setting', null]);
                    $verificationRequired = config([strtolower($module) . '.verification_required', false]);
                    $envatoId = config([strtolower($module) . '.envato_item_id', null]);

                    // Load config from Modules directory if plugin exists
                    if (in_array($moduleKey, custom_module_plugins())) {
                        $configPath = base_path("Modules/$module/Config/config.php");

                        if (file_exists($configPath)) {
                            $moduleConfig = require $configPath;
                            $settingClass = $moduleConfig['setting'] ?? $settingClass;
                            $verificationRequired = $moduleConfig['verification_required'] ?? $verificationRequired;
                            $envatoId = $moduleConfig['envato_item_id'] ?? $envatoId;

                            if ($settingClass) {
                                $fetchSetting = $settingClass::first();
                            }
                        }
                    }
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                        <span class="font-semibold font-bold text-gray-900">{{ $module }}</span>
                        @if (module_enabled('UniversalBundle') && isInstallFromUniversalBundleModule($key))
                            <i class="text-blue-500 cursor-pointer icon fas fa-info-circle" data-toggle="tooltip"
                                data-original-title="{{__('universalbundle::app.moduleInfo')}}"></i>
                        @endif
                    </td>
                    @if (!$universalBundle)
                    <td class="flex items-center gap-2 px-6 py-4 text-sm whitespace-nowrap">

                        @if ($fetchSetting)
                            @if ($verificationRequired)
                                @include('custom-modules.sections.purchase-code')
                            @endif
                        @endif
                    </td>
                    @endif
                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                        @if ($settingClass)
                            @include('custom-modules.sections.version')

                            @if ($plugins->where('envato_id', $envatoId)->first() && !(module_enabled('UniversalBundle') && isInstallFromUniversalBundleModule($key)))
                                @include('custom-modules.sections.module-update')
                            @endif
                        @endif
                    </td>

                    @if (!$universalBundle)
                    <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                        @if ($fetchSetting)
                        <div class="relative inline-block ml-2 group">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer change-module-notification"
                                    @checked($fetchSetting->notify_update)
                                    id="module-notification-{{ $key }}" data-module-name="{{ $module }}">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>

                            <!-- Tooltip -->
                            <div class="pointer-events-none hidden group-hover:block absolute z-50 w-75 px-4 py-2
                                      right-full mr-5 top-1/2 -translate-y-1/2
                                      text-sm text-white bg-gray-900 rounded-lg shadow-sm
                                      before:content-[''] before:absolute before:top-1/2 before:-translate-y-1/2
                                      before:right-[-6px] before:border-[6px] before:border-transparent
                                      before:border-l-gray-900">
                                @lang('app.moduleNotifySwitchMessage', ['name' => $module])
                            </div>
                        </div>
                        @endif
                    </td>
                    @endif

                    <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                        <div class="relative inline-block ml-2 group" data-toggle="tooltip"
                             data-original-title="@lang('app.moduleSwitchMessage', ['name' => $module])">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" @if (in_array($key, custom_module_plugins())) checked @endif
                                    class="sr-only peer change-module-status"
                                    id="module-{{ $key }}" data-module-name="{{ $module }}">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>

                            <!-- Tooltip -->
                            <div class="pointer-events-none hidden group-hover:block absolute z-50 w-75 px-4 py-2
                                      right-full mr-5 top-1/2 -translate-y-1/2
                                      text-sm text-white bg-gray-900 rounded-lg shadow-sm
                                      before:content-[''] before:absolute before:top-1/2 before:-translate-y-1/2
                                      before:right-[-6px] before:border-[6px] before:border-transparent
                                      before:border-l-gray-900">
                                @lang('app.moduleSwitchMessage', ['name' => $module])
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700">
                    <td colspan="5" class="px-6 py-4 text-center">
                        <div class="flex flex-col items-center justify-center space-y-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm text-gray-500">@lang('messages.noRecordFound')</span>
                            <p class="text-sm text-gray-600">@lang('messages.moduleSettingsInstall')</p>

                            <x-primary-link href="{{ route('superadmin.custom-modules.create') }}" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white uppercase bg-blue-600 border border-transparent rounded-md ">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg> @lang('app.moduleSettingsInstall')
                            </x-primary-link>
                        </div>
                    </td>
                </tr>
            @endforelse
        </x-table>
    </div>

    @include('vendor.froiden-envato.update.plugins', ['allModules' => $allModules])
</div>

<!-- Subdomain Activation Modal -->
<div id="subdomainModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

        <div class="relative overflow-hidden transition-all transform bg-white rounded-lg shadow-xl sm:w-full sm:max-w-4xl">
            <div class="bg-white">
                <div class="px-4 py-3 border-b sm:px-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">@lang('modules.custom.confirmSubdomainModuleActivation')</h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 close-modal">
                            <span class="sr-only">@lang('app.close')</span>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-2 py-3 sm:p-4">
                    <div class="text-left">
                        <!-- Warning Banner -->
                        <div class="p-4 mb-4 border-l-4 bg-amber-50 border-amber-400">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="text-lg fas fa-exclamation-triangle text-amber-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-amber-700">
                                        @lang('modules.custom.ensureWildcardSubdomainsConfigured')
                                    </p>
                                    <a href="https://youtu.be/4mLyhf43_wI"
                                       class="inline-flex items-center justify-center gap-2 px-4 py-2 mt-3 transition-colors duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 group"
                                       target="_blank">
                                        <svg class="w-5 h-5 text-red-600 group-hover:text-red-700" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">@lang('modules.custom.watchConfigurationGuide')</span>
                                        <svg class="w-3 h-3 text-gray-500 group-hover:text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                            <polyline points="15 3 21 3 21 9"></polyline>
                                            <line x1="10" y1="14" x2="21" y2="3"></line>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Changes Section -->
                        <div class="mb-4 bg-white border border-gray-200 shadow-sm rounded-xl">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h3 class="text-base font-semibold text-gray-900">
                                    <i class="mr-2 text-blue-500 fas fa-info-circle"></i>
                                    @lang('modules.custom.importantChangesAfterActivation')
                                </h3>
                            </div>
                            <div class="px-4 py-3">
                                <ul class="space-y-3">
                                    <li class="flex items-start">
                                        <i class="mt-1 mr-3 text-gray-400 fas fa-link"></i>
                                        <div class="flex-grow">
                                            <span class="block text-sm text-gray-700">@lang('modules.custom.newSuperadminLoginURL')</span>
                                            <div class="flex items-center gap-2 mt-1">
                                                <code class="flex-grow px-3 py-2 font-mono text-sm text-gray-600 border border-gray-200 rounded bg-gray-50">
                                                    {{ url('/') }}/super-admin-login
                                                </code>
                                                <button class="p-2 transition-colors duration-200 rounded-lg copy-url hover:bg-gray-100"
                                                        data-url="{{ url('/') }}/super-admin-login"
                                                        title="Copy URL">
                                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <p class="mt-1 text-xs text-blue-600">@lang('modules.custom.clickCopyIcon')</p>
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="mt-1 mr-3 text-green-500 fas fa-check-circle"></i>
                                        <div>
                                            <span class="block text-sm font-medium text-gray-900">@lang('modules.custom.enhancedSecurity')</span>
                                            <span class="text-sm text-gray-600">@lang('modules.custom.publicLoginPageDisabled')</span>
                                        </div>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="mt-1 mr-3 text-blue-500 fas fa-building"></i>
                                        <div>
                                            <span class="block text-sm font-medium text-gray-900">Societies-Specific Access</span>
                                            <span class="text-sm text-gray-600">Each society gets a dedicated login page on their subdomain</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Warning Section -->
                        <div class="p-4 mb-4 border-l-4 border-red-400 rounded-r bg-red-50">
                            <div class="font-medium text-red-800">@lang('modules.custom.warning')</div>
                            <ul class="mt-2 ml-4 text-sm text-red-700 list-disc">
                                <li>@lang('modules.custom.previousAdminLoginUrlDisabled')</li>
                                <li>@lang('modules.custom.dnsWildcardSubdomainsConfigured')</li>
                                <li>@lang('modules.custom.updateBookmarks')</li>
                            </ul>
                        </div>

                        <!-- Confirmation -->
                        <div class="mt-4">
                            <p class="mb-4 text-sm text-center text-gray-600">@lang('modules.custom.proceedWithActivation')</p>

                            <div class="flex justify-center gap-3">
                                <button type="button"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg cancel-subdomain-activation hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        @lang('app.cancel')
                                </button>
                                <button type="button"
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg confirm-subdomain-activation hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        @lang('modules.custom.confirmActivation')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verify Purchase Code Modal -->
<div id="verifyPurchaseModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

        <div class="relative overflow-hidden transition-all transform bg-white rounded-lg shadow-xl sm:w-full sm:max-w-lg">
            <div class="bg-white">
                <div class="px-4 py-3 border-b sm:px-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="verifyModalHeading">@lang('modules.custom.verifyPurchaseCode')</h3>
                        <button type="button" class="text-gray-400 hover:text-gray-500 close-modal">
                            <span class="sr-only">@lang('app.close')</span>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-4 py-5 sm:p-6" id="verifyModalBody">
                    <!-- Content will be loaded dynamically -->
                </div>

                <div class="px-4 py-3 border-t sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" class="inline-flex justify-center w-full px-3 py-2 mt-3 text-sm font-semibold text-gray-900 bg-white rounded-md shadow-sm close-modal ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">@lang('app.close')</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const SUBDOMAIN_MODAL = '#subdomainModal';
    const VERIFY_MODAL = '#verifyPurchaseModal';
    const PAGE_LOADER = '#page-loader';

    function showLoader() {
        $(PAGE_LOADER).addClass('show');
    }

    function hideLoader() {
        $(PAGE_LOADER).removeClass('show');
    }

    function updateModuleStatus(module, moduleStatus) {
        let url = "{{ route('superadmin.custom-modules.update', ':module') }}";
        url = url.replace(':module', module);

        $('#custom-module-alert').addClass('d-none');

        // Show page loader
        showLoader();

        $.easyAjax({
            url: url,
            type: "POST",
            disableButton: true,
            buttonSelector: "#custom-modules-table",
            container: '#custom-modules-table',
            blockUI: false,
            data: {
                'id': module,
                'status': moduleStatus,
                '_method': 'PUT',
                '_token': '{{ csrf_token() }}'
            },
            success: function(response) {
                // hideLoader();
                if (response.status === 'success') {
                    window.location.reload();
                }
            },
            error: function (response) {
                hideLoader();
                if (response.responseJSON) {
                    $('#custom-module-alert').html(response.responseJSON.message).removeClass('d-none');
                    $('#module-' + module).prop('checked', false);
                }
            },
            complete: function() {
                // hideLoader();
            }
        });
    }
</script>

<script src="{{ asset('vendor/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/froiden-helper/helper.js') }}"></script>

@include('vendor.froiden-envato.update.update_script')

<script>
    $('body').on('click', '.show-hide-purchase-code', function () {
        $('> .eye-icon', this).toggleClass('hidden');
        $('> .eye-slash-icon', this).toggleClass('hidden');
        $(this).siblings('span').toggleClass('blur-code ');
    });

    $('body').on('change', '.change-module-status', function () {
        let moduleStatus;
        const module = $(this).data('module-name');
        const checkbox = $(this);

        if ($(this).is(':checked')) {
            moduleStatus = 'active';

            // Show confirmation for subdomain module activation
            if (module === 'Subdomain') {
                // Uncheck the checkbox initially
                checkbox.prop('checked', false);
                $(SUBDOMAIN_MODAL).removeClass('hidden');
                return;
            }
        } else {
            moduleStatus = 'inactive';
        }

        updateModuleStatus(module, moduleStatus);
    });

    $('body').on('click', '.cancel-subdomain-activation', function() {
        $(SUBDOMAIN_MODAL).addClass('hidden');
    });

    $('body').on('click', '.confirm-subdomain-activation', function() {
        const module = 'Subdomain';
        $('#module-' + module).prop('checked', true);
        $(SUBDOMAIN_MODAL).addClass('hidden');
        updateModuleStatus(module, 'active');
    });

    $('body').on('click', '.verify-module', function () {
        const module = $(this).data('module');
        let url = "{{ route('superadmin.custom-modules.show', ':module') }}";
        url = url.replace(':module', module);
        $.easyAjax({
            url: url,
            container: '#verifyModalBody',
            blockUI: true,
            success: function (response) {
                if (response.status === 'success') {
                    $('#verifyModalBody').html(response.html);
                }
            },
            error: function (response) {
                if (response.responseJSON) {
                    $('#custom-module-alert').html(response.responseJSON.message).removeClass('d-none');
                    $('#module-' + module).prop('checked', false);
                }
            }
        });
        $(VERIFY_MODAL).removeClass('hidden');
    });

    $('body').on('click', '.close-modal', function () {
        $(SUBDOMAIN_MODAL).addClass('hidden');
        $(VERIFY_MODAL).addClass('hidden');
    });

    $('body').on('click', '.copy-url', function() {
        const url = $(this).data('url');
        const button = $(this);
        const originalHTML = button.html();

        // Create temporary input element
        const tempInput = document.createElement('input');
        tempInput.value = url;
        document.body.appendChild(tempInput);

        // Select and copy
        tempInput.select();
        try {
            document.execCommand('copy');
            // Show success state
            button.html(`
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            `);

            // Revert back after 2 seconds
            setTimeout(() => {
                button.html(originalHTML);
            }, 2000);
        } catch (err) {
            console.error('Failed to copy URL:', err);
        }

        // Remove temporary input
        document.body.removeChild(tempInput);
    });

    function copyPurchaseCode(element) {
    const code = element.getAttribute('data-code');
    navigator.clipboard.writeText(code).then(() => {
        // Create tooltip for success message
        const tooltip = document.createElement('div');
        tooltip.className = 'absolute z-10 px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg';
        tooltip.textContent = 'Copied!';
        tooltip.style.top = '-25px';
        tooltip.style.left = '50%';
        tooltip.style.transform = 'translateX(-50%)';

        // Position relative for tooltip parent
        element.style.position = 'relative';

        // Add tooltip to element
        element.appendChild(tooltip);

        // Remove tooltip after delay
        setTimeout(() => {
            element.removeChild(tooltip);
        }, 1000);
    });
}
</script>

@includeIf('vendor.froiden-envato.update.update_module')

