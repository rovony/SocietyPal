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
        Schema::table('email_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('email_settings', 'email_verified')) {
                $table->boolean('email_verified')->default(0);
            }

            if (!Schema::hasColumn('email_settings', 'verified')) {
                $table->boolean('verified')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_settings', function (Blueprint $table) {
            //
        });
    }
    
};
