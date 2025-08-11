<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\OfflinePlanChange;

class PendingOfflineRequest extends Component
{
    public $pendingRequests = [];

    public function mount()
    {
        $this->fetchPendingRequests();
    }

    private function fetchPendingRequests()
    {
        $this->pendingRequests = OfflinePlanChange::where('status', 'pending')->limit(5)->get();
    }

    public function render()
    {
        $totalPending = OfflinePlanChange::where('status', 'pending')->count();

        return view('livewire.dashboard.pending-offline-request', [
            'totalPending' => $totalPending,
        ]);
    }
}
