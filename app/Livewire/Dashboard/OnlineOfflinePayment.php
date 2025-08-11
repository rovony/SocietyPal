<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\GlobalInvoice;
use App\Models\Package;
use App\Enums\PackageType;

class OnlineOfflinePayment extends Component
{
    public $onlineCount = 0;
    public $offlineCount = 0;

    public function mount()
    {
        $this->countGateways();
    }

    public function countGateways()
    {
        $packageIds = Package::where('package_type', '!=', PackageType::DEFAULT)
            ->where('package_type', '!=', PackageType::TRIAL)
            ->where('is_private', false)
            ->pluck('id')
            ->toArray();

        // Count invoices only for valid packages
        $this->onlineCount = GlobalInvoice::whereIn('package_id', $packageIds)
            ->where('gateway_name', 'online')
            ->count();
        
        $this->offlineCount = GlobalInvoice::whereIn('package_id', $packageIds)
            ->where('gateway_name', 'offline')
            ->count();
    }

    public function refreshChart()
    {
        $this->countGateways();
        $this->dispatch('update-chart', [
            'onlineCount' => $this->onlineCount,
            'offlineCount' => $this->offlineCount
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard.online-offline-payment');
    }
}
