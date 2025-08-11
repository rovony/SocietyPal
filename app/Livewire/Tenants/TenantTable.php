<?php

namespace App\Livewire\Tenants;

use App\Exports\TenantsExport;
use App\Models\ApartmentManagement;
use App\Models\Tenant;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Role;

class TenantTable extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    protected $listeners = ['refreshUsers' => '$refresh'];

    public $roles;
    public $search;
    public $seletedTenant;
    public $seletedEditTenant;
    public $showTenantDetailModal = false;
    public $showEditTenantModal = false;
    public $confirmDeleteTenantModal = false;
    public $showFilters = true;
    public $filterStatuses = ['active'];
    public $clearFilterButton = false;
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $tenantsData;
    public $confirmSelectedDeleteTenantModal = false;

    public function mount()
    {
        $this->roles = Role::where('display_name', 'Tenant')->get();
        $this->tenantsData = Tenant::get();
    }


    public function showTenantDetail($id)
    {
        $this->seletedTenant = Tenant::findOrFail($id);
        $this->showTenantDetailModal = true;
    }

    #[On('hideTenantDetail')]
    public function hideTenantDetail()
    {
        $this->showTenantDetailModal = false;
    }

    public function showEditTenant($id)
    {
        $this->seletedEditTenant = Tenant::with('apartments')->findOrFail($id);
        $this->showEditTenantModal = true;
    }

    #[On('hideEditTenant')]
    public function hideEditTenant()
    {
        $this->showEditTenantModal = false;
        $this->js('window.location.reload()');
    }

    public function showDeleteTenant($id)
    {
        $this->seletedTenant = Tenant::findOrFail($id);
        $this->confirmDeleteTenantModal = true;
    }

    public function deleteTenant($id)
    {
        // Find the tenant
        $tenant = Tenant::find($id);

        $apartments = $tenant->apartments;

        foreach ($apartments as $apartment) {
            if ($apartment->user_id) {
                $apartment->update(['status' => 'available_for_rent']);
            } else {
                $apartment->update(['status' => 'not_sold']);
            }
        }

        $tenant->user()->delete();
        $tenant->delete();

        $this->confirmDeleteTenantModal = false;
        $this->seletedTenant = '';

        $this->alert('success', __('messages.tenantDeleted'));
    }


    public function showSelectedDeleteTenant()
    {
        $this->confirmSelectedDeleteTenantModal = true;
    }

    #[On('showTenantFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->filterStatuses = [];
        $this->search = '';
        $this->clearFilterButton = false;
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->tenantsData->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function deleteSelected()
    {
        foreach ($this->selected as $tenantId) {
            $tenant = Tenant::find($tenantId);
            if ($tenant) {
                $apartments = $tenant->apartments;
                foreach ($apartments as $apartment) {
                    if ($apartment->status == 'rented') {
                        if ($apartment->user_id) {
                            $apartment->update(['status' => 'available_for_rent']);
                        } else {
                            $apartment->update(['status' => 'not_sold']);
                        }
                    }
                }
                $tenant->user()->delete();
                $tenant->delete();
            }
        }

        $this->selected = [];
        $this->selectAll = false;
        $this->showActions = false;
        $this->confirmSelectedDeleteTenantModal = false;

        // Show a success alert
        $this->alert('success', __('messages.tenantDeleted'));
    }

    #[On('exportTenants')]
    public function exportTenants()
    {
        return (new TenantsExport($this->search, $this->filterStatuses))->download('tenants-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;

        $query = Tenant::with('user');

        // Apply search filter
        if (!empty($this->search)) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
                ->orWhere('phone_number', 'like', '%'.$this->search.'%');
            });
        }

        if (!user_can('Show Tenant')) {
            if (user()->role->display_name === 'Owner') {
                $loggedInUserId = user()->id;
                $query->whereHas('apartments', function ($apartmentQuery) use ($loggedInUserId) {
                    $apartmentQuery->where('user_id', $loggedInUserId);
                });
            }  else {
                $query->where('user_id', user()->id);
            }
        }

        if (!empty($this->filterStatuses)) {
            $query->whereHas('user', function ($q) {
                $q->whereIn('status', $this->filterStatuses);
            });
            $this->clearFilterButton = true;
        }

        $tenants = $query->paginate(10);

        return view('livewire.tenants.tenant-table', [
            'tenants' => $tenants
        ]);
    }
}
