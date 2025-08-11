<?php

namespace Database\Seeders;

use App\Models\GlobalCurrency;
use App\Models\GlobalSetting;
use App\Models\StorageSetting;
use Illuminate\Database\Seeder;

class GlobalSettingSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = new GlobalSetting();
        $setting->name = 'SocietyPro';
        $setting->theme_hex = '#e11d48';
        $setting->theme_rgb = '225, 29, 72';
        $setting->hash = md5(microtime());
        $setting->installed_url = config('app.url');
        $setting->facebook_link = 'https://www.facebook.com/';
        $setting->instagram_link = 'https://www.instagram.com/';
        $setting->twitter_link = 'https://www.twitter.com/';
        $setting->default_currency_id = GlobalCurrency::first()->id;
        $setting->save();

        StorageSetting::firstOrCreate([
            'filesystem' => 'local',
            'status' => 'enabled',
        ]);
    }

}
