<?php

namespace Database\Seeders;

use App\Models\ApartmentManagement;
use App\Models\BillType;
use App\Models\Society;
use Illuminate\Database\Seeder;
use App\Models\UtilityBillManagement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UtilityBillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $billTypeId = BillType::where('society_id', $society->id)->where('bill_type_category', 'Utility Bill Type')->pluck('id')->first();
        $apartmentIds = ApartmentManagement::where('society_id', $society->id)->pluck('id')->toArray();

        for ($i = 0; $i < 5; $i++) {
            UtilityBillManagement::create([
                'apartment_id' => $apartmentIds[array_rand($apartmentIds)],
                'bill_type_id' => $billTypeId,
                'bill_date' => now()->subDays(rand(1, 30))->toDateString(),
                'status' => 'unpaid',
                'bill_amount' => rand(50, 500) + (rand(0, 99) / 100),
                'society_id' => $society->id,
            ]);
        }
    }
}
