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
        Schema::table('bill_types', function (Blueprint $table) {
            $table->unsignedBigInteger('society_id')->nullable();
            $table->foreign('society_id')->references('id')->on('societies')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_types', function (Blueprint $table) {
            //
        });
    }
};
