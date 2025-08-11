<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('language_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('language_settings', 'active')) {
                $table->boolean('active')->default(1);
            }

            if (!Schema::hasColumn('language_settings', 'is_rtl')) {
                $table->boolean('is_rtl')->default(0);
            }
        });

         // Convert existing status values into active (1 = enabled, 0 = disabled)
         DB::table('language_settings')->where('status', 'enabled')->update(['active' => 1]);
         DB::table('language_settings')->where('status', 'disabled')->update(['active' => 0]);

         // Drop the old status column
         Schema::table('language_settings', function (Blueprint $table) {
             $table->dropColumn('status');
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('language_settings', function (Blueprint $table) {
            $table->enum('status', ['enabled', 'disabled'])->default('disabled');
        });

        // Restore the old values
        DB::table('language_settings')->where('active', 1)->update(['status' => 'enabled']);
        DB::table('language_settings')->where('active', 0)->update(['status' => 'disabled']);

        Schema::table('language_settings', function (Blueprint $table) {
            $table->dropColumn(['active', 'is_rtl']);
        });
    }

};
