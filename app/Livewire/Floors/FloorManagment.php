<?php

namespace App\Livewire\Floors;

use App\Exports\FloorExport;
use App\Models\Floor;
use App\Models\Tower;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class FloorManagment extends Component
{
    use LivewireAlert;

    protected $listeners = ['refreshFloors' => 'mount'];

    public $floors;
    public $floor;
    public $tower;
    public $search;
    public $showEditFloorModal = false;
    public $showAddFloorModal = false;
    public $confirmSelectedDeleteFloorModal = false;
    public $confirmDeleteFloorModal = false;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $showFilters = false;
    public $clearFilterButton = false;
    public $filterTower = [];

    public function mount()
    {
        $this->floors = Floor::get();
        $this->tower = Tower::get();;
    }

    public function showAddFloor()
    {
        $this->dispatch('resetForm');
        $this->showAddFloorModal = true;
    }

    public function showEditFloor($id)
    {
        $this->floor = Floor::findOrFail($id);
        $this->showEditFloorModal = true;
    }

    public function showDeleteFloor($id)
    {
        $this->floor = Floor::findOrFail($id);
        $this->confirmDeleteFloorModal = true;
    }

    public function deleteFloor($id)
    {
        Floor::destroy($id);

        $this->confirmDeleteFloorModal = false;

        $this->floor= '';
        $this->dispatch('refreshFloors');

        $this->alert('success', __('messages.floorDeleted'));
    }

    #[On('hideEditFloor')]
    public function hideEditFloor()
    {
        $this->showEditFloorModal = false;
        $this->js('window.location.reload()');
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->floors->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;

    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function showSelectedDelete()
    {
        $this->confirmSelectedDeleteFloorModal = true;
    }

    public function deleteSelected()
    {
        Floor::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->confirmSelectedDeleteFloorModal=false;
        $this->alert('success', __('messages.floorDeleted'));

    }

    #[On('showFloorFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterTower = [];
    }
    
    #[On('exportFloor')]
    public function exportFloor()
    {
        return (new FloorExport($this->search, $this->filterTower))->download('floor-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;

        $query = Floor::query();

        if (!empty($this->filterTower)) {
            $query->whereHas('tower', function ($q) {
                $q->whereIn('tower_name', $this->filterTower);
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->search)) {
            $query->where('floor_name', 'like', '%' . $this->search . '%');
        }

        $floorData = $query->paginate(10);

        return view('livewire.floors.floor-managment', [
            'floorData' => $floorData,
        ]);
    }

}
