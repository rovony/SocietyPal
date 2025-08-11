<?php

namespace App\Livewire\Society;

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Society;
use App\Models\GlobalSetting;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SocietyList extends Component
{
    use LivewireAlert;

    public $search;
    public $showAddSociety = false;
    public $count = 0;
    public $filterStatus = 'all';

    public function mount()
    {
        $this->updatedCount();
    }


    #[On('hideAddSociety')]
    public function hideAddSociety()
    {
        $this->showAddSociety = false;
        $this->js('window.location.reload()');
    }

    #[On('updatedCount')]
    public function updatedCount()
    {
        $this->count = Society::where('approval_status', 'pending')->count();
    }

    public function render()
    {
        return view('livewire.society.society-list');
    }
}
