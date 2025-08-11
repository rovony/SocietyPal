<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\VisitorTypeSettingsModel;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AddVisitorType extends Component
{

    use LivewireAlert;

    public $visitorTypeName;

    public function submitForm()
    {
        $this->validate([
            'visitorTypeName' => 'required|unique:visitor_settings,name,NULL,id,society_id,' . society()->id,
        ]);

        $visitorType = new VisitorTypeSettingsModel();
        $visitorType->name = $this->visitorTypeName;
        $visitorType->save();

        $this->alert('success', __('messages.visitorTypeAdded'));

        $this->dispatch('hideAddVisitorType');
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->resetValidation();
        $this->reset(['visitorTypeName']);
    }

    public function render()
    {
        return view('livewire.forms.add-visitor-type');
    }
}
