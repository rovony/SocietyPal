<?php

namespace App\Livewire\Forms;

use App\Models\ApartmentManagement;
use App\Models\Maintenance;
use App\Models\MaintenanceManagement;
use App\Notifications\MaintenancePublishedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditMaintenance extends Component
{
    use LivewireAlert;

    public $maintenanceId;
    public $maintenance;
    public $month;
    public $year;
    public $additionalCosts = [];
    public $payment_due_date;
    public $apartments;
    public $months = ['january', 'february', 'march', 'april', 'may', 'june','july', 'august', 'september', 'october', 'november', 'december' ];

    public function mount()
    {
        $this->maintenanceId = $this->maintenance->id;
        $this->month = $this->maintenance->month;
        $this->year = $this->maintenance->year;
        $this->payment_due_date = $this->maintenance->payment_due_date;
        $this->additionalCosts = json_decode($this->maintenance->additional_details, true);

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

    public function saveAsDraft()
    {
        $this->saveMaintenance('draft');
    }

    public function saveAsPublish()
    {
        $this->saveMaintenance('published');
    }

    protected function saveMaintenance($status)
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

        $totalAdditionalCost = array_sum(array_column($this->additionalCosts, 'cost'));
        $this->payment_due_date = Carbon::parse($this->payment_due_date)->format('Y-m-d');
        $maintenance = MaintenanceManagement::findOrFail($this->maintenanceId);
        $maintenance->update([
            'month' => $this->month,
            'year' => $this->year,
            'payment_due_date' => $this->payment_due_date,
            'additional_details' => json_encode($this->additionalCosts),
            'total_additional_cost' => $totalAdditionalCost,
            'status' => $status,
        ]);
        try {

            if ($status === 'published') {
                $this->notifyUsers($maintenance);
            }
            $this->alert('success', __('messages.maintenanceUpdated'));
            $this->dispatch('hideEditMaintenance');

        } catch (\Exception $e) {
            
            $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end'
            ]);
        }
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


    public function render()
    {
        return view('livewire.forms.edit-maintenance');
    }
}
