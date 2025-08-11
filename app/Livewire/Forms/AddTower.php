<?php

namespace App\Livewire\Forms;

use App\Models\Tower;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\Attributes\On;

class AddTower extends Component
{
    use LivewireAlert;

    public $towerName;

    public function submitForm()
    {
        $this->validate([
            'towerName' => 'required|unique:towers,tower_name,NULL,id,society_id,' . society()->id,
        ]);

        $tower = new Tower();
        $tower->tower_name = $this->towerName;
        $tower->save();
        $this->dispatch('hideAddTower');
        $this->dispatch('towerAdded');

        $this->alert('success', __('messages.towerAdded'));

    }

    #[On('resetForm')]

    public function resetForm()
    {
        $this->reset('towerName');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.forms.add-tower');
    }
}
