<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('apartment_parking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_management_id')->constrained('apartment_managements')->onDelete('cascade');
            $table->foreignId('parking_id')->constrained('parking_managements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartment_parking');
    }
};
