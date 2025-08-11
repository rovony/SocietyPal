<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Floor;
use App\Models\AssetManagement;
use Illuminate\Database\Seeder;
use App\Models\Tower;
use App\Models\Apartment;
use App\Models\ApartmentManagement;
use App\Models\AssetsCategory;

class AssetManagementSeeder extends Seeder
{
    public function run($society)
    {
        $apartments = ApartmentManagement::where('society_id', $society->id)->get();
        $categoryIds = AssetsCategory::where('society_id', $society->id)->pluck('id')->toArray();

        $conditions = ['New', 'Good', 'Needs Repair'];
        $names = ['TV', 'Refrigerator', 'AC', 'Washing Machine', 'Fan'];
        $locations = ['Main Office', 'Conference Room', 'Reception'];

        if ($apartments->isEmpty() || empty($categoryIds)) {
            return;
        }

        for ($i = 0; $i < 5; $i++) {
            $apartment = $apartments->random();

            AssetManagement::create([
                'name' => fake()->randomElement($names),
                'society_id' => $society->id,
                'category_id' => fake()->randomElement($categoryIds),
                'location' => fake()->randomElement($locations),
                'condition' => fake()->randomElement($conditions),
                'tower_id' => $apartment->tower_id,
                'floor_id' => $apartment->floor_id,
                'apartment_id' => $apartment->id,
                'file_path' => null,
                'purchase_date' => Carbon::now()->subDays(rand(0, 1000))->format('Y-m-d'),
            ]);
        }
    }
}


