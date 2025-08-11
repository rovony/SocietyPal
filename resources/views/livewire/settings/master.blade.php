<div>
    <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700">
        <ul class="flex flex-wrap -mb-px">
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=app' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'app'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'app')])>@lang('modules.settings.appSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=society' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'society'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'society')])>@lang('modules.settings.societySettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=theme' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'theme'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'theme')])>@lang('modules.settings.themeSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=currency' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'currency'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'currency')])>@lang('modules.settings.currencySettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=billType' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'billType'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'billType')])>@lang('modules.settings.billTypeSetting')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=maintenance' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'maintenance'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'maintenance')])>@lang('modules.settings.maintenanceSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=role' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'role'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'role')])>@lang('modules.settings.roleSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=module' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'module'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'module')])>@lang('modules.settings.moduleSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=payment' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'payment'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'payment')])>@lang('modules.settings.paymentgatewaySettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=email' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'email'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'email')])>@lang('modules.settings.emailSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=apartmentType' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'apartmentType'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'apartmentType')])>@lang('modules.settings.apartmentTypeSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=assetCategory' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'assetCategory'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'assetCategory')])>@lang('modules.settings.assetCategorySetting')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=ticket' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'ticket'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'ticket')])>@lang('modules.settings.ticketSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=visitorSetting' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'visitorSetting'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'visitorSetting')])>@lang('modules.settings.visitorSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=billing' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'billing'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'billing')])>@lang('modules.settings.billing')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=aboutus' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'aboutus'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'aboutus')])>@lang('modules.settings.aboutUsSettings')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=societyForumCategory' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'societyForumCategory'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'societyForumCategory')])>@lang('modules.settings.societyForumCategory')</a>
            </li>
            <li class="me-2">
                <a href="{{ route('settings.index').'?tab=securitySetting' }}" wire:navigate
                @class(["inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300", 'border-transparent' => ($activeSetting != 'securitySetting'), 'active border-skin-base dark:text-skin-base dark:border-skin-base text-skin-base' => ($activeSetting == 'securitySetting')])>@lang('modules.settings.securitySetting')</a>
            </li>


        </ul>
    </div>

    <div class="grid grid-cols-1 pt-6 dark:bg-gray-900">

        <div>
            @switch($activeSetting)
                @case('role')
                    @livewire('settings.roleSettings', ['settings' => $settings])
                    @break
                @case('society')
                    @livewire('settings.societySettings',['settings' => $settings])
                    @break
                @case('theme')
                    @livewire('settings.themeSettings', ['settings' => $settings])
                    @break
                @case('billType')
                    @livewire('settings.billTypeSettings')
                    @break
                @case('currency')
                    @livewire('settings.currencySettings')
                    @break
                @case('app')
                    @livewire('settings.timezoneSettings', ['settings' => $settings])
                    @break
                @case('maintenance')
                    @livewire('settings.maintenanceSettings', ['maintenanceSetting' => $maintenanceSetting])
                    @break
                @case('email')
                    @livewire('settings.notificationSettings', ['settings' => $settings])
                    @break
                @case('apartmentType')
                    @livewire('settings.apartmentType')
                    @break
                @case('assetCategory')
                    @livewire('settings.assetCategory')
                    @break
                @case('ticket')
                    @livewire('settings.ticketSettings')
                    @break
                @case('visitorSetting')
                    @livewire('settings.visitorTypeSettings')
                    @break
                @case('billing')
                    @livewire('settings.billingSettings')
                    @break
                @case('module')
                    @livewire('settings.moduleSettings')
                    @break
                @case('payment')
                    @livewire('settings.PaymentSettings')
                    @break
                @case('aboutus')
                    @livewire('settings.aboutUsSettings', ['settings' => $settings])
                    @break
                @case('societyForumCategory')
                    @livewire('settings.societyForumSetting')
                    @break
                @case('securitySetting')
                    @livewire('settings.securitySetting')
                    @break
                @default
            @endswitch
        </div>


    </div>

</div>
