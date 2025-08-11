<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $adminRole = Role::create(['name' => 'Admin_'.$society->id, 'display_name' => 'Admin', 'guard_name' => 'web', 'society_id' => $society->id]);
        $managerRole = Role::create(['name' => 'Manager_'.$society->id, 'display_name' => 'Manager', 'guard_name' => 'web', 'society_id' => $society->id]);
        $ownerRole = Role::create(['name' => 'Owner_'.$society->id, 'display_name' => 'Owner', 'guard_name' => 'web', 'society_id' => $society->id]);
        $tenantRole = Role::create(['name' => 'Tenant_'.$society->id, 'display_name' => 'Tenant', 'guard_name' => 'web', 'society_id' => $society->id]);
        $guardRole = Role::create(['name' => 'Guard_'.$society->id, 'display_name' => 'Guard', 'guard_name' => 'web', 'society_id' => $society->id]);

        $allPermissions = Permission::get()->pluck('name')->toArray();
        $adminRole->syncPermissions($allPermissions);

        $managerPermissions = Permission::get()->pluck('name')->toArray();
        $managerRole->syncPermissions($managerPermissions);

        $ownerPermissions = Permission::whereIn('name', ['Create Book Amenity' ,'Create Tickets'])->get();
        $ownerRole->syncPermissions($ownerPermissions);

        $tenantPermissions = Permission::whereIn('name', ['Create Book Amenity' ,'Create Tickets'])->get();
        $tenantRole->syncPermissions($tenantPermissions);

        $guardPermissions = Permission::whereIn('name', ['Create Visitors', 'Create Tickets', 'Show Parking','Show Apartment','Update Visitors'])->get();
        $guardRole->syncPermissions($guardPermissions);

    }

}
