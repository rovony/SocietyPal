<?php

namespace App\Livewire\Towers;

use Livewire\Component;
use Livewire\Attributes\On;

class TowerManagmentList extends Component
{
    public $search;
    public $showAddTowerModal = false;

    #[On('hideAddTower')]
    public function hideAddTower()
    {
        $this->showAddTowerModal = false;
        $this->js('window.location.reload()');
    }

    public function render()
    {
        return view('livewire.towers.tower-managment-list');
    }
}
