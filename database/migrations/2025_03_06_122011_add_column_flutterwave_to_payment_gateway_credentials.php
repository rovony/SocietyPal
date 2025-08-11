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
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            $table->boolean('flutterwave_status')->default(false);
                $table->enum('flutterwave_mode', ['sandbox', 'live'])->default('sandbox');
                $table->string('test_flutterwave_key')->nullable();
                $table->string('test_flutterwave_secret')->nullable();
                $table->string('test_flutterwave_hash')->nullable();
                $table->string('live_flutterwave_key')->nullable();
                $table->string('live_flutterwave_secret')->nullable();
                $table->string('live_flutterwave_hash')->nullable();
                $table->string('flutterwave_webhook_secret_hash')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_gateway_credentials', function (Blueprint $table) {
            //
        });
    }
};
