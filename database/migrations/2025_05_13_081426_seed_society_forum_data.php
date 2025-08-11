<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Faker\Factory as Faker;
use App\Models\Society;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('societies') || !Schema::hasTable('users') || !Schema::hasTable('forums') || !Schema::hasTable('society_forum_category')) {
            return;
        }

        $icons = [
            ['name' => 'Culture & Traditions', 'icon' => 'globe-alt'],
            ['name' => 'Social Issues', 'icon' => 'exclamation-triangle'],
            ['name' => 'Politics & Governance', 'icon' => 'building-library'],
            ['name' => 'Laws & Justice', 'icon' => 'scale'],
            ['name' => 'Healthcare & Public Health', 'icon' => 'heart'],
            ['name' => 'Family & Relationships', 'icon' => 'users'],
            ['name' => 'Ethics & Morality', 'icon' => 'question-mark-circle'],
            ['name' => 'Technology & Society', 'icon' => 'cpu-chip'],
        ];

        $iconSize = 30;
        $faker = Faker::create();

        $societies = Society::all();

        foreach ($societies as $society) {
            // 1. Seed forum categories
            $categoryIds = [];

            foreach ($icons as $item) {
                $svgPath = base_path("vendor/blade-ui-kit/blade-heroicons/resources/svg/c-{$item['icon']}.svg");
                $iconSvg = File::exists($svgPath) ? File::get($svgPath) : null;

                if ($iconSvg) {
                    $iconSvg = preg_replace('/(width|height)="\d+(\.\d+)?"/', '', $iconSvg);
                    $iconSvg = preg_replace(
                        '/<svg\b/',
                        "<svg width=\"{$iconSize}\" height=\"{$iconSize}\" class=\"svg icon bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6\"",
                        $iconSvg,
                        1
                    );
                }

                $categoryId = DB::table('society_forum_category')->insertGetId([
                    'society_id' => $society->id,
                    'name'       => $item['name'],
                    'icon'       => $item['icon'],
                    'image'      => $iconSvg,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $categoryIds[] = $categoryId;
            }

            // 2. Seed forums (only if a user exists)
            $user = User::where('society_id', $society->id)->first();
            if (!$user) continue;

            $forums = [];
            for ($i = 0; $i < 5; $i++) {
                $forums[] = [
                    'society_id'          => $society->id,
                    'category_id'         => $faker->randomElement($categoryIds),
                    'title'               => $faker->sentence(6),
                    'description'         => $faker->paragraphs(2, true),
                    'discussion_type'     => 'public',
                    'user_selection_type' => null,
                    'created_by'          => $user->id,
                    'date'                => $faker->date(),
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ];
            }

            DB::table('forums')->insert($forums);
        }
    }

    public function down(): void
    {
        DB::table('forums')->truncate();
        DB::table('society_forum_category')->truncate();
    }
};
