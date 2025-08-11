<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditTower extends Component
{
    use LivewireAlert;

    public $tower;
    public $towerName;
    public $towerId;

    public function mount()
    {
        $this->towerName = $this->tower->tower_name;
        $this->towerId = $this->tower->id;

    }

    public function submitForm()
    {
        $this->validate([
            'towerName' => [
                'required',
                Rule::unique('towers', 'tower_name')
                    ->where('society_id', society()->id)
                    ->ignore($this->towerId)
            ]

        ]);

        $this->tower->tower_name = $this->towerName;
        $this->tower->save();

        $this->dispatch('hideEditTower');
        $this->alert('success', __('messages.towerUpdated'));

    }

    public function render()
    {
        return view('livewire.forms.edit-tower');
    }
}
