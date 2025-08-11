<?php

namespace Database\Seeders;

use App\Models\ServiceManagement;
use Illuminate\Database\Seeder;
use App\Models\ServiceType;

class ServiceManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $serviceTypes = ServiceType::where('society_id', $society->id)->get();

        foreach ($serviceTypes as $serviceType) {
            for ($i = 0; $i < 5; $i++) {
                ServiceManagement::create([
                    'company_name' => null,
                    'daily_help' => 1,
                    'contact_person_name' => fake()->name(),
                    'phone_number' => fake()->phoneNumber(),
                    'website_link' => fake()->url(),
                    'price' => fake()->randomFloat(2, 100, 10000),
                    'description' => fake()->paragraph(),
                    'status' => 'available',
                    'payment_frequency' => fake()->randomElement(['per_visit', 'per_hour', 'per_day', 'per_week', 'per_month', 'per_year']),
                    'service_type_id' => $serviceType->id,
                    'society_id' => $society->id,
                ]);
            }
        }
    }
}
