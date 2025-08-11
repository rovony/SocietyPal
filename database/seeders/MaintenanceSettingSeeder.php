<?php

namespace Database\Seeders;

use App\Models\Society;
use App\Models\Maintenance;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MaintenanceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run($society): void
    {
        Maintenance::create([
            'set_value' => rand(500, 5000),
            'unit_name' => 'sqft',
            'society_id' => $society->id,
        ]);

    }
}
