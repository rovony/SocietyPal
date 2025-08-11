<?php

namespace App\Livewire\Assets;

use App\Models\AssetManagement;
use Livewire\Component;

class AssetDetail extends Component
{
    public $asset;
    public $assetId;
    public $activeTab = 'maintenance';


    public function mount()
    {
        $this->asset = AssetManagement::findOrFail($this->assetId);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.assets.asset-detail');
    }
}
