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
        Schema::create('maintenance_apartment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_management_id')->constrained('maintenance_management')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('apartment_management_id')->constrained('apartment_managements')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('cost', 10, 2);
            $table->date('payment_date')->nullable();
            $table->string('payment_proof')->nullable();
            $table->enum('paid_status', ['paid', 'unpaid'])->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */ 
    public function down(): void
    {
        Schema::dropIfExists('maintenance_apartment');
    }
};
