<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $tables = ['users', 'visitors_management', 'service_management', 'societies'];

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'country_phonecode')) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                    // Add after a column that exists or just at the end
                    $tableBlueprint->integer('country_phonecode')->nullable();
                });
            }
        }

        // Update phonecode in users table by joining with societies table
        if (Schema::hasColumn('users', 'country_phonecode')) {
            DB::statement("
                UPDATE users
                JOIN societies ON societies.id = users.society_id
                JOIN countries ON countries.id = societies.country_id
                SET users.country_phonecode = countries.phonecode
                WHERE societies.country_id IS NOT NULL
            ");
        }

        // Update phonecode in visitors_management
        if (Schema::hasColumn('visitors_management', 'country_phonecode')) {
            DB::statement("
                UPDATE visitors_management
                JOIN societies ON societies.id = visitors_management.society_id
                JOIN countries ON countries.id = societies.country_id
                SET visitors_management.country_phonecode = countries.phonecode
                WHERE societies.country_id IS NOT NULL
            ");
        }

        // Update phonecode in service_management
        if (Schema::hasColumn('service_management', 'country_phonecode')) {
            DB::statement("
                UPDATE service_management
                JOIN societies ON societies.id = service_management.society_id
                JOIN countries ON countries.id = societies.country_id
                SET service_management.country_phonecode = countries.phonecode
                WHERE societies.country_id IS NOT NULL
            ");
        }

        // Update phonecode in societies directly
        if (Schema::hasColumn('societies', 'country_phonecode')) {
            DB::statement("
                UPDATE societies
                JOIN countries ON countries.id = societies.country_id
                SET societies.country_phonecode = countries.phonecode
                WHERE societies.country_id IS NOT NULL
            ");
        }
    }

    public function down()
    {
        $tables = ['users', 'visitors_management', 'service_management', 'societies'];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'country_phonecode')) {
                Schema::table($table, function (Blueprint $tableBlueprint) {
                    $tableBlueprint->dropColumn('country_phonecode');
                });
            }
        }
    }
};

