<?php

namespace App\Livewire\Assets;

use Livewire\Component;
use App\Models\AssetIssue;
use App\Models\AssetMaintenance;
use Livewire\Attributes\On;

class ShowIssues extends Component
{

    public $assetId;
    public $showAssetIssue = false;
    public $showAddAssetIssue = false;
    public $showAssetIssueModal = false;
    public $showDeleteAssetIssueModal = false;
    public $selectedIssue;
    public $maintenances;
    public $assetIssues;
    public $showIssueModal = false;
    public $issueId;
    public $asset;
    public $confirmDeleteAssetModal = false;
    public $assetIssueId;

    public function mount()
    {
        $this->assetId = $this->assetId;
        $this->assetIssues = AssetIssue::where('asset_id', $this->assetId)->get();
    }

    #[On('hideAddAssetIssue')]
    public function hideAddAssetIssue()
    {
        $this->showAddAssetIssue = false;
        $this->js('window.location.reload()');
    }


    public function showEditAssetIssue($id)
    {
        $this->selectedIssue = $id;
        $this->showAssetIssueModal = true;
    }

    #[On('hideEditAssetIssue')]
    public function hideEditAssetIssue()
    {
        $this->showAssetIssueModal = false;
        $this->selectedIssue = null;
        $this->js('window.location.reload()');
    }

    public function showAssetIssues($id)
    {
        $this->issueId = $id;
        $this->showIssueModal = true;
    }

    public function showDeleteAsset($id)
    {
        $this->asset = AssetMaintenance::findOrFail($id);
        $this->confirmDeleteAssetModal = true;
    }

    public function deleteAsset($id)
    {
        AssetMaintenance::destroy($id);
        $this->confirmDeleteAssetModal = false;
        $this->asset = '';

        $this->alert('success', __('messages.assetDeleted'));
    }


    public function render()
    {
        return view('livewire.assets.show-issues');
    }
}
