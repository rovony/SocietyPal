<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use App\Models\Module;
use Spatie\Permission\Models\Permission;
use App\Models\Society;
use App\Models\ModuleSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $societies = Society::all();
        $types = ['Admin', 'Manager', 'Owner', 'Tenant', 'Guard'];
        $moduleName = 'Forum';
        foreach ($societies as $society) {

            if (!Module::where('name', $moduleName)->exists()) {
                $module = Module::firstOrCreate(['name' => $moduleName]);

                // Create permissions for the module
                $permissions = [
                    ['name' => "Create $moduleName", 'module_id' => $module->id, 'guard_name' => 'web'],
                    ['name' => "Show $moduleName", 'module_id' => $module->id, 'guard_name' => 'web'],
                    ['name' => "Update $moduleName", 'module_id' => $module->id, 'guard_name' => 'web'],
                    ['name' => "Delete $moduleName", 'module_id' => $module->id, 'guard_name' => 'web'],
                ];
            }

            foreach ($permissions as $permission) {
                Permission::firstOrCreate($permission);
            }

            $allPermissions = Permission::pluck('name')->toArray();

            // Now loop over societies to assign roles and module settings

            $adminRole = Role::where('name', 'Admin_' . $society->id)->first();
            $managerRole = Role::where('name', 'Manager_' . $society->id)->first();

            if ($adminRole) {
                $adminRole->syncPermissions($allPermissions);
            }

            if ($managerRole) {
                $managerRole->syncPermissions($allPermissions);
            }

            foreach ($types as $type) {
                if (!ModuleSetting::where('society_id', $society->id)->where('module_name', $moduleName)->where('type', $type)->exists()) {
                    ModuleSetting::create([
                        'society_id' => $society->id,
                        'module_name' => $moduleName,
                        'status' => 'active',
                        'type' => $type,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $moduleName = 'Forum';

        Permission::where('name', 'like', "%$moduleName")->delete();
        ModuleSetting::where('module_name', $moduleName)->delete();
        Module::where('name', $moduleName)->delete();
    }
};
