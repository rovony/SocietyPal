<?php

use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\StorageSetting;
use App\Helper\Files;
use App\Models\Currency;
use App\Models\GlobalSetting;
use App\Models\LanguageSetting;
use App\Models\ModuleSetting;
use App\Models\Package;
use App\Models\Society;
use App\Models\EmailSetting;
use Carbon\Carbon;
use Nwidart\Modules\Facades\Module;
use App\Models\SuperadminPaymentGateway;
use App\Models\GlobalCurrency;


function timezone()
{
    if (session()->has('timezone')) {
        return session('timezone');
    }

    session(['timezone' => 'Asia/Kolkata']); // Should come from setting
    return session('timezone');
}

function currency()
{
    if (session()->has('currency')) {
        return session('currency');
    }

    if (society()) {
        session(['currency' => society()->currency->currency_symbol]);

        return session('currency');
    }

    return false;
}



if (!function_exists('user')) {

    /**
     * Return current logged-in user
     */
    function user()
    {
        if (session()->has('user')) {
            return session('user');
        }

        $user = auth()->user();

        if ($user) {
            session(['user' => $user]);

            return session('user');
        }

        return null;
    }
}

function society()
{
    if (session()->has('society')) {
        return session('society');
    }

    if (user()) {
        if (user()->society_id) {
            session(['society' => Society::find(user()->society_id)]);
            return session('society');
        }
    }

    // session(['society' => Society::first()]); // Used in Non-saas

    // return session('society');  // Used in Non-saas
    return false;  // Used in Saas

}

if (!function_exists('isRole')) {

    /**
     * Return current logged-in user's Role Name
     */
    function isRole()
    {
        if (session()->has('isRole')) {
            return session('isRole');
        }

        $roleName = user()->role->display_name;

        if ($roleName) {
            session(['isRole' => $roleName]);

            return session('isRole');
        }

        return null;
    }
}

// @codingStandardsIgnoreLine
function check_migrate_status()
{

    if (!session()->has('check_migrate_status')) {

        $status = Artisan::call('migrate:check');

        if ($status && !request()->ajax()) {
            Artisan::call('migrate', ['--force' => true, '--schema-path' => 'do not run schema path']); // Migrate database
            Artisan::call('optimize:clear');
        }

        session(['check_migrate_status' => 'Good']);
    }

    return session('check_migrate_status');
}

if (!function_exists('role_permissions')) {

    function role_permissions()
    {
        if (session()->has('role_permissions')) {
            return session('role_permissions');
        }

        $roleID = user()->role->id;
        $permissions = Role::where('id', $roleID)->first()->permissions->pluck('name')->toArray();

        session(['role_permissions' => $permissions]);

        return $permissions ?: [];
    }
}

if (!function_exists('user_can')) {

    function user_can($permission)
    {
        if (is_null(role_permissions())) {
            $rolePermissions = [];
        } else {
            $rolePermissions = role_permissions();
        }

        return in_array($permission, $rolePermissions);
    }
}


if (!function_exists('currency_format_setting')) {

    // @codingStandardsIgnoreLine
    function currency_format_setting($currencyId = null)
    {
        $cacheKey = 'currency_format_setting' . $currencyId;

        if (!session()->has($cacheKey)) {
            if (is_null($currencyId)) {
                if (!society()) {
                    return null;
                }
                $setting = society()->load('currency')->currency;
            } else {
                $setting = Currency::find($currencyId);
                if (!$setting) {
                    return null;
                }
            }

            session([$cacheKey => $setting]);
        }

        return session($cacheKey);
    }
}

if (!function_exists('currency_format')) {

    // @codingStandardsIgnoreLine
    function currency_format($amount, $currencyId = null, $showSymbol = true)
    {
        $formats = currency_format_setting($currencyId);

        if (!$formats) {
            return $amount;
        }

        $currency_symbol = '';
        if ($showSymbol) {
            $settings = $formats->society ?? Society::find($formats->society_id);
            $currency_symbol = $currencyId === null ? $settings->currency->currency_symbol : $formats->currency_symbol;
        }

        $no_of_decimal = $formats->no_of_decimal ?? 0;
        $thousand_separator = $formats->thousand_separator ?? '';
        $decimal_separator = $formats->decimal_separator ?? '.';

        $formatted_amount = number_format(
            floatval($amount),
            $no_of_decimal,
            $decimal_separator,
            $thousand_separator
        );

        return match ($formats->currency_position) {
            'right' => $formatted_amount . $currency_symbol,
            'left_with_space' => $currency_symbol . ' ' . $formatted_amount,
            'right_with_space' => $formatted_amount . ' ' . $currency_symbol,
            default => $currency_symbol . $formatted_amount,
        };
    }
}

if (!function_exists('global_setting')) {

    // @codingStandardsIgnoreLine
    function global_setting()
    {

        if (cache()->has('global_setting')) {
            return cache('global_setting');
        }

        cache(['global_setting' => GlobalSetting::first()]);

        return cache('global_setting');
    }
}

if (!function_exists('societyOrGlobalSetting')) {

    function societyOrGlobalSetting()
    {
        if (user()) {

            if (user()->society_id) {
                return society();
            }
        }

        return global_setting();
    }
}

if (!function_exists('isRtl')) {

    function isRtl()
    {

        if (session()->has('isRtl')) {
            return session('isRtl');
        }

        if (user()) {
            $language = LanguageSetting::where('language_code', auth()->user()->locale)->first();
            $isRtl = ($language->is_rtl == 1);
            session(['isRtl' => $isRtl]);
        }

        return false;
    }
}

if (!function_exists('languages')) {

    function languages()
    {

        if (cache()->has('languages')) {
            return cache('languages');
        }

        $languages = LanguageSetting::where('active', 1)->get();
        cache(['languages' => $languages]);

        return cache('languages');
    }
}

if (!function_exists('asset_url_local_s3')) {

    // @codingStandardsIgnoreLine
    function asset_url_local_s3($path)
    {
        if (in_array(config('filesystems.default'), StorageSetting::S3_COMPATIBLE_STORAGE)) {
            // Check if the URL is already cached
            if (\Illuminate\Support\Facades\Cache::has(config('filesystems.default') . '-' . $path)) {
                $temporaryUrl = \Illuminate\Support\Facades\Cache::get(config('filesystems.default') . '-' . $path);
            } else {
                // Generate a new temporary URL and cache it
                $temporaryUrl = Storage::disk(config('filesystems.default'))->temporaryUrl($path, now()->addMinutes(StorageSetting::HASH_TEMP_FILE_TIME));
                \Illuminate\Support\Facades\Cache::put(config('filesystems.default') . '-' . $path, $temporaryUrl, StorageSetting::HASH_TEMP_FILE_TIME * 60);
            }

            return $temporaryUrl;
        }

        $path = Files::UPLOAD_FOLDER . '/' . $path;
        $storageUrl = $path;

        if (!Str::startsWith($storageUrl, 'http')) {
            return url($storageUrl);
        }

        return $storageUrl;
    }
}

if (!function_exists('download_local_s3')) {

    // @codingStandardsIgnoreLine
    function download_local_s3($file, $path)
    {

        if (in_array(config('filesystems.default'), StorageSetting::S3_COMPATIBLE_STORAGE)) {
            return Storage::disk(config('filesystems.default'))->download($path, basename($file->filename));
        }

        $path = Files::UPLOAD_FOLDER . '/' . $path;
        $ext = pathinfo($file->filename, PATHINFO_EXTENSION);

        $filename = $file->name ? $file->name . '.' . $ext : $file->filename;
        try {
            return response()->download($path, $filename);
        } catch (\Exception $e) {
            return response()->view('errors.file_not_found', ['message' => $e->getMessage()], 404);
        }
    }
}


if (!function_exists('asset_url')) {

    // @codingStandardsIgnoreLine
    function asset_url($path)
    {
        $path = \App\Helper\Files::UPLOAD_FOLDER . '/' . $path;
        $storageUrl = $path;

        if (!Str::startsWith($storageUrl, 'http')) {
            return url($storageUrl);
        }

        return $storageUrl;
    }
}

if (!function_exists('getDomain')) {

    function getDomain($host = false)
    {
        if (!$host) {
            $host = $_SERVER['SERVER_NAME'] ?? 'societypro.test';
        }

        $shortDomain = config('app.short_domain_name');
        $dotCount = ($shortDomain === true) ? 2 : 1;

        $myHost = strtolower(trim($host));
        $count = substr_count($myHost, '.');

        if ($count === $dotCount || $count === 1) {
            return $myHost;
        }

        $myHost = explode('.', $myHost, 2);

        return end($myHost);
    }
}

if (!function_exists('package')) {

    function package()
    {

        if (cache()->has('package')) {
            return cache('package');
        }

        $package = Package::first();

        cache(['package' => $package]);

        return cache('package');
    }
}

function superadminPaymentGateway()
{
    if (cache()->has('superadminPaymentGateway')) {
        return cache('superadminPaymentGateway');
    }

    $payment = SuperadminPaymentGateway::first();

    cache(['superadminPaymentGateway' => $payment]);

    return cache('superadminPaymentGateway');
}

if (!function_exists('societyToYmd')) {

    function societyToYmd($date, $timeFormat = false, $onlyTime = false)
    {
        if (is_null($date)) {
            return null;
        }

        if ($onlyTime && $timeFormat) {
            return Carbon::createFromFormat($timeFormat, $date, 'UTC')->format('H:i:s');
        }

        if ($timeFormat) {
            return Carbon::createFromFormat('d F Y ' . $timeFormat, $date, 'UTC')->format('Y-m-d H:i:s');
        }

        return Carbon::createFromFormat('d F Y', $date, 'UTC')->format('Y-m-d');
    }
}

if (!function_exists('ymdToDateString')) {

    function ymdToDateString($date, $timeFormat = false, $object = false)
    {
        if (is_null($date)) {
            return null;
        }

        if ($object) {
            if ($timeFormat) {
                return Carbon::createFromFormat('Y-m-d H:i:s', $date, society()->timezone);
            }

            return Carbon::createFromFormat('Y-m-d', $date, society()->timezone);
        }

        if ($timeFormat) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $date, society()->timezone)->format('d F Y H:i a');
        }

        return Carbon::createFromFormat('Y-m-d', $date, society()->timezone)->format('d F Y');
    }
}

if (!function_exists('societyToYmd')) {

    function societyToYmd($date, $timeFormat = false, $onlyTime = false)
    {
        if (is_null($date)) {
            return null;
        }

        if ($onlyTime && $timeFormat) {
            return Carbon::createFromFormat($timeFormat, $date, 'UTC')->format('H:i:s');
        }

        if ($timeFormat) {
            return Carbon::createFromFormat('d F Y ' . $timeFormat, $date, 'UTC')->format('Y-m-d H:i:s');
        }

        return Carbon::createFromFormat('d F Y', $date, 'UTC')->format('Y-m-d');
    }
}

if (!function_exists('ymdToDateString')) {

    function ymdToDateString($date, $timeFormat = false, $object = false)
    {
        if (is_null($date)) {
            return null;
        }

        if ($object) {
            if ($timeFormat) {
                return Carbon::createFromFormat('Y-m-d H:i:s', $date, society()->timezone);
            }

            return Carbon::createFromFormat('Y-m-d', $date, society()->timezone);
        }

        if ($timeFormat) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $date, society()->timezone)->format('d F Y H:i a');
        }

        return Carbon::createFromFormat('Y-m-d', $date, society()->timezone)->format('d F Y');
    }
}

if (!function_exists('getDomainSpecificUrl')) {

    function getDomainSpecificUrl($url, $society = null)
    {
        // Check if Subdomain module exist
        if (!module_enabled('Subdomain')) {
            return $url;
        }

        config(['app.url' => config('app.main_app_url')]);

        // If society specific
        if ($society) {
            $societyUrl = (config('app.redirect_https') ? 'https' : 'http') . '://' . $society->sub_domain;

            config(['app.url' => $societyUrl]);
            // Removed Illuminate\Support\Facades\URL::forceRootUrl($companyUrl);

            if (Str::contains($url, $society->sub_domain)) {
                return $url;
            }

            $url = str_replace(request()->getHost(), $society->sub_domain, $url);
            $url = str_replace('www.', '', $url);

            // Replace https to http for sub-domain to
            if (!config('app.redirect_https')) {
                return str_replace('https', 'http', $url);
            }

            return $url;
        }

        // Removed config(['app.url' => $url]);
        // Comment      \Illuminate\Support\Facades\URL::forceRootUrl($url);
        // If there is no society and url has login means
        // New superadmin is created
        return str_replace('login', 'super-admin-login', $url);
    }
}

function module_enabled($moduleName)
{
    return Module::has($moduleName) && Module::isEnabled($moduleName);
}


if (!function_exists('society_modules')) {
    function society_modules(): array
    {
        $society = society();

        if (!$society) {
            return [];
        }

        $cacheKey = 'society_modules_' . $society->id;
        if (cache()->has($cacheKey)) {
            return cache($cacheKey);
        }

        $user = user();
        if (is_null($user->society_id)) {
            return [];
        }

        $society = Society::with('package.modules')->find($society->id);
        session(['society' => $society]);

        $package = $society->package;

        $packageModules = $package->modules->pluck('name')->toArray();
        $additionalFeatures = json_decode($package->additional_features ?? '[]', true);

        $allModules = array_unique(array_merge($packageModules, $additionalFeatures));

        cache([$cacheKey => $allModules]);

        return cache($cacheKey);
    }
}

if (!function_exists('society_role_modules')) {
    function society_role_modules(): array
    {
        $role = isRole();
        $society = society();

        if (!$society) {
            return [];
        }
        $cacheKey = 'society_role_modules_' . $society->id . ($role ? "_$role" : '_all');

        if (cache()->has($cacheKey)) {
            return cache($cacheKey);
        }

        $query = ModuleSetting::where('society_id', $society->id)->where('status', 'active')->where('type', $role);

        $modules = $query->pluck('module_name')->toArray();

        cache([$cacheKey => $modules]);

        return $modules;
    }
}

if (!function_exists('clearSocietyModulesCache')) {

    function clearSocietyModulesCache($societyId)
    {
        if (is_null($societyId)) {
            return true;
        }

        cache()->forget('society_modules_' . $societyId);
    }
}

if (!function_exists('currency_format_setting')) {

    // @codingStandardsIgnoreLine
    function currency_format_setting($currencyId = null)
    {
        if (!session()->has('currency_format_setting' . $currencyId) || (is_null($currencyId) && society())) {
            $setting = $currencyId == null ? society()->load('currency')->currency : Currency::where('id', $currencyId)->first();
            session(['currency_format_setting' . $currencyId => $setting]);
        }

        return session('currency_format_setting' . $currencyId);
    }
}


if (!function_exists('currency_format')) {

    // @codingStandardsIgnoreLine
    function currency_format($amount, $currencyId = null, $showSymbol = true)
    {
        $formats = currency_format_setting($currencyId);

        if (!$showSymbol) {
            $currency_symbol = '';
        } else {
            $settings = $formats->society ?? Society::find($formats->society_id);
            $currency_symbol = $currencyId == null ? $settings->currency->currency_symbol : $formats->currency_symbol;
        }

        $currency_position = $formats->currency_position;
        $no_of_decimal = !is_null($formats->no_of_decimal) ? $formats->no_of_decimal : '0';
        $thousand_separator = !is_null($formats->thousand_separator) ? $formats->thousand_separator : '';
        $decimal_separator = !is_null($formats->decimal_separator) ? $formats->decimal_separator : '0';

        $amount = number_format(floatval($amount), $no_of_decimal, $decimal_separator, $thousand_separator);

        $amount = match ($currency_position) {
            'right' => $amount . $currency_symbol,
            'left_with_space' => $currency_symbol . ' ' . $amount,
            'right_with_space' => $amount . ' ' . $currency_symbol,
            default => $currency_symbol . $amount,
        };

        return $amount;
    }
}


if (!function_exists('global_currency_format_setting')) {

    // @codingStandardsIgnoreLine
    function global_currency_format_setting($currencyId = null)
    {
        if (!session()->has('global_currency_format_setting' . $currencyId)) {
            $setting = $currencyId == null ? GlobalCurrency::first() : GlobalCurrency::where('id', $currencyId)->first();
            session(['global_currency_format_setting' . $currencyId => $setting]);
        }

        return session('global_currency_format_setting' . $currencyId);
    }
}

if (!function_exists('global_currency_format')) {

    // @codingStandardsIgnoreLine
    function global_currency_format($amount, $currencyId = null, $showSymbol = true)
    {
        $formats = global_currency_format_setting($currencyId);


        if (!$showSymbol) {
            $currency_symbol = '';
        } else {
            $currency_symbol = $formats->currency_symbol;
        }

        $currency_position = $formats->currency_position;
        $no_of_decimal = !is_null($formats->no_of_decimal) ? $formats->no_of_decimal : '0';
        $thousand_separator = !is_null($formats->thousand_separator) ? $formats->thousand_separator : '';
        $decimal_separator = !is_null($formats->decimal_separator) ? $formats->decimal_separator : '0';

        $amount = number_format($amount, $no_of_decimal, $decimal_separator, $thousand_separator);

        $amount = match ($currency_position) {
            'right' => $amount . $currency_symbol,
            'left_with_space' => $currency_symbol . ' ' . $amount,
            'right_with_space' => $amount . ' ' . $currency_symbol,
            default => $currency_symbol . $amount,
        };

        return $amount;
    }
}

if (!function_exists('smtp_setting')) {

    // @codingStandardsIgnoreLine
    function smtp_setting()
    {
        if (!session()->has('smtp_setting')) {
            session(['smtp_setting' => EmailSetting::first()]);
        }

        return session('smtp_setting');
    }
}

if (!function_exists('custom_module_plugins')) {

    // @codingStandardsIgnoreLine
    function custom_module_plugins()
    {

        if (!cache()->has('custom_module_plugins')) {
            $plugins = \Nwidart\Modules\Facades\Module::allEnabled();
            cache(['custom_module_plugins' => array_keys($plugins)]);
        }

        return cache('custom_module_plugins');
    }
}
