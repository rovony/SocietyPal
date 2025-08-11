<?php

namespace Database\Seeders;

use App\Models\Tower;
use App\Models\Society;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run($society): void
    {
        $towerNames = [
            'Evergreen Heights',
            'Sunset View',
            'Ocean Breeze',
            'Mountain Vista',
            'Palm Court',
            'Royal Gardens',
            'Crystal Towers',
            'Golden Gate'
        ];

        foreach (array_slice($towerNames, 0, 5) as $towerName) {
            Tower::create([
                'tower_name' => $towerName,
                'society_id' => $society->id,
            ]);
        }
    }
}
