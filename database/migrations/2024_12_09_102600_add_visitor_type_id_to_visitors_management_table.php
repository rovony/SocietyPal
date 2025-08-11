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
        Schema::table('visitors_management', function (Blueprint $table) {
            $table->string('purpose_of_visit')->nullable();
            $table->foreignId('visitor_type_id')->nullable()->constrained('visitor_settings')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors_management', function (Blueprint $table) {
            //
        });
    }
};
