<?php

namespace Database\Seeders;

use App\Models\Society;
use App\Models\VisitorTypeSettingsModel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VisitorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $visitorTypes = ['Guest', 'Service Personnel', 'Delivery Person', 'Family Members', 'Friends'];

        foreach ($visitorTypes as $type) {
            VisitorTypeSettingsModel::create([
                'name' => $type,
                'society_id' => $society->id,
            ]);
        }
    }

}
