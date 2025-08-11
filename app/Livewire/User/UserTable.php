<?php

namespace App\Livewire\User;

use App\Exports\UsersExport;
use App\Models\User;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use App\Models\Role;

class UserTable extends Component
{
    use LivewireAlert;
    use WithPagination, WithoutUrlPagination;
    protected $listeners = ['refreshUsers' => 'mount'];

    public $search;
    public $seletedUser;
    public $roles;
    public $firstUserId;
    public $showEditUserModal = false;
    public $confirmDeleteUserModal = false;
    public $clearFilterButton = false;
    public $showFilters = true;
    public $filterRoles = [];
    public $filterStatus = ['active'];
    public $selectAll = false;
    public $selected = [];
    public $showActions = false;
    public $usersData;
    public $confirmSelectedDeleteUserModal = false;

    public function mount()
    {
        $this->roles = Role::whereIn('name', ['Admin_'.society()->id, 'Manager_'.society()->id, 'Guard_'.society()->id])->get();
        $firstUser = User::whereHas('roles', function ($query) {
            $query->where('display_name', 'Admin');
        })->first();
        $this->firstUserId = $firstUser ? $firstUser->id : null;
        $this->usersData = User::where('id', '!=', $this->firstUserId)->get();
    }

    public function showEditUser($id)
    {
        $this->seletedUser = User::findOrFail($id);
        $this->showEditUserModal = true;
    }

    #[On('hideEditUser')]
    public function hideEditUser()
    {
        $this->showEditUserModal = false;
    }

    public function showDeleteUser($id)
    {
        $this->seletedUser = User::findOrFail($id);
        $this->confirmDeleteUserModal = true;
    }

    public function deleteUser($id)
    {
        User::destroy($id);
        $this->confirmDeleteUserModal = false;
        $this->seletedUser = '';

        $this->alert('success', __('messages.userDeleted'));
    }

    public function showSelectedDeleteUser()
    {
        $this->confirmSelectedDeleteUserModal = true;
    }

    public function setUserRole($roleId, $userID)
    {
        $role = Role::find($roleId);
        $user = User::find($userID);
        if ($user) {
            $user->role_id = $roleId;
            $user->save();
            $user->syncRoles([$role->name]);
        }
        $this->alert('success', __('messages.savedSuccessfully'));

        $this->redirect(route('users.index'), navigate: true);
    }

    #[On('showUserFilters')]
    public function showFiltersSection()
    {
        $this->showFilters = true;
    }

    public function clearFilters()
    {
        $this->filterRoles = [];
        $this->filterStatus = [];
        $this->search = '';
        $this->clearFilterButton = false;
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value ? $this->usersData->pluck('id')->toArray() : [];
        $this->showActions = count($this->selected) > 0;
    }

    public function updatedSelected()
    {
        $this->showActions = count($this->selected) > 0;
    }

    public function deleteSelected()
    {
        User::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        $this->showActions=false;
        $this->confirmSelectedDeleteUserModal=false;
        $this->alert('success', __('messages.userDeleted'));
    }


    #[On('exportUsers')]
    public function exportUsers()
    {
        return (new UsersExport($this->search, $this->filterRoles, $this->filterStatus))->download('users-'.now()->toDateTimeString().'.xlsx');
    }

    public function render()
    {
        $this->clearFilterButton = false;

        $query = User::query();
        $this->roles = Role::whereNotIn('display_name', ['Tenant', 'Owner'])->get();
        $query->whereDoesntHave('role', function ($q) {
            $q->whereIn('display_name', ['Owner', 'Tenant']);
        });

        $query->whereNotNull('society_id');

        if (!user_can('Show User')) {
            $query->where('id', user()->id);
        }

        if (!empty($this->search)) {
            $query->where(function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_number', 'like', '%' . $this->search . '%');
            });
            $this->clearFilterButton = true;
        } 

        if (!empty($this->filterRoles)) {
            $query->whereHas('roles', function ($q) {
                $q->whereIn('id', $this->filterRoles);
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

        $query->with('roles');

        $users = $query->paginate(10);

        return view('livewire.user.user-table', [
            'users' => $users
        ]);
    }
}
