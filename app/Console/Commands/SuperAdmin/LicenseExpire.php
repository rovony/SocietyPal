<?php

namespace App\Console\Commands\SuperAdmin;

use App\Models\Package;
use App\Enums\PackageType;
use App\Models\Society;
use App\Models\GlobalInvoice;
use Illuminate\Console\Command;
use App\Models\GlobalSubscription;
use App\Notifications\SubscriptionExpire;

class LicenseExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:license-expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set license expire status of societies in societies table.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $societies = Society::with('package')
            ->where('status', 'active')
            ->whereNotNull('license_expire_on')
            ->where('license_expire_on', '<', now())
            ->whereHas('package', function ($query) {
                $query->where('package_type', '!=', PackageType::DEFAULT)->where('is_free', 0);
            })->get();

        $packages = Package::all();

        // info('societies' . $societies->count());

        $defaultPackage = $packages->firstWhere('package_type', PackageType::DEFAULT);

        $otherPackages = $packages->whereNotIn('package_type', [PackageType::DEFAULT, PackageType::LIFETIME]);

        // Set default package for license expired societies.
        foreach ($societies as $society) {
            $latestInvoice = GlobalInvoice::where('society_id', $society->id)
                ->whereNotNull('pay_date')
                ->latest()
                ->first();

            if (!($latestInvoice && $latestInvoice->next_pay_date > now())) {
                $society->package_id = $defaultPackage->id;
                $society->package_type = 'monthly';
                $society->license_expire_on = now()->addMonth();
                $society->status = 'license_expired';
                $society->save();

                $this->updateSubscription($society, $defaultPackage);

                $societyUser = Society::societyAdmin($society);
                // info('expire for: ' . $society->name);
                $societyUser->notify(new SubscriptionExpire($society));
            }
        }

        // Sent notification to societies before license expire.
        foreach ($otherPackages as $package) {
            if (!is_null($package->trial_notification_before_days)) {
                $societiesNotify = Society::with('package')
                    ->where('status', 'active')
                    ->whereNotNull('license_expire_on')
                    ->whereDate('license_expire_on', now()->addDays($package->trial_notification_before_days))
                    ->whereHas('package', function ($query) use ($package) {
                        $query->where('package_type', '!=', PackageType::DEFAULT)->where('is_free', 0)->where('id', $package->id);
                    })->get();

                foreach ($societiesNotify as $rst) {
                    $societyUser = Society::societyAdmin($rst);
                }
            }
        }
    }

    public function updateSubscription(Society $society, Package $package)
    {
        $currencyId = $package->currency_id ?: global_setting()->currency_id;

        GlobalSubscription::where('society_id', $society->id)
            ->where('subscription_status', 'active')
            ->update(['subscription_status' => 'inactive']);

        $subscription = new GlobalSubscription();
        $subscription->society_id = $society->id;
        $subscription->package_id = $society->package_id;
        $subscription->currency_id = $currencyId;
        $subscription->package_type = $society->package_type;
        $subscription->quantity = 1;
        $subscription->gateway_name = 'offline';
        $subscription->subscription_status = 'active';
        $subscription->subscribed_on_date = now();
        $subscription->ends_at = $society->license_expire_on;
        $subscription->transaction_id = str(str()->random(15))->upper();
        $subscription->save();

        $offlineInvoice = new GlobalInvoice();
        $offlineInvoice->global_subscription_id = $subscription->id;
        $offlineInvoice->society_id = $subscription->society_id;
        $offlineInvoice->currency_id = $subscription->currency_id;
        $offlineInvoice->package_id = $subscription->package_id;
        $offlineInvoice->package_type = $subscription->package_type;
        $offlineInvoice->total = 0.00;
        $offlineInvoice->pay_date = now();
        $offlineInvoice->next_pay_date = $subscription->ends_at;
        $offlineInvoice->gateway_name = 'offline';
        $offlineInvoice->transaction_id = $subscription->transaction_id;
        $offlineInvoice->save();
    }
}
