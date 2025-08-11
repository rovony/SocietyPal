<?php

namespace App\Livewire\Settings;

use App\Models\ModuleSetting;
use App\Models\Role;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ModuleSettings extends Component
{
    use LivewireAlert;
    public $roles;
    public $activeTab = 'Admin';
    public $moduleSettings = [];
    protected $queryString = ['activeTab'];
    
    public function mount()
    {
        $this->roles = Role::pluck('display_name')->toArray();
        $this->loadModuleSettings();
    }

    public function loadModuleSettings()
    {
        $this->moduleSettings = ModuleSetting::all()
            ->groupBy('type')
            ->toArray();
    }

    public function toggleStatus($moduleId)
    {
        $setting = ModuleSetting::find($moduleId);
        $setting->status = $setting->status === 'active' ? 'deactive' : 'active';
        $setting->save();
        $this->clearModuleCache($setting->society_id);
        $this->js("Livewire.navigate(window.location.href)");
        $this->alert('success', __('messages.savedSuccessfully'));

        $this->loadModuleSettings();
    }

    protected function clearModuleCache($societyId)
    {
        $roles = Role::pluck('display_name')->toArray();

        foreach ($roles as $role) {
            cache()->forget('society_role_modules_' . $societyId . "_$role");
        }

        cache()->forget('society_role_modules_' . $societyId . '_all');
    }
    
    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.settings.module-settings');
    }
}