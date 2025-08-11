<?php

namespace Database\Seeders;

use App\Models\OnboardingStep;
use Illuminate\Database\Seeder;

class OnboardingSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        OnboardingStep::create(['society_id' => $society->id]);
    }
}
