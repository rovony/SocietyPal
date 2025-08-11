<?php

namespace App\Livewire\Forms;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditApartmentType extends Component
{
    use LivewireAlert;

    public $apartment;
    public $apartmentType;
    public $apartmentId;

    public function mount()
    {
        $this->apartmentType = $this->apartment->apartment_type;
        $this->apartmentId = $this->apartment->id;
    }

    public function submitForm()
    {
        $this->validate([
            'apartmentType' => [
                'required',
                Rule::unique('apartments', 'apartment_type')
                    ->where('society_id', society()->id)
                    ->ignore($this->apartmentId)
            ]

        ]);

        $this->apartment->apartment_type = $this->apartmentType;
        $this->apartment->save();

        $this->alert('success', __('messages.apartmentTypeUpdated'));

        $this->dispatch('hideEditApartment');
    }

    public function render()
    {
        return view('livewire.forms.edit-apartment-type');
    }
}
