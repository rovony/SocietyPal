<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\GlobalInvoice;
use App\Models\Package;
use App\Enums\PackageType;

class LatestBilling extends Component
{
    public $latestInvoices = [];

    public function mount()
    {
        $this->fetchLatestInvoices();
    }

    private function fetchLatestInvoices()
    {
        $packageIds = Package::where('package_type', '!=', PackageType::DEFAULT)
        ->where('package_type', '!=', PackageType::TRIAL)
        ->where('is_private', false)
        ->pluck('id');

        $this->latestInvoices = GlobalInvoice::with(['society', 'package'])->whereIn('package_id', $packageIds)->orderBy('created_at', 'desc')->limit(5)->get();
    }

    public function render()
    {
        return view('livewire.dashboard.latest-billing');
    }
}
