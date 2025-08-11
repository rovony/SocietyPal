<?php

namespace App\Livewire\AssetIssues;

use Livewire\Component;
use Livewire\Attributes\On;

class AssetIssueList extends Component
{

    public $search;
    public $showAddIssue = false;
    public $showFilterButton = true;

    #[On('hideAddMaintenance')]
    public function hideAddIssue()
    {
        $this->showAddIssue = false;
        $this->js('window.location.reload()');
    }
    public function hideAddAsset()
    {
        $this->showAddIssue = false;
        $this->js('window.location.reload()');
    }

    #[On('clearAssetFilter')]
    public function clearAssetFilter()
    {
        $this->showFilterButton = false;
        $this->search = '';
    }

    #[On('hideAssetFilters')]
    public function hideAssetFiltersBtn()
    {
        $this->showFilterButton = true;
    }
    public function render()
    {
        return view('livewire.asset-issues.asset-issue-list');
    }
}
