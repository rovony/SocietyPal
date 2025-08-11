<?php

namespace Database\Seeders;

use App\Models\Rent;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RentSeeder extends Seeder
{
    public function run($society)
    {
        $faker = Faker::create();

        $tenantApartments = Tenant::join('users', 'tenants.user_id', '=', 'users.id')
            ->where('users.society_id', $society->id)
            ->join('apartment_tenant', 'tenants.id', '=', 'apartment_tenant.tenant_id')
            ->join('apartment_managements', 'apartment_tenant.apartment_id', '=', 'apartment_managements.id')
            ->where('apartment_managements.status', 'rented')
            ->select('tenants.id as tenant_id', 'apartment_managements.id as apartment_id')
            ->get();

        for ($i = 0; $i < 5; $i++) {
            $tenantApartment = $faker->randomElement($tenantApartments->toArray());

            $status = $faker->randomElement(['paid', 'unpaid']);

            Rent::create([
                'society_id' => $society->id,
                'tenant_id' => $tenantApartment['tenant_id'],
                'apartment_id' => $tenantApartment['apartment_id'],
                'rent_for_year' => $faker->numberBetween(now()->year - 5, now()->year),
                'rent_for_month' => strtolower($faker->monthName()),
                'rent_amount' => $faker->randomFloat(2, 500, 5000),
                'status' => $status,
                'payment_date' => $status === 'paid' ? $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d') : null,
                'payment_proof' => $status === 'paid' ? $faker->optional(0.3)->imageUrl(640, 480, 'finance', true, 'Proof') : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
