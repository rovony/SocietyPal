<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Society;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $floorNames = [
            'Ground Floor',
            'First Floor',
            'Second Floor',
            'Third Floor',
            'Fourth Floor',
        ];

        foreach ($society->towers as $tower) {
            // Create 8-15 floors per tower
            $numFloors = rand(2, 5);

            for ($i = 0; $i < $numFloors; $i++) {
                Floor::create([
                    'floor_name' => $floorNames[$i],
                    'tower_id' => $tower->id,
                    'society_id' => $society->id,
                ]);
            }
        }
    }
}
