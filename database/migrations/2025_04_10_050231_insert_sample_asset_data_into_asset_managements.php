<?php

use Carbon\Carbon;
use App\Models\Floor;
use App\Models\Tower;
use App\Models\Society;
use App\Models\AssetsCategory;
use App\Models\AssetManagement;
use App\Models\ApartmentManagement;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $societies = Society::all();

        foreach ($societies as $society) {
            $apartments = ApartmentManagement::where('society_id', $society->id)->get();
            $categoryIds = AssetsCategory::where('society_id', $society->id)->pluck('id')->toArray();

            $conditions = ['New', 'Good', 'Needs Repair'];
            $names = ['TV', 'Refrigerator', 'AC'];
            $locations = ['Main Office', 'Conference Room', 'Reception'];

            if ($apartments->isEmpty() || empty($categoryIds)) {
                continue;
            }

            for ($i = 0; $i < 5; $i++) {
                $randomApartment = $apartments->random();

                AssetManagement::insert([
                    'name' => $names[array_rand($names)],
                    'society_id' => $society->id,
                    'category_id' => $categoryIds[array_rand($categoryIds)],
                    'location' => $locations[array_rand($locations)],
                    'condition' => $conditions[array_rand($conditions)],
                    'tower_id' => $randomApartment->tower_id,
                    'floor_id' => $randomApartment->floor_id,
                    'apartment_id' => $randomApartment->id,
                    'file_path' => null,
                    'purchase_date' => Carbon::now()->subDays(rand(0, 1000))->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

};
