<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\EventRole;
use App\Models\User;
use Faker\Factory as Faker;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param \App\Models\Society $society
     */
    public function run($society)
    {
        $faker = Faker::create();

        // Get all users belonging to the society
        $users = User::where('society_id', $society->id)->pluck('id')->toArray();

        if (empty($users)) {
            // Exit if no users found for the society
            return;
        }

        // Create 5 events
        for ($i = 0; $i < 5; $i++) {
            $start = $faker->dateTimeBetween('now', '+1 month');
            $end = (clone $start)->modify('+2 hours');

            $event = Event::create([
                'society_id'      => $society->id,
                'event_name'      => $faker->sentence(3),
                'where'           => $faker->address,
                'description'     => $faker->paragraph,
                'start_date_time' => $start,
                'end_date_time'   => $end,
                'status'          => $faker->randomElement(['pending', 'completed', 'cancelled']),
                'assign_to'       => 'user',
            ]);

            // Pick up to 3 random users from the same society
            $attendees = collect($users)->shuffle()->take(3);

            foreach ($attendees as $userId) {
                EventAttendee::create([
                    'user_id'  => $userId,
                    'event_id' => $event->id,
                ]);
            }
        }
    }
}