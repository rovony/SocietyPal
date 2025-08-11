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
        Schema::table('global_settings', function (Blueprint $table) {
            Schema::table('global_settings', function (Blueprint $table) {
                $table->string('hash')->nullable()->after('name');
                $table->unsignedBigInteger('default_currency_id')->nullable()->after('hash');
                $table->foreign('default_currency_id')->references('id')->on('global_currencies')->onDelete('cascade')->onUpdate('cascade');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropForeign(['default_currency_id']);
            $table->dropColumn(['hash', 'default_currency_id']);
        });
    }
};
