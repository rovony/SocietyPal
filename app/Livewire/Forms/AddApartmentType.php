<?php

namespace App\Livewire\Forms;

use App\Models\Apartment;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class AddApartmentType extends Component
{
    use LivewireAlert;

    public $apartmentType;

    public function submitForm()
    {
        $this->validate([
            'apartmentType' => 'required|unique:apartments,apartment_type,NULL,id,society_id,' . society()->id,
        ]);

        $apartment = new Apartment();
        $apartment->apartment_type = $this->apartmentType;

        $apartment->save();
        $this->alert('success', __('messages.apartmentTypeAdded'));
        $this->dispatch('hideAddApartment');
    }
    public function render()
    {
        return view('livewire.forms.add-apartment-type');
    }
}
