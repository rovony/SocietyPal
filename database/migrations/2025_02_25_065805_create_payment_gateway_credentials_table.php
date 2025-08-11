<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Society;
use App\Models\PaymentGatewayCredential;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_gateway_credentials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('society_id')->nullable();
            $table->foreign('society_id')->references('id')->on('societies')->onDelete('cascade')->onUpdate('cascade');
            $table->text('razorpay_key')->nullable();
            $table->text('razorpay_secret')->nullable();
            $table->text('razorpay_webhook_key')->nullable();
            $table->boolean('razorpay_status')->default(false);

            $table->text('stripe_key')->nullable();
            $table->text('stripe_secret')->nullable();
            $table->text('stripe_webhook_key')->nullable();
            $table->boolean('stripe_status')->default(false);
            $table->boolean('is_offline_payment_enabled')->default(false);
            $table->string('offline_payment_detail')->nullable();
            $table->boolean('is_cash_payment_enabled')->default(false);

            $table->timestamps();
        });

        $societies = Society::all();

        foreach ($societies as $society) {
            $exists = PaymentGatewayCredential::where('society_id', $society->id)->exists();

            if (!$exists) {
                $setting = new PaymentGatewayCredential();
                $setting->society_id = $society->id;
                $setting->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_credentials');
    }
};
