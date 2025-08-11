<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VisitorManagement;
use App\Models\ApartmentManagement;
use App\Models\VisitorTypeSettingsModel;
use Carbon\Carbon;
use Faker\Factory as Faker;

class VisitorManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $faker = Faker::create();
        $apartments = ApartmentManagement::where('society_id', $society->id)->get();
        $visitorType = VisitorTypeSettingsModel::where('society_id', $society->id)->get();
        $apartments = ApartmentManagement::where('society_id', $society->id)->get();
        $visitorType = VisitorTypeSettingsModel::where('society_id', $society->id)->get();

        $purpose = ['Guest Visit', 'Package Delivery', 'Food Delivery', 'Service Person', 'Family Visit', 'Other'];

        for ($i = 0; $i < 5; $i++) {
            $daysBeforeToday = rand(1, 2);
            $dateOfVisit = Carbon::today()->subDays($daysBeforeToday);
            $dateOfExit = $dateOfVisit->copy()->addDays(rand(0, 1));
            $inTime = Carbon::createFromFormat('H:i', $faker->time('H:i'));
            $outTime = $inTime->copy()->addHours(rand(1, 3))->format('H:i');

            VisitorManagement::create([
                'visitor_name' => $faker->name(),
                'phone_number' => $faker->phoneNumber(),
                'address' => $faker->address(),
                'apartment_id' => $apartments->random()->id ?? null,
                'visitor_type_id' => $visitorType->random()->id ?? null,
                'date_of_visit' => $dateOfVisit->toDateString(),
                'date_of_exit' => $dateOfExit,
                'purpose_of_visit' => $faker->randomElement($purpose),
                'in_time' => $inTime->format('H:i'),
                'out_time' => $outTime,
                'user_id' => $faker->randomElement([1]),
                'society_id' => $society->id,
            ]);
        }
    }
}
