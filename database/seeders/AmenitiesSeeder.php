<?php

namespace Database\Seeders;

use App\Models\Amenities;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmenitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $amenitiesData = [
            [
                'amenities_name' => 'Swimming Pool',
                'status' => 'available',
                'booking_status' => 1,
                'start_time' => '08:00:00',
                'end_time' => '20:00:00',
                'slot_time' => 40,
                'multiple_booking_status' => 1,
                'number_of_person' => 2
            ],
            [
                'amenities_name' => 'Gym',
                'status' => 'available',
                'booking_status' => 1,
                'start_time' => '06:00:00',
                'end_time' => '22:00:00',
                'slot_time' => 20,
                'multiple_booking_status' => 1,
                'number_of_person' => 3
            ],
            [
                'amenities_name' => 'Tennis Court',
                'status' => 'available',
                'booking_status' => 1,
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'slot_time' => 15,
                'multiple_booking_status' => 1,
                'number_of_person' => 4
            ],
            [
                'amenities_name' => 'Basketball Court',
                'status' => 'available',
                'booking_status' => 1,
                'start_time' => '07:00:00',
                'end_time' => '19:00:00',
                'slot_time' => 30,
                'multiple_booking_status' => 1,
                'number_of_person' => 5
            ],
            [
                'amenities_name' => 'Club House',
                'status' => 'available',
                'booking_status' => 1,
                'start_time' => '10:00:00',
                'end_time' => '23:00:00',
                'slot_time' => 60,
                'multiple_booking_status' => 1,
                'number_of_person' => 10
            ]
        ];

        foreach ($amenitiesData as $data) {
            $amenities = new Amenities();
            foreach ($data as $key => $value) {
                $amenities->$key = $value;
            }
            $amenities->society_id = $society->id;
            $amenities->saveQuietly();
        }
    }
}
