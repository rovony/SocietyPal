<?php

namespace Database\Seeders;

use App\Models\BillType;
use App\Models\CommonAreaBills;
use App\Models\Society;
use Illuminate\Database\Seeder;

class CommonAreaBillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run($society): void
    {
        $billTypeId = BillType::where('society_id', $society->id)
            ->where('bill_type_category', BillType::COMMON_AREA_BILL_TYPE)
            ->pluck('id')
            ->first();

        if (!$billTypeId) {
            $this->command->error('No common area bill type found for society ' . $society->id);
            return;
        }

        for ($i = 0; $i < 5; $i++) {
            CommonAreaBills::create([
                'bill_type_id' => $billTypeId,
                'bill_date' => now()->subDays(rand(0, 365))->format('Y-m-d'),
                'status' => 'unpaid',
                'bill_amount' => rand(100, 1000),
                'society_id' => $society->id,
            ]);
        }

    }
}
