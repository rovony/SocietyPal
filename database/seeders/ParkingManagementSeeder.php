<?php

namespace Database\Seeders;

use App\Models\Society;
use Illuminate\Database\Seeder;
use App\Models\ParkingManagementSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ParkingManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        for ($i = 1; $i < 5; $i++) {
            $parkingManagementSetting = new ParkingManagementSetting();
            $parkingManagementSetting->parking_code = str_pad($i, 3, '0', STR_PAD_LEFT);
            $parkingManagementSetting->society_id = $society->id;
            $parkingManagementSetting->status = 'not_available';
            $parkingManagementSetting->save();
        }
    }
}
