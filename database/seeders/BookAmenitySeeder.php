<?php

namespace Database\Seeders;

use App\Models\Amenities;
use App\Models\BookAmenity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookAmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $amenity1 = Amenities::where('amenities_name', 'Swimming Pool')->where('society_id', $society->id)->first();
        $amenity2 = Amenities::where('amenities_name', 'Gym')->where('society_id', $society->id)->first();
        $amenity3 = Amenities::where('amenities_name', 'Tennis Court')->where('society_id', $society->id)->first();

        $user1 = User::where('society_id', $society->id)->first();
        $user2 = User::where('society_id', $society->id)->skip(1)->first();
        $user3 = User::where('society_id', $society->id)->skip(2)->first();

        if ($amenity1 && $user1) {
            BookAmenity::create([
                'society_id' => $society->id,
                'amenity_id' => $amenity1->id,
                'booked_by' => $user1->id,
                'booking_date' => Carbon::today()->toDateString(),
                'booking_time' => '10:00:00',
                'persons' => 2,
                'booking_type' => 'single',
                'unique_id' => uniqid(),
            ]);
        }

        if ($amenity2 && $user2) {
            BookAmenity::create([
                'society_id' => $society->id,
                'amenity_id' => $amenity2->id,
                'booked_by' => $user2->id,
                'booking_date' => Carbon::today()->toDateString(),
                'booking_time' => '08:00:00',
                'persons' => 1,
                'booking_type' => 'single',
                'unique_id' => uniqid(),
            ]);
        }

        if ($amenity3 && $user3) {
            BookAmenity::create([
                'society_id' => $society->id,
                'amenity_id' => $amenity3->id,
                'booked_by' => $user3->id,
                'booking_date' => Carbon::today()->toDateString(),
                'booking_time' => '11:00:00',
                'persons' => 2,
                'booking_type' => 'single',
                'unique_id' => uniqid(),
            ]);
        }
    }
}
