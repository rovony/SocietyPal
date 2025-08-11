<?php

namespace App\Livewire\Assets;

use Livewire\Component;
use App\Models\AssetIssue;

class ShowIssuesDetails extends Component
{

    public $assetIssueId;
    public $title;
    public $priority;
    public $status;
    public $reported_by;
    public $description;
    public $documentPath;
    public $assetName;


    public function mount()
    {



        $assetIssue = AssetIssue::findOrFail($this->assetIssueId);

        $this->assetIssueId = $assetIssue->id;
        $this->assetName = $assetIssue->asset->name;
        $this->title = $assetIssue->title;
        $this->priority = $assetIssue->priority;
        $this->status = $assetIssue->status;
        $this->reported_by = $assetIssue->reported_by;
        $this->description = $assetIssue->description;
        $this->documentPath = $assetIssue->photo_url;

    }
    public function render()
    {
        return view('livewire.assets.show-issues-details');
    }
}
