<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ForumCategorySeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
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
        $categories = [];
    
        foreach ($icons as $item) {
            $svgPath = base_path("vendor/blade-ui-kit/blade-heroicons/resources/svg/c-{$item['icon']}.svg");
    
            $iconSvg = File::exists($svgPath) ? File::get($svgPath) : null;
    
            if ($iconSvg) {
                // Remove existing width/height
                $iconSvg = preg_replace('/(width|height)="\d+(\.\d+)?"/', '', $iconSvg);
                $iconSvg = preg_replace(
                    '/<svg\b/',
                    "<svg width=\"{$iconSize}\" height=\"{$iconSize}\" class=\"svg icon bi bi-qr-code-scan text-skin-base dark:text-skin-base size-6\"",
                    $iconSvg,
                    1
                );
            }
    
            $categories[] = [
                'society_id' => $society->id,
                'name' => $item['name'],
                'icon' => $item['icon'],
                'image' => $iconSvg,
            ];
        }
    
        DB::table('society_forum_category')->insert($categories);
    }
}