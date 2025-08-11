<?php

namespace Database\Seeders;

use App\Models\ServiceClockInOut;
use App\Models\ServiceManagement;
use Illuminate\Database\Seeder;
use App\Models\ServiceType;
use App\Models\User;
use Carbon\Carbon;

class ServiceClockInOutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $serviceProviders = ServiceManagement::where('society_id', $society->id)->take(6)->get();
        $user = User::where('society_id', $society->id)->first();

        foreach ($serviceProviders as $provider) {
            $clockInDate = Carbon::now()->subMonth()->addDays(rand(0, 30))->toDateString();

            $clockInTime = Carbon::createFromTime(rand(1, 10), rand(0, 59))->format('H:i');

            $isClockedOut = rand(0, 1);

            if ($isClockedOut) {
                $clockOutTime = Carbon::createFromTime(rand(11, 20), rand(0, 59))->format('H:i');

                $durationMinutes = Carbon::parse("$clockInDate $clockInTime")
                    ->diffInMinutes(Carbon::parse("$clockInDate $clockOutTime"));
            } else {
                $clockOutTime = null;
                $durationMinutes = null;
            }

            ServiceClockInOut::create([
                'society_id' => $society->id,
                'service_management_id' => $provider->id,
                'added_by' => $user->id,
                'clock_in_date' => $clockInDate,
                'clock_in_time' => $clockInTime,
                'clock_out_date' => $isClockedOut ? $clockInDate : null,
                'clock_out_time' => $clockOutTime,
                'duration_minutes' => $durationMinutes,
                'status' => $isClockedOut ? 'clock_out' : 'clock_in',
            ]);
        }
    }
}
