<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PaymentGatewayCredential;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        PaymentGatewayCredential::whereNull('is_cash_payment_enabled')
            ->orWhere('is_cash_payment_enabled', false)
            ->update(['is_cash_payment_enabled' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        PaymentGatewayCredential::where('is_cash_payment_enabled', true)
            ->update(['is_cash_payment_enabled' => false]);
    }
};
