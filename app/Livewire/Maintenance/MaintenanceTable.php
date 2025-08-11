<?php

namespace App\Livewire\Maintenance;

use App\Exports\MaintenanceExport;
use App\Models\MaintenanceManagement;
use App\Notifications\MaintenancePublishedNotification;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class MaintenanceTable extends Component
{
    use LivewireAlert;

    public $search;
    public $maintenance;
    public $showEditMaintenanceModal = false;
    public $showMaintenanceDetailModal = false;
    public $confirmDeleteMaintenanceModal = false;
    public $clearFilterButton = false;
    public $showFilters = false;
    public $filterYears = [];
    public $filterMonths = [];
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $maintenanceData;
    public $confirmSelectedDeleteMaintenanceModal = false;

    public function mount()
    {
        $this->maintenanceData = MaintenanceManagement::get();
    }

    public function showEditMaintenance($id)
    {
        $this->maintenance = MaintenanceManagement::findOrFail($id);
        $this->showEditMaintenanceModal = true;
    }

    #[On('hideEditMaintenance')]
    public function hideEditMaintenance()
    {
        $this->showEditMaintenanceModal = false;
    }

    public function showPublished($id)
    {
        $this->maintenance = MaintenanceManagement::with('maintenanceApartments.apartment.tenants.user')->findOrFail($id);

        $this->maintenance->status = 'Published';
        $this->maintenance->save();

        try {
            foreach ($this->maintenance->maintenanceApartments as $maintenanceApartment) {
                $apartment = $maintenanceApartment->apartment;

                if ($apartment->user) {
                    $apartment->user->notify(new MaintenancePublishedNotification($this->maintenance, $maintenanceApartment->cost));
                }

                foreach ($apartment->tenants as $tenant) {
                    if ($tenant->user) {
                        $tenant->user->notify(new MaintenancePublishedNotification($this->maintenance, $maintenanceApartment->cost));
                    }
                }
            }

            $this->alert('success', __('messages.maintenancePublished'));
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage(), [
                'toast' => true,
                'position' => 'top-end'
            ]);
        }
    }

    public function showDeleteMaintenance($id)
    {
        $this->maintenance = MaintenanceManagement::findOrFail($id);
        $this->confirmDeleteMaintenanceModal = true;
    }

    public function deleteMaintenance($id)
    {
        MaintenanceManagement::destroy($id);
        $this->confirmDeleteMaintenanceModal = false;
        $this->maintenance = '';

        $this->alert('success', __('messages.maintenanceDeleted'));

    }

    public function showSelectedDeleteMaintenance()
    {
        $this->confirmSelectedDeleteMaintenanceModal = true;
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->maintenanceData->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function deleteSelected()
    {
        MaintenanceManagement::whereIn('id', $this->selected)->where('status' , 'draft')->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->confirmSelectedDeleteMaintenanceModal=false;
        $this->alert('success', __('messages.maintenanceDeleted'));
    }

    public function publishedAll()
    {
        MaintenanceManagement::whereIn('id', $this->selected)->where('status' , 'draft')->update(['status' => 'published']);
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->alert('success', __('messages.maintenancePublished'));
    }

    #[On('showMaintenanceFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->filterYears = [];
        $this->filterMonths = [];
        $this->search = '';
        $this->clearFilterButton = false;
    }

    #[On('exportMaintenance')]
    public function exportMaintenance()
    {
        return (new MaintenanceExport($this->search, $this->filterYears, $this->filterMonths))->download('maintenance-'.now()->toDateTimeString().'.xlsx');
    }

    public function getMonthsProperty()
    {
        return [
            'january' => __('modules.rent.january'),
            'february' => __('modules.rent.february'),
            'march' => __('modules.rent.march'),
            'april' => __('modules.rent.april'),
            'may' => __('modules.rent.may'),
            'june' => __('modules.rent.june'),
            'july' => __('modules.rent.july'),
            'august' => __('modules.rent.august'),
            'september' => __('modules.rent.september'),
            'october' => __('modules.rent.october'),
            'november' => __('modules.rent.november'),
            'december' => __('modules.rent.december'),
        ];
    }

    public function render()
    {
        $this->clearFilterButton = false;

        $query = MaintenanceManagement::query();

        if ($this->search != '') {
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterYears)) {
            $query = $query->whereIn('year', $this->filterYears);
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterMonths)) {
            $query = $query->whereIn('month', $this->filterMonths);
            $this->clearFilterButton = true;
        }

        if ($this->search) {
            $query = $query->where('year', 'like', '%' . $this->search . '%')
                            ->orWhere('month', 'like', '%' . $this->search . '%')
                            ->orWhere('status', 'like', '%' . $this->search . '%');
        }

        $maintenances = $query->with('maintenanceApartments')->paginate(10);
        
        return view('livewire.maintenance.maintenance-table', [
            'maintenances' => $maintenances
        ]);
    }
}
