<?php

use App\Models\Society;
use App\Models\ModuleSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $module = 'Assets';
        $types = ['Admin', 'Manager', 'Owner', 'Tenant', 'Guard'];

        $societies = Society::all();

        foreach ($societies as $society) {
            foreach ($types as $type) {
                if (!ModuleSetting::where('society_id', $society->id)->where('module_name', $module)->where('type', $type)->exists()) {
                    ModuleSetting::create([
                        'society_id' => $society->id,
                        'module_name' => $module,
                        'status' => 'active',
                        'type' => $type,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $modules = ['Assets'];
        ModuleSetting::whereIn('module_name', $modules)->delete();
    }
};
