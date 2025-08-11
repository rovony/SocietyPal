<?php

namespace App\Livewire\Forms;

use App\Models\Floor;
use App\Models\Tower;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;

class AddFloor extends Component
{
    use LivewireAlert;

    public $floorName;
    public $towerName;
    public $towers;

    public function mount()
    {
        $this->towers = Tower::all();
    }

    public function submitForm()
    {
        $this->validate([
            'floorName' => 'required|unique:floors,floor_name,NULL,id,tower_id,' . $this->towerName,
            'towerName' => 'required',
        ]);
        $floor = new Floor();
        $floor->floor_name = $this->floorName;
        $floor->tower_id = $this->towerName;
        $floor->save();
        $this->dispatch('hideAddFloor');
        $this->dispatch('floorAdded');
        
        $this->alert('success', __('messages.floorAdded'));

    }

    #[On('resetForm')]

    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['floorName','towerName']);
    }

    public function render()
    {
        return view('livewire.forms.add-floor');
    }
}
