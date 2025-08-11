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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->char('countries_code', 2);
            $table->string('countries_name');
            $table->string('phonecode');

            // Indexes
            $table->primary(['id']);
            $table->index(['countries_code']);
        });

        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('currency_name');
            $table->string('currency_code');
            $table->string('currency_symbol');
        });

        Schema::create('societies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();

            $table->text('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('property_measurement')->nullable();
            $table->string('logo')->nullable();
            $table->string('fevicon')->nullable();
            $table->string('theme_rgb')->nullable();
            $table->string('theme_hex')->nullable();
            $table->string('timezone')->nullable();
            $table->text('address')->nullable();

            $table->string('key')->nullable();
            $table->unsignedBigInteger('country_id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');

            $table->unsignedBigInteger('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->enum('license_type', ['free', 'paid'])->default('free');
            $table->boolean('is_active')->default(true);
            $table->longText('about_us')->nullable();
            $table->boolean('show_logo_text')->default(true);
            $table->timestamps();
        });


        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('society_id')->nullable();
            $table->foreign('society_id')->references('id')->on('societies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
        Schema::dropIfExists('currencies');
        Schema::dropIfExists('societies');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
