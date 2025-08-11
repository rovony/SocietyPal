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
        Schema::create('razorpay_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenance_apartment_id');
            $table->foreign('maintenance_apartment_id')->references('id')->on('maintenance_apartment')->onDelete('cascade');

            $table->dateTime('payment_date')->nullable();
            $table->double('amount', 16, 2)->nullable();
            $table->enum('payment_status', ['pending', 'requested', 'failed', 'completed'])->default('pending');
            $table->text('payment_error_response')->nullable();

            $table->string('razorpay_order_id')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            $table->timestamps();
        });


        Schema::create('stripe_payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('maintenance_apartment_id');
            $table->foreign('maintenance_apartment_id')->references('id')->on('maintenance_apartment')->onDelete('cascade');

            $table->dateTime('payment_date')->nullable();
            $table->double('amount', 16, 2)->nullable();
            $table->enum('payment_status', ['pending', 'requested', 'failed', 'completed'])->default('pending');
            $table->text('payment_error_response')->nullable();

            $table->string('stripe_payment_intent')->nullable();

            $table->text('stripe_session_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('razorpay_payments');
        Schema::dropIfExists('stripe_payments');

    }
};
