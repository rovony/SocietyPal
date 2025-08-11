<?php

namespace App\Livewire\Assets;

use App\Models\Tower;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Exports\AssetExport;
use App\Models\AssetsCategory;
use App\Models\AssetManagement;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AssetTable extends Component
{
    use LivewireAlert;
    public $search;
    public $asset;
    public $categories;
    public $showEditAssetModal = false;
    public $showAssetDetailModal = false;
    public $confirmDeleteAssetModal = false;
    public $clearFilterButton = false;
    public $showFilters = false;
    public $filterCategories = [];
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $assetsData;
    public $confirmSelectedDeleteAssetModal = false;
    public $assetData;
    public $assetEdit;
    public $assetCategories;
    public $tower;
    public $filterTower = [];
    public $showFilterButton = true;


    public function mount()
    {
        $this->assetData = AssetManagement::get();
        $this->assetCategories = AssetsCategory::all();
        $this->tower = Tower::get();
    }
    public function showEditAsset($id)
    {
        $this->assetEdit = AssetManagement::findOrFail($id);
        $this->showEditAssetModal = true;
    }

    #[On('hideEditAsset')]
    public function hideEditAsset()
    {
        $this->showEditAssetModal = false;
    }

    public function showAssetDetail($id)
    {
        $this->asset = AssetManagement::findOrFail($id);
        $this->showAssetDetailModal = true;
    }

    #[On('hideAssetDetail')]
    public function hideAssetDetail()
    {
        $this->showAssetDetailModal = false;
    }

    public function showDeleteAsset($id)
    {
        $this->asset = AssetManagement::findOrFail($id);
        $this->confirmDeleteAssetModal = true;
    }

    public function deleteAsset($id)
    {
        AssetManagement::destroy($id);
        $this->confirmDeleteAssetModal = false;
        $this->asset = '';

        $this->alert('success', __('messages.assetDeleted'));
    }

    public function showSelectedDeleteAsset()
    {
        $this->confirmSelectedDeleteAssetModal = true;
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->assetsData->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function deleteSelected()
    {
        AssetManagement::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions = false;
        $this->confirmSelectedDeleteAssetModal = false;
        $this->alert('success', __('messages.assetDeleted'));
    }

    #[On('exportAssets')]
    public function exportAssets()
    {
        return (new AssetExport($this->search, $this->filterCategories, $this->filterTower))->download('assets-' . now()->toDateTimeString() . '.xlsx');
    }


    #[On('showAssetFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;

    }

    public function clearFilters()
    {
        $this->filterCategories = [];

        $this->search = '';
        $this->clearFilterButton = false;
        $this->filterTower = [];
    }

    public function render()
    {
        $this->clearFilterButton = false;

        $this->categories = AssetsCategory::all();
        $query = AssetManagement::query();
        $loggedInUser = user()->id;

        if (!user_can('Show Assets')) {
            $query->where(function ($subQuery) use ($loggedInUser) {
                $subQuery->whereHas('apartments', function ($q) use ($loggedInUser) {
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
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhereHas('apartments', function ($subQuery) {
                  $subQuery->where('apartment_number', 'like', '%' . $this->search . '%');
              });
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterCategories)) {
            $query->whereHas('category', function ($q) {
            $q->whereIn('id', $this->filterCategories);
            });
            $this->clearFilterButton = true;
        }
        if (!empty($this->filterTower)) {
            $query->whereHas('towers', function ($query) {
                $query->whereIn('tower_name', $this->filterTower);
            });
            $this->clearFilterButton = true;
        }


        $this->assetsData = $query->get();
        $assets = $query->paginate(10);

        return view('livewire.assets.asset-table', [
            'assets' => $assets
        ]);

    }
}
