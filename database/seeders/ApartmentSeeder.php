<?php

namespace Database\Seeders;

use App\Models\Society;
use App\Models\Apartment;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ApartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run($society): void
    {
        $apartmentTypes = [
            ['apartment_type' => '1 BHK', 'maintenance_value' => 100],
            ['apartment_type' => '2 BHK', 'maintenance_value' => 200],
            ['apartment_type' => '3 BHK', 'maintenance_value' => 300],
            ['apartment_type' => '4 BHK', 'maintenance_value' => 400],
            ['apartment_type' => '5 BHK', 'maintenance_value' => 500],
        ];

            foreach ($apartmentTypes as $type) {
                Apartment::create([
                    'apartment_type' => $type['apartment_type'],
                    'maintenance_value' => $type['maintenance_value'],
                    'society_id' => $society->id,
                ]);
            }

        }
}
