<?php

use App\Models\Society;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasColumn('societies', 'hash')) {
            Schema::table('societies', function (Blueprint $table) {
                $table->string('hash')->after('name')->default(md5(microtime(true)));
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('societies', 'hash')) {
            Schema::table('societies', function (Blueprint $table) {
                $table->dropColumn('hash');
            });
        }
    }
};
