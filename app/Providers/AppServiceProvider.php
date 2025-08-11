<?php

namespace App\Providers;

use App\Models\Rent;
use App\Models\User;
use App\Models\Event;
use App\Models\Floor;
use App\Models\Tower;
use App\Models\Notice;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\Society;
use App\Models\BillType;
use App\Models\Currency;
use App\Models\Amenities;
use App\Models\Apartment;
use App\Models\AssetIssue;
use App\Models\BookAmenity;
use App\Models\FileStorage;
use App\Models\Maintenance;
use App\Models\ServiceType;
use App\Models\ModuleSetting;
use App\Models\AssetsCategory;
use App\Models\AssetManagement;
use App\Models\CommonAreaBills;
use App\Models\LanguageSetting;
use App\Observers\RentObserver;
use App\Observers\UserObserver;
use App\Models\PushNotification;
use App\Observers\EventObserver;
use App\Observers\FloorObserver;
use App\Observers\TowerObserver;
use App\Models\ServiceClockInOut;
use App\Models\ServiceManagement;
use App\Models\TicketTypeSetting;
use App\Models\VisitorManagement;
use App\Observers\NoticeObserver;
use App\Observers\TenantObserver;
use App\Observers\TicketObserver;
use App\Models\TicketAgentSetting;
use App\Observers\SocietyObserver;
use App\Models\ApartmentManagement;
use App\Models\AssetMaintenance;
use App\Observers\BillTypeObserver;
use App\Observers\CurrencyObserver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Observers\AmenitiesObserver;
use App\Observers\ApartmentObserver;
use Illuminate\Support\Facades\Gate;
use App\Models\MaintenanceManagement;
use App\Models\UtilityBillManagement;
use App\Observers\AssetIssueObserver;
use App\Observers\TicketTypeObserver;
use App\Observers\BookAmenityObserver;
use App\Observers\FileStorageObserver;
use App\Observers\MaintenanceObserver;
use App\Observers\ServiceTypeObserver;
use App\Observers\TicketAgentObserver;
use App\Observers\VisitorTypeObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\ParkingManagementSetting;
use App\Models\VisitorTypeSettingsModel;
use App\Observers\AssetCategoryObserver;
use App\Observers\ModuleSettingObserver;
use App\Observers\CommonBillAreaObserver;
use Illuminate\Database\Eloquent\Builder;
use App\Observers\AssetManagementObserver;
use App\Observers\LanguageSettingObserver;
use App\Observers\PushNotificationObserver;
use App\Observers\ParkingManagementObserver;
use App\Observers\ServiceClockInOutObserver;
use App\Observers\ServiceManagementObserver;
use App\Observers\VisitorsManagementObserver;
use Spatie\Translatable\Facades\Translatable;
use App\Observers\ApartmentManagementObserver;
use App\Observers\MaintenanceManagementObserver;
use App\Observers\UtilityBillManagementObserver;
use App\Models\SocietyForumCategory;
use App\Observers\SocietyForumCategoryObserver;
use App\Models\Forum;
use App\Observers\AssetMaintenanceObserver;
use App\Observers\ForumObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (config('app.redirect_https')) {
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.redirect_https')) {
            URL::forceScheme('https');
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);

        // Observer register
        Society::observe(SocietyObserver::class);
        User::observe(UserObserver::class);
        BillType::observe(BillTypeObserver::class);
        Currency::observe(CurrencyObserver::class);
        $this->app->register(FileStorageCustomConfigProvider::class);
        $this->app->register(CustomConfigProvider::class);
        Tower::observe(TowerObserver::class);
        Maintenance::observe(MaintenanceObserver::class);
        Floor::observe(FloorObserver::class);
        ApartmentManagement::observe(ApartmentManagementObserver::class);
        AssetsCategory::observe(AssetCategoryObserver::class);
        Apartment::observe(ApartmentObserver::class);
        ParkingManagementSetting::observe(ParkingManagementObserver::class);
        AssetManagement::observe(AssetManagementObserver::class);
        Amenities::observe(AmenitiesObserver::class);
        VisitorManagement::observe(VisitorsManagementObserver::class);
        TicketTypeSetting::observe(TicketTypeObserver::class);
        TicketAgentSetting::observe(TicketAgentObserver::class);
        UtilityBillManagement::observe(UtilityBillManagementObserver::class);
        Notice::observe(NoticeObserver::class);
        CommonAreaBills::observe(CommonBillAreaObserver::class);
        MaintenanceManagement::observe(MaintenanceManagementObserver::class);
        ServiceType::observe(ServiceTypeObserver::class);
        ServiceManagement::observe(ServiceManagementObserver::class);
        VisitorTypeSettingsModel::observe(VisitorTypeObserver::class);
        Tenant::observe(TenantObserver::class);
        Rent::observe(RentObserver::class);
        Ticket::observe(TicketObserver::class);
        FileStorage::observe(FileStorageObserver::class);
        ModuleSetting::observe(ModuleSettingObserver::class);
        BookAmenity::observe(BookAmenityObserver::class);
        ServiceClockInOut::observe(ServiceClockInOutObserver::class);
        AssetIssue::observe(AssetIssueObserver::class);
        PushNotification::observe(PushNotificationObserver::class);
        Event::observe(EventObserver::class);
        LanguageSetting::observe(LanguageSettingObserver::class);
        SocietyForumCategory::observe(SocietyForumCategoryObserver::class);
        Forum::observe(ForumObserver::class);
        AssetMaintenance::observe(AssetMaintenanceObserver::class);

        // Implicitly grant "Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Admin_' . $user->society_id) ? true : null;
        });

        // Search macro for searching in tables.
        Builder::macro('search', function ($field, $string) {
            return $string ? $this->where($field, 'like', '%' . $string . '%') : $this;
        });

        // Fallback to English if the locale is not found
        try {
            Translatable::fallback(global_setting()->locale, 'en');
        } catch (\Exception $e) {
            Log::error('Error in Translatable fallback: ' . $e->getMessage());
        }

    }
}
