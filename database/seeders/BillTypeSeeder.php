<?php

namespace Database\Seeders;

use App\Models\BillType;
use Illuminate\Database\Seeder;

class BillTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     public function run($society): void
     {
         $billTypeCategories = [
             BillType::COMMON_AREA_BILL_TYPE => [
             'Electricity Bill',
              'Internet Bill',
             'Gas Bill'
             ],
            BillType::UTILITY_BILL_TYPE => [
                'Water Bill',
                'Phone Bill',
              'Maintenance Bill'
         ]
 ];
         $billTypes = [];
        foreach ($billTypeCategories as $category => $names) {
             foreach ($names as $name) {
                $billTypes[] =[
                 'name' => $name,
                 'bill_type_category' => $category,
                 'society_id' => $society->id
             ];
         }

     }

        // Use chunk insert for better performance with large datasets
        foreach (array_chunk($billTypes, 100) as $chunk) {
            BillType::insert($chunk);
        }
    }
}
