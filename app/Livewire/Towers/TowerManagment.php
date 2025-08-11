<?php

namespace App\Livewire\Towers;

use App\Exports\TowerExport;
use App\Models\Tower;
use Livewire\Component;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class TowerManagment extends Component
{
    use LivewireAlert;

    protected $listeners = ['refreshTowers' => 'mount'];

    public $towers;
    public $tower;
    public $search;
    public $showEditTowerModal = false;
    public $showAddTowerModal = false;
    public $confirmSelectedDeleteTowerModal = false;
    public $confirmDeleteTowerModal = false;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;

    public function mount()
    {
        $this->towers = Tower::with('apartmentManagement')->get();

    }

    public function showAddTower()
    {
        $this->dispatch('resetForm');
        $this->showAddTowerModal = true;
    }

    public function showEditTower($id)
    {
        $this->tower = Tower::findOrFail($id);
        $this->showEditTowerModal = true;
    }

    public function showDeleteTower($id)
    {
        $this->tower = Tower::findOrFail($id);
        $this->confirmDeleteTowerModal = true;
    }

    public function deleteTower($id)
    {
        Tower::destroy($id);

        $this->confirmDeleteTowerModal = false;

        $this->tower= '';
        $this->dispatch('refreshTowers');

        $this->alert('success', __('messages.towerDeleted'));
    }

    #[On('hideEditTower')]
    public function hideEditTower()
    {
        $this->showEditTowerModal = false;
        $this->js('window.location.reload()');
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->towers->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;

    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function showSelectedDelete()
    {
        $this->confirmSelectedDeleteTowerModal = true;
    }

    public function deleteSelected()
    {
        Tower::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->confirmSelectedDeleteTowerModal=false;
        $this->alert('success', __('messages.towerDeleted'));

    }

    #[On('exportTower')]
    public function exportTower()
    {
        return (new TowerExport($this->search))->download('tower-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $query = Tower::query();

        $query = $query->where('tower_name', 'like', '%'.$this->search.'%')->paginate(10);

        return view('livewire.towers.tower-managment', [
            'towerData' => $query
        ]);

    }
}
