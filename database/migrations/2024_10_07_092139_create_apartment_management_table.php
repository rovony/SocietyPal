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
        Schema::create('apartment_managements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('society_id')->nullable();
            $table->foreign('society_id')->references('id')->on('societies')->onDelete('cascade')->onUpdate('cascade');
            $table->string('apartment_number');
            $table->integer('apartment_area');
            $table->string('apartment_area_unit');
            $table->foreignId('floor_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('tower_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['not_sold', 'occupied','available_for_rent','rented'])->default('not_sold');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartment_managements');
    }
};
