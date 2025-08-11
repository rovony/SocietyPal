<?php

use App\Models\LanguageSetting;
use App\Models\Society;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('language_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('language_code');
            $table->string('language_name');
            $table->string('flag_code')->nullable();
            $table->enum('status', ['enabled', 'disabled']);
            $table->boolean('is_rtl')->default(0);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('locale')->default('en');
        });

        $society = Society::withoutGlobalScopes()->count();

        if ($society > 0) {
            LanguageSetting::insert(LanguageSetting::LANGUAGES);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['locale']);
        });

        Schema::dropIfExists('language_settings');
    }
};
