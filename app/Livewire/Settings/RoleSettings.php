<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Module;
use App\Models\Role;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class RoleSettings extends Component
{
    use LivewireAlert;

    public $permissions;
    public $roles;

    public function mount()
    {
        $this->permissions = Module::with('permissions')->get();
        $this->roles = Role::where('display_name', '<>', 'Admin')->orderByRaw("FIELD(display_name, 'Manager', 'Owner', 'Tenant', 'Guard')")->get();
    }

    public function setPermission($roleID, $permissionID)
    {
        $role = Role::find($roleID);
        $role->givePermissionTo($permissionID);
        $this->alert('success', __('messages.savedSuccessfully'));

    }

    public function removePermission($roleID, $permissionID)
    {
        $role = Role::find($roleID);
        $role->revokePermissionTo($permissionID);
        $this->alert('success', __('messages.savedSuccessfully'));

    }

    public function render()
    {
        return view('livewire.settings.role-settings');
    }
}
