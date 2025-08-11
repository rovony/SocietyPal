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
            $table->boolean('disable_landing_site')->default(false);
            $table->enum('landing_site_type', ['theme', 'custom'])->default('theme');
            $table->string('landing_site_url')->nullable();
            $table->tinyText('installed_url')->nullable();
            $table->boolean('requires_approval_after_signup')->default(false);
            $table->string('facebook_link', 255)->nullable();
            $table->string('instagram_link', 255)->nullable();
            $table->string('twitter_link', 255)->nullable();
            $table->string('meta_keyword', 255)->nullable();
            $table->longText('meta_description')->nullable();
            $table->string('upload_fav_icon_android_chrome_192')->nullable();
            $table->string('upload_fav_icon_android_chrome_512')->nullable();
            $table->string('upload_fav_icon_apple_touch_icon')->nullable();
            $table->string('upload_favicon_16')->nullable();
            $table->string('upload_favicon_32')->nullable();
            $table->string('favicon')->nullable();
            $table->string('webmanifest')->nullable();
            $table->boolean('is_pwa_install_alert_show')->default(0);

        });

        Schema::table('societies', function (Blueprint $table) {
            $table->enum('approval_status', ['Pending', 'Approved', 'Rejected'])->default('Approved');
            $table->text('rejection_reason')->nullable();
            $table->string('meta_keyword', 255)->nullable();
            $table->longText('meta_description')->nullable();
            $table->string('upload_fav_icon_android_chrome_192')->nullable();
            $table->string('upload_fav_icon_android_chrome_512')->nullable();
            $table->string('upload_fav_icon_apple_touch_icon')->nullable();
            $table->string('upload_favicon_16')->nullable();
            $table->string('upload_favicon_32')->nullable();
            $table->string('favicon')->nullable();
            $table->string('webmanifest')->nullable();
            $table->boolean('is_pwa_install_alert_show')->default(0);

        });

        cache()->forget('global_setting');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn('disable_landing_site');
            $table->dropColumn('landing_site_type');
            $table->dropColumn('landing_site_url');
            $table->dropColumn(['installed_url']);
            $table->dropColumn('requires_approval_after_signup');
            $table->dropColumn(['facebook_link', 'instagram_link', 'twitter_link']);
            $table->dropColumn(['meta_keyword', 'meta_description']);
            $table->dropColumn('upload_fav_icon_android_chrome_192');
            $table->dropColumn('upload_fav_icon_android_chrome_512');
            $table->dropColumn('upload_fav_icon_apple_touch_icon');
            $table->dropColumn('upload_favicon_16');
            $table->dropColumn('upload_favicon_32');
            $table->dropColumn('favicon');
            $table->dropColumn('webmanifest');
            $table->dropColumn('is_pwa_install_alert_show');
        });

        Schema::table('societies', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'rejection_reason']);
            $table->dropColumn(['meta_keyword', 'meta_description']);
            $table->dropColumn('upload_fav_icon_android_chrome_192');
            $table->dropColumn('upload_fav_icon_android_chrome_512');
            $table->dropColumn('upload_fav_icon_apple_touch_icon');
            $table->dropColumn('upload_favicon_16');
            $table->dropColumn('upload_favicon_32');
            $table->dropColumn('favicon');
            $table->dropColumn('webmanifest');
            $table->dropColumn('is_pwa_install_alert_show');
        });
    }
};
