<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\GlobalSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            GlobalSetting::where('theme_hex', '#A78BFA')
            ->where('theme_rgb', '167, 139, 250')
            ->update([
                'theme_hex' => '#e11d48',
                'theme_rgb' => '225, 29, 72'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            GlobalSetting::where('theme_hex', '#e11d48')
            ->where('theme_rgb', '225, 29, 72')
            ->update([
                'theme_hex' => '#A78BFA',
                'theme_rgb' => '167, 139, 250'
            ]);
        });
    }
};
