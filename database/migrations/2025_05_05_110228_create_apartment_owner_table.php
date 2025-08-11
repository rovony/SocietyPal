<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apartment_owner', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartment_managements')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
        DB::table('apartment_managements')
            ->whereNotNull('user_id')
            ->get()
            ->each(function ($apartment) {
                DB::table('apartment_owner')->updateOrInsert([
                    'apartment_id' => $apartment->id,
                    'user_id' => $apartment->user_id,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartment_owner');
    }
};
