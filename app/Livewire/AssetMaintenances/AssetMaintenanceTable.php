<?php

namespace App\Livewire\AssetMaintenances;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\AssetManagement;
use App\Models\AssetMaintenance;
use App\Exports\AssetMaintenanceExport;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AssetMaintenanceTable extends Component
{
    use LivewireAlert;
    public $assetId;
    public $selected = [];
    public $showActions = false;
    public $selectAll = false;
    public $showMaintenanceDetailsModal = false;
    public $selectedMaintenance;
    public $showDeleteMaintenanceModal = false;
    public $showAddMaintenance = false;
    public $showMaintenancesModal = false;
    public $MaintenanceId;
    public $showFilterButton = true;
    public $search;
    public $clearFilterButton = false;
    public $filterCategories = [];
    public $categories;
    public $showFilters = false;
    public $assetsData;
    public $assetData;
    public $filterStatus = [];
    public $confirmDeleteAssetModal = false;
    public $assetMaintenanceId;

    public function mount()
    {
      //
    }
    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->maintenances->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function showEditMaintenance($id)
    {
        $this->selectedMaintenance = $id;
        $this->showMaintenanceDetailsModal = true;
    }

    #[On('hideEditMaintenance')]
    public function hideEditMaintenance()
    {
        $this->showMaintenanceDetailsModal = false;
        $this->selectedMaintenance = null;
        $this->js('window.location.reload()');
    }

    public function showMaintenances($id)
    {
        $this->MaintenanceId = $id;
        $this->showMaintenancesModal = true;
    }
    #[On('showMaintenanceFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    #[On('exportAssetMaintenances')]
    public function exportAssets()
    {
        return (new AssetMaintenanceExport($this->search, $this->filterStatus))->download('assets-maintenance' . now()->toDateTimeString() . '.xlsx');
    }

    public function showDeleteAsset($id)
    {
        $this->assetMaintenanceId = AssetMaintenance::findOrFail($id);
        $this->confirmDeleteAssetModal = true;
    }

    public function deleteAsset($id)
    {
        AssetMaintenance::destroy($id);
        $this->confirmDeleteAssetModal = false;
        $this->assetMaintenanceId = '';

        $this->alert('success', __('messages.assetMaintenanceDeleted'));
    }


    public function clearFilters()
    {

        $this->filterStatus = [];
    }
    public function render()
    {
        $this->clearFilterButton = false;

        $query = AssetMaintenance::query()
            ->with('asset', 'asset.apartments');
            $loggedInUser = user()->id;

            if (!user_can('Show Assets')) {
                $query->whereHas('asset', function ($assetQuery) use ($loggedInUser) {
                    $assetQuery->whereHas('apartments', function ($q) use ($loggedInUser) {
                        $q->where('user_id', $loggedInUser);
                    })
                    ->orWhereHas('apartments', function ($q) use ($loggedInUser) {
                        $q->where('status', 'rented')
                            ->whereHas('tenants', function ($q) use ($loggedInUser) {
                                $q->where('user_id', $loggedInUser);
                            });
                    });
                });
            }
            if (!empty($this->filterStatus)) {
                $query->where(function ($q) {
                    foreach ($this->filterStatus as $status) {
                        $q->orWhere('status', $status);
                    }
                });
                $this->clearFilterButton = true;
            }


        if (!empty($this->search)) {
            $query->whereHas('asset', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });

            $this->clearFilterButton = true;
        }

        $assetMaintenance = $query->paginate(10);

        return view('livewire.asset-maintenances.asset-maintenance-table', [
            'maintenances' => $assetMaintenance
        ]);
    }
}
