<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            ['name' => 'Tower'],
            ['name' => 'Floor'],
            ['name' => 'Apartment'],
            ['name' => 'User'],
            ['name' => 'Owner'],
            ['name' => 'Tenant'],
            ['name' => 'Rent'],
            ['name' => 'Utility Bills'],
            ['name' => 'Common Area Bills'],
            ['name' => 'Maintenance'],
            ['name' => 'Amenities'],
            ['name' => 'Book Amenity'],
            ['name' => 'Visitors'],
            ['name' => 'Notice Board'],
            ['name' => 'Tickets'],
            ['name' => 'Parking'],
            ['name' => 'Service Provider'],
            ['name' => 'Service Time Logging'],
            ['name' => 'Settings'],
            ['name' => 'Assets'],
            ['name' => 'Event'],
            ['name' => 'Forum'],

        ];

        Module::insert($modules);
    }
}
