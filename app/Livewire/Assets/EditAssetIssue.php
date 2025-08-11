<?php

namespace App\Livewire\Assets;

use Livewire\Component;
use App\Models\AssetIssue;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditAssetIssue extends Component
{
    use LivewireAlert;


    public $assetIssueId;
    public $title;
    public $priority;
    public $status;
    public $reported_by;
    public $description;


    public function mount()
    {


        $assetIssue = AssetIssue::findOrFail($this->assetIssueId);

        $this->assetIssueId = $assetIssue->id;
        $this->title = $assetIssue->title;
        $this->priority = $assetIssue->priority;
        $this->status = $assetIssue->status;
        $this->reported_by = $assetIssue->reported_by;
        $this->description = $assetIssue->description;

    }


    public function updateAssetIssue()
    {
        $this->validate([
            'title' => 'required',
            'priority' => 'required',
            'status' => 'required',
        ]);

        $assetIssue = AssetIssue::findOrFail($this->assetIssueId);
        $assetIssue->title = $this->title;
        $assetIssue->priority = $this->priority;
        $assetIssue->status = $this->status;
        $assetIssue->description = $this->description;
        $assetIssue->save();



        $this->alert('success', __('messages.AssetIssueUpdated'));
        $this->dispatch('hideEditAssetIssue');
    }
    public function render()
    {
        return view('livewire.assets.edit-asset-issue');
    }
}
