<?php

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
        Schema::table('global_settings', function (Blueprint $table) {
            $table->enum('google_recaptcha_status', ['active', 'deactive'])->default('deactive');
            $table->enum('google_recaptcha_v3_status', ['active', 'deactive'])->default('deactive');
            $table->string('google_recaptcha_v3_site_key')->nullable();
            $table->string('google_recaptcha_v3_secret_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            //
        });
    }
};
