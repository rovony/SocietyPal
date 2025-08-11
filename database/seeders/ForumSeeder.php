<?php

namespace Database\Seeders;

use App\Models\Society;
use App\Models\SocietyForumCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Faker\Factory as Faker;
use App\Models\User;

class ForumSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $faker = Faker::create();
        $categoryIds = SocietyForumCategory::where('society_id', $society->id)->pluck('id');
        $user = User::where('society_id', $society->id)->first();

        $forums = [];

        for ($i = 0; $i < 5; $i++) {
            $forums[] = [
                'society_id'         => $society->id,
                'category_id'        => $faker->randomElement($categoryIds),
                'title'              => $faker->sentence(6, true),
                'description'        => $faker->paragraphs(2, true),
                'discussion_type'    => 'public',
                'user_selection_type'=> null,
                'created_by'         => $user->id,
                'date'               => $faker->date(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
        }

        DB::table('forums')->insert($forums);
    }
}