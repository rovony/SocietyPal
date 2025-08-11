<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Apartment;
use Livewire\Attributes\On;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ApartmentType extends Component
{

    use LivewireAlert;

    protected $listeners = ['refreshApartments' => 'mount'];

    public $apartments;
    public $apartment;
    public $showEditApartmentModal = false;
    public $showAddApartmentModal = false;
    public $confirmDeleteApartmentModal = false;

    public function mount()
    {
        $this->apartments = Apartment::get();
    }

    public function showAddApartment()
    {
        $this->showAddApartmentModal = true;
    }

    public function showEditApartment($id)
    {
        $this->apartment = Apartment::findOrFail($id);
        $this->showEditApartmentModal = true;
    }

    public function showDeleteApartment($id)
    {
        $this->apartment = Apartment::findOrFail($id);
        $this->confirmDeleteApartmentModal = true;
    }

    public function deleteApartment($id)
    {
        Apartment::destroy($id);

        $this->confirmDeleteApartmentModal = false;

        $this->apartment= '';
        $this->dispatch('refreshApartments');

        $this->alert('success', __('messages.apartmentTypeDeleted'));
    }

    #[On('hideEditApartment')]
    public function hideEditApartment()
    {
        $this->showEditApartmentModal = false;
        $this->dispatch('refreshApartments');
    }

    #[On('hideAddApartment')]
    public function hideAddApartment()
    {
        $this->showAddApartmentModal = false;
        $this->dispatch('refreshApartments');
    }

    public function render()
    {
        return view('livewire.settings.apartment-type');
    }
}
