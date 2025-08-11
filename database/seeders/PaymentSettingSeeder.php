<?php

namespace Database\Seeders;

use App\Models\PaymentGatewayCredential;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSettingSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $setting = new PaymentGatewayCredential();
        $setting->society_id = $society->id;
        $setting->save();
    }

}
