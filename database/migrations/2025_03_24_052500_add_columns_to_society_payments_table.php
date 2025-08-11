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
        Schema::table('society_payments', function (Blueprint $table) {
            $table->string('flutterwave_transaction_id')->nullable();
            $table->string('flutterwave_payment_ref')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('society_payments', function (Blueprint $table) {
            //
        });
    }
};
