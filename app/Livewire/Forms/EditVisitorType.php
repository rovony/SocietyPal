<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditVisitorType extends Component
{
    use LivewireAlert;

    public $visitorType;
    public $visitorTypeName;
    public $visitorId;

    public function mount()
    {
        $this->visitorTypeName = $this->visitorType->name;
        $this->visitorId = $this->visitorType->id;
    }

    public function submitForm()
    {
        $this->validate([
            'visitorTypeName' => [
                'required',
                Rule::unique('visitor_settings', 'name')
                    ->where('society_id', society()->id)
                    ->ignore($this->visitorId)
            ]

        ]);

        $this->visitorType->name = $this->visitorTypeName;
        $this->visitorType->save();

        $this->alert('success', __('messages.visitorUpdated'));

        $this->dispatch('hideEditVisitorType');
    }

    public function render()
    {
        return view('livewire.forms.edit-visitor-type');
    }
}
