<?php

namespace App\Livewire\Tenants;

use App\Models\ApartmentManagement;
use App\Models\Tenant;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class ShowApartments extends Component
{
    use WithFileUploads , LivewireAlert;

    public $tenant;
    public $tenantId;
    public $showAddApartmentTenant = false;
    public $showEditApartmentTenantModal = false;
    public $showDeleteApartmentTenantModal = false;
    public $selectedApartment;
    public $selectedApartmentForEdit;

    public function mount()
    {
        $this->tenantId = $this->tenantId;
        $this->tenant = Tenant::with('apartments')->findOrFail($this->tenantId);
    }

    #[On('hideAddApartmentTenant')]
    public function hideAddApartmentTenant()
    {
        $this->showAddApartmentTenant = false;
        $this->js('window.location.reload()');
    }

    public function showEditApartmentTenant($id)
    {
        $this->selectedApartmentForEdit = ApartmentManagement::findOrFail($id);
        $this->showEditApartmentTenantModal = true;
    }

    public function hideEditApartmentTenant()
    {
        $this->showEditApartmentTenantModal = false;
        $this->js('window.location.reload()');
    }

    public function showDeleteApartmentTenant($id)
    {
        $this->selectedApartment = ApartmentManagement::findOrFail($id);
        $this->showDeleteApartmentTenantModal = true;
    }

    public function deleteApartmentTenant()
    {
        if ($this->selectedApartment) {
            if ($this->selectedApartment->user_id) {
                $this->selectedApartment->update(['status' => 'available_for_rent']);
            } else {
                $this->selectedApartment->update(['status' => 'not_sold']);
            }
            $this->tenant->apartments()->detach($this->selectedApartment->id);

            $this->alert('success', __('messages.apartmentManagementDeleted'));
            $this->showDeleteApartmentTenantModal = false;
            $this->selectedApartment = null;
        }
    }

    public function render()
    {
        return view('livewire.tenants.show-apartments');
    }
}
