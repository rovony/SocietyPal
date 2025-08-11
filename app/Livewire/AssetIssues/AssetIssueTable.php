<?php

namespace App\Livewire\AssetIssues;

use Livewire\Component;
use App\Models\AssetIssue;
use Livewire\Attributes\On;
use App\Exports\AssetIssueExport;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AssetIssueTable extends Component
{
    use LivewireAlert;

    public $search;
    public $showAddMaintenance = false;
    public $showFilterButton = true;
    public $clearFilterButton = false;
    public $showFilters = false;
    public $assetId;
    public $selected = [];
    public $showActions = false;
    public $selectAll = false;
    public $showDeleteMaintenanceModal = false;
    public $issueId;
    public $showIssueModal = false;
    public $showIssueDetailsModal = false;
    public $selectedIssue;
    public $asset;
    public $confirmDeleteAssetModal = false;
    public $filterStatus = [];
    public $filterPriority = [];






    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->maintenances->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function showEditAssetIssue($id)
    {
        $this->selectedIssue = $id;
        $this->showIssueDetailsModal = true;
    }

    #[On('hideEditAssetIssue')]
    public function hideEditAssetIssue()
    {
        $this->showIssueDetailsModal = false;
        $this->selectedIssue = null;
        $this->js('window.location.reload()');
    }

    public function showIssues($id)
    {
        $this->issueId = $id;
        $this->showIssueModal = true;
    }

    public function showDeleteAsset($id)
    {
        $this->asset = AssetIssue::findOrFail($id);
        $this->confirmDeleteAssetModal = true;
    }

    public function deleteAsset($id)
    {
        AssetIssue::destroy($id);
        $this->confirmDeleteAssetModal = false;
        $this->asset = '';

        $this->alert('success', __('messages.assetIssueDeleted'));
    }

    #[On('showAssetIssueFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;

    }

    public function clearFilters()
    {
        $this->filterStatus = [];
        $this->filterPriority = [];
        $this->search = '';
        $this->clearFilterButton = false;
    }

    #[On('exportAssetIssues')]
    public function exportAssets()
    {
        return (new AssetIssueExport($this->search, $this->filterStatus,  $this->filterPriority))->download('assets-maintenance' . now()->toDateTimeString() . '.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;

        $query = AssetIssue::query()
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

        if (!empty($this->search)) {
            $query->where(function ($q) {
            $q->whereHas('asset', function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('title', 'like', '%' . $this->search . '%');
            });

            $this->clearFilterButton = true;
        }

        if (!empty($this->filterStatus)) {
            $query->where(function ($q) {
                foreach ($this->filterStatus as $status) {
                    $q->orWhere('status', $status);
                }
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterPriority)) {
            $query->where(function ($q) {
                foreach ($this->filterPriority as $priority) {
                    $q->orWhere('priority', $priority);
                }
            });
            $this->clearFilterButton = true;
        }

        $assetIssue = $query->paginate(10);
        return view('livewire.asset-issues.asset-issue-table' , [
            'assetIssues' => $assetIssue
        ]);
    }
}
