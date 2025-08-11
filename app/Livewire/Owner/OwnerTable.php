<?php

namespace App\Livewire\Owner;

use App\Exports\OwnersExport;
use App\Models\ApartmentManagement;
use App\Models\Tenant;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Role;
use App\Models\User;


class OwnerTable extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    protected $listeners = ['refreshOwners' => '$refresh'];

    public $search;
    public $selectedOwner;
    public $selectedEditOwner;
    public $roles;
    public $showEditOwnerModal = false;
    public $confirmDeleteOwnerModal = false;
    public $clearFilterButton = false;
    public $showFilters = true;
    public $filterStatus = ['active'];
    public $filterApartment = [];
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $ownersData;
    public $confirmSelectedDeleteOwnerModal = false;
    public $apartments;

    public function mount()
    {
        $this->roles = Role::where('name' , 'Owner_'.society()->id)->get();
        $this->ownersData = User::whereHas('roles', function ($query) {
            $query->where('display_name', 'Owner');
        })->get();
        $this->apartments = ApartmentManagement::get();
    }

    public function showEditOwner($id)
    {
        $this->selectedEditOwner = User::findOrFail($id);
        $this->showEditOwnerModal = true;
    }

    #[On('hideEditOwner')]
    public function hideEditOwner()
    {
        $this->showEditOwnerModal = false;
        $this->js('window.location.reload()');
    }

    public function showDeleteOwner($id)
    {
        $this->selectedOwner = User::findOrFail($id);
        $this->confirmDeleteOwnerModal = true;
    }

    public function deleteOwner($id)
    {
        $owner = User::findOrFail($id);

        ApartmentManagement::where('user_id', $id)->update([
            'status' => 'not_sold',
            'user_id' => null,
        ]);
        $owner->delete();
        $this->confirmDeleteOwnerModal = false;
        $this->selectedOwner = '';

        $this->alert('success', __('messages.ownerDeleted'));

    }

    public function showSelectedDeleteOwner()
    {
        $this->confirmSelectedDeleteOwnerModal = true;
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->ownersData->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function deleteSelected()
    {
        $selectedOwners = User::whereIn('id', $this->selected)->get();

        foreach ($selectedOwners as $owner) {
            ApartmentManagement::where('user_id', $owner->id)->update([
                'status' => 'not_sold',
                'user_id' => null,
            ]);
        }

        User::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->confirmSelectedDeleteOwnerModal=false;
        $this->alert('success', __('messages.ownerDeleted'));
    }

    #[On('showOwnerFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->filterStatus = [];
        $this->filterApartment = [];
        $this->search = '';
        $this->clearFilterButton = false;
    }

    #[On('exportOwners')]
    public function exportOwners()
    {
        return (new OwnersExport($this->search, $this->filterStatus, $this->filterApartment))->download('owners-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;
        $query = User::query()->with('apartment')
            ->whereHas('role', function ($q) {
                $q->where('display_name', 'Owner');
        });

        if (!user_can('Show Owner')) {
            if (user()->role->display_name === 'Tenant') {
                $tenant = Tenant::where('user_id', user()->id)->first();
                $ownerUserIds = ApartmentManagement::whereHas('tenants', function ($q) use ($tenant) {
                    $q->where('tenant_id', $tenant->id)
                      ->where('apartment_tenant.status', 'current_resident');
                })->pluck('user_id');
                $query->whereIn('id', $ownerUserIds);
            }  else {
                $query->where('id', user()->id);
            }
        }

        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_number', 'like', '%' . $this->search . '%');
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterStatus)) {
            $query->where(function ($q) {
                foreach ($this->filterStatus as $status) {
                    $q->orWhere('status', $status);
                }
            });
            $this->clearFilterButton = true;
        }

        if (!empty($this->filterApartment)) {
            $query->whereHas('apartment', function ($q) {
                $q->whereIn('id', $this->filterApartment);
            });
            $this->clearFilterButton = true;
        }

        $owners = $query->paginate(10);

        return view('livewire.owner.owner-table', [
            'owners' => $owners
        ]);
    }
}
