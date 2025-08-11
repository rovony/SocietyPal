<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\GlobalSubscription;
use Carbon\Carbon;
use App\Models\Package;
use App\Enums\PackageType;

class SubscriptionChart extends Component
{
    public $dates = [];
    public $subscriptions = [];

    public function mount()
    {
        $this->fetchSubscriptionData();
    }

    private function fetchSubscriptionData()
    {
        $packageIds = Package::where('package_type', '!=', PackageType::DEFAULT)
        ->where('package_type', '!=', PackageType::TRIAL)
        ->where('is_private', false)
        ->pluck('id')
        ->toArray();

         $subscriptionData = GlobalSubscription::selectRaw('DATE(subscribed_on_date) as date, COUNT(*) as total')
            ->whereIn('package_id', $packageIds) // Filter by valid package IDs
            ->where('subscribed_on_date', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->total];
            })->toArray();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $this->dates[] = $date;
            $this->subscriptions[] = $subscriptionData[$date] ?? 0;
        }
    }

    public function render()
    {
        return view('livewire.dashboard.subscription-chart');
    }
}
