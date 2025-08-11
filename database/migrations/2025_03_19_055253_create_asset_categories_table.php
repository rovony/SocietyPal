<?php

use App\Models\Society;
use App\Models\AssetsCategory;
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
        if (Schema::hasTable('asset_categories')) {
            $existingCategories = AssetsCategory::whereIn('name', ['Electronic', 'Appliances', 'Furniture', 'Vehicles', 'Stationery'])->exists();

            if ($existingCategories) {
                return;
            }
        }

        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('society_id')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        $categories = ['Electronic', 'Appliances', 'Furniture', 'Vehicles', 'Stationery'];

        $societies = Society::all();

        foreach ($societies as $society) {
            foreach ($categories as $category) {
                $assetCategory = new AssetsCategory();
                $assetCategory->name = $category;
                $assetCategory->society_id = $society->id;
                $assetCategory->save();

            }
        }
    }


    public function down(): void
    {
        Schema::dropIfExists('asset_categories');
    }
};
