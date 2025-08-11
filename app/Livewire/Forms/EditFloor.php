<?php

namespace App\Livewire\Forms;

use App\Models\Tower;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditFloor extends Component
{
    use LivewireAlert;

    public $floor;
    public $floorId;
    public $floorName;
    public $towers;
    public $towerId;

    public function mount()
    {
        $this->floorName = $this->floor->floor_name;
        $this->floorId = $this->floor->id;
        $this->towers=Tower::all();
        $this->towerId =$this->floor->tower_id;
    }

    public function submitForm()
    {
        $this->validate([
            'floorName' => [
            'required',
            Rule::unique('floors', 'floor_name')
                ->where('tower_id', $this->towerId)
                ->ignore($this->floorId)
        ],
        'towerId' => 'required',
        ]);

        $this->floor->floor_name = $this->floorName;
        $this->floor->tower_id=$this->towerId;
        $this->floor->save();

        $this->alert('success', __('messages.floorUpdated'));

        $this->dispatch('hideEditFloor');
    }

    public function render()
    {
        return view('livewire.forms.edit-floor');
    }
}
