<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\VisitorManagement;

class TotalVisitorCount extends Component
{


    public $showVisitor;
    public $showVisitorModal = false;
    public $visitor;

    public function showVisitorDashboard($id)
    {
        $this->showVisitor = VisitorManagement::findOrFail($id);
        $this->showVisitorModal = true;
    }

    #[On('hideShowVisitor')]
    public function hideShowVisitor()
    {
        $this->showVisitorModal = false;
        $this->dispatch('refreshVisitor');
    }

    public function render()
    {
        $userId = user()->id;
        if (user_can('Show Visitors')) {
            $this->visitor = VisitorManagement::whereDate('date_of_visit', now()->format('Y-m-d'))->get();
        } else {
            if (isRole() == 'Owner'){
                $this->visitor = VisitorManagement::whereDate('date_of_visit', now()->format('Y-m-d')) ->whereHas('apartment', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->get();
            }
            elseif (isRole() == 'Tenant')
            {   
                $this->visitor = VisitorManagement::whereDate('date_of_visit', now()->format('Y-m-d'))
                ->whereHas('apartment', function ($query) use ($userId) {
                    $query->whereHas('tenants', function ($tenantQuery) use ($userId) {
                        $tenantQuery->where('user_id', $userId);
                    });
                })->get();
            }
            elseif (isRole() == 'Guard'){
                $this->visitor = VisitorManagement::whereDate('date_of_visit', now()->format('Y-m-d'))->where('added_by', $userId)->get();
            }
        }
        return view('livewire.dashboard.total-visitor-count');
    }
}
