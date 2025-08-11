<?php

namespace App\Livewire\Assets;

use App\Models\User;
use App\Helper\Files;
use Livewire\Component;
use App\Models\AssetIssue;
use Livewire\WithFileUploads;
use App\Models\AssetManagement;
use Illuminate\Support\Facades\Request;
use App\Notifications\AssetIssueNotification;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddAssetIssue extends Component
{
    use LivewireAlert, WithFileUploads;


    public $assetId;
    public $title;
    public $description;
    public $status;
    public $priority;
    public $documentPath;
    public $assetsApartment = [];


    public function mount()
    {
        $this->assetId = $this->assetId;
        $loggedInUser = user()->id;
        if (user_can('Create Assets')) {
            $this->assetsApartment = AssetManagement::all();
        } else {
            $this->assetsApartment = AssetManagement::whereHas('apartments', function ($query) use ($loggedInUser) {
                $query->where('user_id', $loggedInUser);
            })->orWhereHas('apartments', function ($query) use ($loggedInUser) {
                $query->where('status', 'rented')
                    ->whereHas('tenants', function ($q) use ($loggedInUser) {
                        $q->where('user_id', $loggedInUser);
                    });
            })->get();
        }
    }



    public function saveAssetIssue()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,process,resolved',
            'assetId' => 'required|exists:asset_managements,id',
            'priority' => 'required|in:low,medium,high',
        ]);

        if ($this->documentPath) {
            $filename = Files::uploadLocalOrS3($this->documentPath, AssetIssue::FILE_PATH . '/');
            $this->documentPath = $filename;
        }

        $assetIssue = new AssetIssue();
        $assetIssue->asset_id = $this->assetId;
        $assetIssue->title = $this->title;
        $assetIssue->description = $this->description;
        $assetIssue->status = $this->status;
        $assetIssue->priority = $this->priority;
        $assetIssue->reported_by = user()->id;
        $assetIssue->file_path = $this->documentPath;
        $assetIssue->save();
        $this->resetForm();
        $this->alert('success', __('messages.AssetAdded'));


            $asset = AssetManagement::find($this->assetId);
            if ($asset) {
                $owner = $asset->apartments ? User::find($asset->apartments->user_id) : null;
                if ($owner) {
                    $owner->notify(new AssetIssueNotification($assetIssue));
                    $this->alert('success', __('messages.notificationSentToOwner.'));
                }
            }

            if (Request::is('assets*')) {
                return redirect()->route('assets.show', ['asset' => $this->assetId]);
            }

        return redirect()->route('asset-issue.index');
    }

    public function removeFile()
    {
        $this->documentPath = null;
        $this->dispatch('photo-removed');
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->status = '';
        $this->priority = '';
    }

    public function render()
    {
        return view('livewire.assets.add-asset-issue');
    }
}
