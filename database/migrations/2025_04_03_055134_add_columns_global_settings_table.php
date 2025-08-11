<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\GlobalSetting;
use Minishlink\WebPush\VAPID;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->string('vapid_public_key')->nullable();
            $table->string('vapid_private_key')->nullable();
            $table->string('vapid_subject')->default('mailto:admin@example.com');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn(['vapid_public_key', 'vapid_private_key', 'vapid_subject']);
        });
    }
};
