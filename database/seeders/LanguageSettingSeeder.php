<?php

namespace Database\Seeders;

use App\Models\LanguageSetting;
use Illuminate\Database\Seeder;
use App\Observers\LanguageSettingObserver;

class LanguageSettingSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LanguageSetting::insert(LanguageSetting::LANGUAGES);

        new LanguageSettingObserver();
    }
}
