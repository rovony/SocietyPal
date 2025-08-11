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
        Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
            $table->enum('flutterwave_type', ['test', 'live'])->default('test');

            $table->text('test_flutterwave_key')->nullable();
            $table->text('test_flutterwave_secret')->nullable();
            $table->text('test_flutterwave_hash')->nullable();
            $table->text('flutterwave_test_webhook_key')->nullable();

            $table->text('live_flutterwave_key')->nullable();
            $table->text('live_flutterwave_secret')->nullable();
            $table->text('live_flutterwave_hash')->nullable();
            $table->text('flutterwave_live_webhook_key')->nullable();

            $table->boolean('flutterwave_status')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('superadmin_payment_gateways', function (Blueprint $table) {
            //
        });
    }
};
