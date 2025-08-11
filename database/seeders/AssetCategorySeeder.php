<?php

namespace Database\Seeders;

use App\Models\Society;
use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AssetsCategory;

class AssetCategorySeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $categories = ['Electronic', 'Appliances', 'Furniture', 'Vehicles', 'Stationery'];


        foreach ($categories as $category) {
            AssetsCategory::firstOrCreate([
                'society_id' => $society->id,
                'name' => $category,
            ]);
        }
    }


}
