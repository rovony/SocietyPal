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
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_code', 80)->nullable();
            $table->timestamp('supported_until')->nullable();
            $table->timestamp('last_license_verified_at')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('theme_hex')->nullable();
            $table->string('theme_rgb')->nullable();
            $table->string('locale')->default('en');
            $table->string('license_type')->nullable();
            $table->boolean('hide_cron_job')->default(0);
            $table->timestamp('last_cron_run')->nullable();
            $table->boolean('system_update')->default(1);
            $table->timestamp('purchased_on')->nullable();
            $table->string('timezone')->nullable()->default('Asia/Kolkata');
            $table->boolean('show_logo_text')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_settings');
    }
};
