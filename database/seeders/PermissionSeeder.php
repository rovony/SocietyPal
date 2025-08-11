<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Module;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $towerModule = Module::where('name', 'Tower')->first();
        $floorModule = Module::where('name', 'Floor')->first();
        $apartmentModule = Module::where('name', 'Apartment')->first();
        $userModule = Module::where('name', 'User')->first();
        $ownerModule = Module::where('name', 'Owner')->first();
        $tenantModule = Module::where('name', 'Tenant')->first();
        $rentModule = Module::where('name', 'Rent')->first();
        $utilityBillsModule = Module::where('name', 'Utility Bills')->first();
        $commonAreaBillsModule = Module::where('name', 'Common Area Bills')->first();
        $maintenanceModule = Module::where('name', 'Maintenance')->first();
        $amenitiesModule = Module::where('name', 'Amenities')->first();
        $bookAmenityModule = Module::where('name', 'Book Amenity')->first();
        $visitorsModule = Module::where('name', 'Visitors')->first();
        $noticeBoardModule = Module::where('name', 'Notice Board')->first();
        $ticketModule = Module::where('name', 'Tickets')->first();
        $parkingModule = Module::where('name', 'Parking')->first();
        $serviceProviderModule = Module::where('name', 'Service Provider')->first();
        $serviceTimeLoggingModule = Module::where('name', 'Service Time Logging')->first();
        $settingsModule = Module::where('name', 'Settings')->first();
        $assetsModule = Module::where('name', 'Assets')->first();
        $eventModule = Module::where('name', 'Event')->first();
        $forumModule = Module::where('name', 'Forum')->first();


        $permissions = [
            ['guard_name' => 'web', 'name' => 'Create Tower', 'module_id' => $towerModule->id],
            ['guard_name' => 'web', 'name' => 'Show Tower', 'module_id' => $towerModule->id],
            ['guard_name' => 'web', 'name' => 'Update Tower', 'module_id' => $towerModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Tower', 'module_id' => $towerModule->id],

            ['guard_name' => 'web', 'name' => 'Create Floor', 'module_id' => $floorModule->id],
            ['guard_name' => 'web', 'name' => 'Show Floor', 'module_id' => $floorModule->id],
            ['guard_name' => 'web', 'name' => 'Update Floor', 'module_id' => $floorModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Floor', 'module_id' => $floorModule->id],

            ['guard_name' => 'web', 'name' => 'Create Apartment', 'module_id' => $apartmentModule->id],
            ['guard_name' => 'web', 'name' => 'Show Apartment', 'module_id' => $apartmentModule->id],
            ['guard_name' => 'web', 'name' => 'Update Apartment', 'module_id' => $apartmentModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Apartment', 'module_id' => $apartmentModule->id],

            ['guard_name' => 'web', 'name' => 'Create User', 'module_id' => $userModule->id],
            ['guard_name' => 'web', 'name' => 'Show User', 'module_id' => $userModule->id],
            ['guard_name' => 'web', 'name' => 'Update User', 'module_id' => $userModule->id],
            ['guard_name' => 'web', 'name' => 'Delete User', 'module_id' => $userModule->id],

            ['guard_name' => 'web', 'name' => 'Create Owner', 'module_id' => $ownerModule->id],
            ['guard_name' => 'web', 'name' => 'Show Owner', 'module_id' => $ownerModule->id],
            ['guard_name' => 'web', 'name' => 'Update Owner', 'module_id' => $ownerModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Owner', 'module_id' => $ownerModule->id],

            ['guard_name' => 'web', 'name' => 'Create Tenant', 'module_id' => $tenantModule->id],
            ['guard_name' => 'web', 'name' => 'Show Tenant', 'module_id' => $tenantModule->id],
            ['guard_name' => 'web', 'name' => 'Update Tenant', 'module_id' => $tenantModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Tenant', 'module_id' => $tenantModule->id],

            ['guard_name' => 'web', 'name' => 'Create Rent', 'module_id' => $rentModule->id],
            ['guard_name' => 'web', 'name' => 'Show Rent', 'module_id' => $rentModule->id],
            ['guard_name' => 'web', 'name' => 'Update Rent', 'module_id' => $rentModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Rent', 'module_id' => $rentModule->id],

            ['guard_name' => 'web', 'name' => 'Create Utility Bills', 'module_id' => $utilityBillsModule->id],
            ['guard_name' => 'web', 'name' => 'Show Utility Bills', 'module_id' => $utilityBillsModule->id],
            ['guard_name' => 'web', 'name' => 'Update Utility Bills', 'module_id' => $utilityBillsModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Utility Bills', 'module_id' => $utilityBillsModule->id],

            ['guard_name' => 'web', 'name' => 'Create Common Area Bills', 'module_id' => $commonAreaBillsModule->id],
            ['guard_name' => 'web', 'name' => 'Show Common Area Bills', 'module_id' => $commonAreaBillsModule->id],
            ['guard_name' => 'web', 'name' => 'Update Common Area Bills', 'module_id' => $commonAreaBillsModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Common Area Bills', 'module_id' => $commonAreaBillsModule->id],

            ['guard_name' => 'web', 'name' => 'Create Maintenance', 'module_id' => $maintenanceModule->id],
            ['guard_name' => 'web', 'name' => 'Show Maintenance', 'module_id' => $maintenanceModule->id],
            ['guard_name' => 'web', 'name' => 'Update Maintenance', 'module_id' => $maintenanceModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Maintenance', 'module_id' => $maintenanceModule->id],

            ['guard_name' => 'web', 'name' => 'Create Amenities', 'module_id' => $amenitiesModule->id],
            ['guard_name' => 'web', 'name' => 'Show Amenities', 'module_id' => $amenitiesModule->id],
            ['guard_name' => 'web', 'name' => 'Update Amenities', 'module_id' => $amenitiesModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Amenities', 'module_id' => $amenitiesModule->id],

            ['guard_name' => 'web', 'name' => 'Create Book Amenity', 'module_id' => $bookAmenityModule->id],
            ['guard_name' => 'web', 'name' => 'Show Book Amenity', 'module_id' => $bookAmenityModule->id],
            ['guard_name' => 'web', 'name' => 'Update Book Amenity', 'module_id' => $bookAmenityModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Book Amenity', 'module_id' => $bookAmenityModule->id],

            ['guard_name' => 'web', 'name' => 'Create Visitors', 'module_id' => $visitorsModule->id],
            ['guard_name' => 'web', 'name' => 'Show Visitors', 'module_id' => $visitorsModule->id],
            ['guard_name' => 'web', 'name' => 'Update Visitors', 'module_id' => $visitorsModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Visitors', 'module_id' => $visitorsModule->id],

            ['guard_name' => 'web', 'name' => 'Create Notice Board', 'module_id' => $noticeBoardModule->id],
            ['guard_name' => 'web', 'name' => 'Show Notice Board', 'module_id' => $noticeBoardModule->id],
            ['guard_name' => 'web', 'name' => 'Update Notice Board', 'module_id' => $noticeBoardModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Notice Board', 'module_id' => $noticeBoardModule->id],

            ['guard_name' => 'web', 'name' => 'Create Tickets', 'module_id' => $ticketModule->id],
            ['guard_name' => 'web', 'name' => 'Show Tickets', 'module_id' => $ticketModule->id],
            ['guard_name' => 'web', 'name' => 'Update Tickets', 'module_id' => $ticketModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Tickets', 'module_id' => $ticketModule->id],

            ['guard_name' => 'web', 'name' => 'Create Parking', 'module_id' => $parkingModule->id],
            ['guard_name' => 'web', 'name' => 'Show Parking', 'module_id' => $parkingModule->id],
            ['guard_name' => 'web', 'name' => 'Update Parking', 'module_id' => $parkingModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Parking', 'module_id' => $parkingModule->id],

            ['guard_name' => 'web', 'name' => 'Create Service Provider', 'module_id' => $serviceProviderModule->id],
            ['guard_name' => 'web', 'name' => 'Show Service Provider', 'module_id' => $serviceProviderModule->id],
            ['guard_name' => 'web', 'name' => 'Update Service Provider', 'module_id' => $serviceProviderModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Service Provider', 'module_id' => $serviceProviderModule->id],

            ['guard_name' => 'web', 'name' => 'Create Service Time Logging', 'module_id' => $serviceTimeLoggingModule->id],
            ['guard_name' => 'web', 'name' => 'Show Service Time Logging', 'module_id' => $serviceTimeLoggingModule->id],
            ['guard_name' => 'web', 'name' => 'Update Service Time Logging', 'module_id' => $serviceTimeLoggingModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Service Time Logging', 'module_id' => $serviceTimeLoggingModule->id],

            ['guard_name' => 'web', 'name' => 'Create Assets', 'module_id' => $assetsModule->id],
            ['guard_name' => 'web', 'name' => 'Show Assets', 'module_id' => $assetsModule->id],
            ['guard_name' => 'web', 'name' => 'Update Assets', 'module_id' => $assetsModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Assets', 'module_id' => $assetsModule->id],

            ['guard_name' => 'web', 'name' => 'Manage Settings', 'module_id' => $settingsModule->id],

            ['guard_name' => 'web', 'name' => 'Create Event', 'module_id' => $eventModule->id],
            ['guard_name' => 'web', 'name' => 'Show Event', 'module_id' => $eventModule->id],
            ['guard_name' => 'web', 'name' => 'Update Event', 'module_id' => $eventModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Event', 'module_id' => $eventModule->id],

            ['guard_name' => 'web', 'name' => 'Create Forum', 'module_id' => $forumModule->id],
            ['guard_name' => 'web', 'name' => 'Show Forum', 'module_id' => $forumModule->id],
            ['guard_name' => 'web', 'name' => 'Update Forum', 'module_id' => $forumModule->id],
            ['guard_name' => 'web', 'name' => 'Delete Forum', 'module_id' => $forumModule->id],
        ];

        Permission::insert($permissions);
    }
}
