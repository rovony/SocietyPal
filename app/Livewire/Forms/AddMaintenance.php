<?php

namespace App\Livewire\Forms;

use App\Models\ApartmentManagement;
use App\Models\Maintenance;
use App\Models\MaintenanceManagement;
use App\Notifications\MaintenancePublishedNotification;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AddMaintenance extends Component
{
    use LivewireAlert;

    public $month;
    public $year;
    public $additionalCosts = [];
    public $payment_due_date;
    public $apartments;
    public $months = ['january', 'february', 'march', 'april', 'may', 'june','july', 'august', 'september', 'october', 'november', 'december' ];

    public function mount()
    {
        $currentDate = now();
        $previousDate = $currentDate->subMonthNoOverflow();
        $this->month = strtolower($previousDate->format('F'));
        $this->year = $previousDate->year;

        $this->apartments = ApartmentManagement::with('apartments')->get()->map(function ($apartment) {
            return [
                'id' => $apartment->id,
                'cost' => 0,
            ];
        })->toArray();
    }

    public function addAdditionalCost()
    {
        $this->additionalCosts[] = ['title' => '', 'cost' => ''];
    }

    public function removeAdditionalCost($index)
    {
        unset($this->additionalCosts[$index]);
        $this->additionalCosts = array_values($this->additionalCosts);
    }

    protected function validateForm()
    {
        $this->validate([
            'month' => 'required',
            'year' => 'required',
            'payment_due_date' => 'required|date',
            'additionalCosts.*.title' => 'required|string',
            'additionalCosts.*.cost' => 'required|numeric|min:0',
        ], [
            'additionalCosts.*.title.required' => 'The title for additional cost is required.',
            'additionalCosts.*.title.string' => 'The title must be a valid string.',
            'additionalCosts.*.cost.required' => 'The cost for additional cost is required.',
            'additionalCosts.*.cost.numeric' => 'The cost must be a valid number.',
            'additionalCosts.*.cost.min' => 'The cost must be at least 0.',
        ]);
    }

    protected function saveMaintenance($status)
    {
        $this->validateForm();

        $totalAdditionalCost = array_sum(array_column($this->additionalCosts, 'cost'));
        $this->payment_due_date = Carbon::parse($this->payment_due_date)->format('Y-m-d');

        $maintenance = MaintenanceManagement::create([
            'month' => $this->month,
            'year' => $this->year,
            'additional_details' => json_encode($this->additionalCosts),
            'total_additional_cost' => $totalAdditionalCost,
            'payment_due_date' => $this->payment_due_date,
            'status' => $status,
        ]);

        if ($status === 'published') {
            $this->notifyUsers($maintenance);
        }

        $this->resetForm();
        $this->dispatch('hideAddMaintenance');
        $this->alert('success', __('messages.maintenanceAdded'));
    }

    public function saveAsDraft()
    {
        $this->saveMaintenance('draft');
    }

    public function saveAsPublish()
    {
        $this->saveMaintenance('published');
    }

    protected function notifyUsers($maintenance)
    {
        $maintenance->load('maintenanceApartments.apartment.tenants.user');

        foreach ($maintenance->maintenanceApartments as $maintenanceApartment) {
            $apartment = $maintenanceApartment->apartment;

            if ($apartment->user) {
                $apartment->user->notify(new MaintenancePublishedNotification($maintenance, $maintenanceApartment->cost));
            }

            foreach ($apartment->tenants as $tenant) {
                if ($tenant->user) {
                    $tenant->user->notify(new MaintenancePublishedNotification($maintenance, $maintenanceApartment->cost));
                }
            }
        }
    }

    protected function resetForm()
    {
        $this->reset(['month', 'year', 'additionalCosts', 'payment_due_date']);
        $this->additionalCosts[] = ['title' => '', 'cost' => 0];
    }
    
    public function render()
    {
        return view('livewire.forms.add-maintenance');
    }
}
