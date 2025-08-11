<?php

namespace Database\Seeders;

use App\Models\Society;
use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CurrencySeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $currencies = [
            ['currency_name' => 'Rupee', 'currency_symbol' => '₹', 'currency_code' => 'INR'],
            ['currency_name' => 'Dollars', 'currency_symbol' => '$', 'currency_code' => 'USD'],
            ['currency_name' => 'Pounds', 'currency_symbol' => '£', 'currency_code' => 'GBP'],
            ['currency_name' => 'Euros', 'currency_symbol' => '€', 'currency_code' => 'EUR'],
        ];

        foreach ($currencies as $currency) {
            Currency::create([
                'currency_name' => $currency['currency_name'],
                'currency_symbol' => $currency['currency_symbol'],
                'currency_code' => $currency['currency_code'],
                'society_id' => $society->id,
            ]);
        }
        
    }


}
