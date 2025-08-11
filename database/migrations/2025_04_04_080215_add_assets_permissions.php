<?php

use App\Models\Role;
use App\Models\Module;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Society;

return new class extends Migration
{
    public function up(): void
    {
        $societies = Society::all();
        foreach ($societies as $society) {
            $modules = [
                'Assets',
            ];

            $permissions = [];


            foreach ($modules as $moduleName) {
                if (!Module::where('name', $moduleName)->exists()) {
                    $module = Module::firstOrCreate(['name' => $moduleName]);


                    $modulePermissions = [
                        ['name' => "Create $moduleName", 'module_id' => $module->id, 'guard_name' => 'web'],
                        ['name' => "Show $moduleName", 'module_id' => $module->id, 'guard_name' => 'web'],
                        ['name' => "Update $moduleName", 'module_id' => $module->id, 'guard_name' => 'web'],
                        ['name' => "Delete $moduleName", 'module_id' => $module->id, 'guard_name' => 'web'],
                    ];

                    $permissions = array_merge($permissions, $modulePermissions);
                }

                foreach ($permissions as $permission) {
                    Permission::firstOrCreate($permission);
                }
                $adminRole = Role::where('name', 'Admin_' . $society->id)->first();
                $managerRole = Role::where('name', 'Manager_' . $society->id)->first();

                if ($adminRole) {
                    $allPermissions = Permission::pluck('name')->toArray();
                    $adminRole->syncPermissions($allPermissions);
                }

                if ($managerRole) {
                    $managerPermissions = Permission::pluck('name')->toArray();
                    $managerRole->syncPermissions($managerPermissions);
                }
            }
        }
    }

    public function down(): void
    {

    }
};
