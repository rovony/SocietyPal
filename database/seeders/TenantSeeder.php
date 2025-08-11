<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\ApartmentManagement;
use App\Models\ApartmentTenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantSeeder extends Seeder
{
    public function run($society)
    {
        // Get all required data upfront to avoid repeated queries
        $tenantRole = Role::where('name', 'Tenant_' . $society->id)->first();
        $id = $tenantRole->id . '_' . $society->id;

        // Get all rented apartments in one query
        $availableApartments = ApartmentManagement::where('society_id', $society->id)
            ->where('status', 'rented')
            ->whereNotIn('id', ApartmentTenant::pluck('apartment_id'))
            ->get();

        if ($availableApartments->isEmpty()) {
            $this->command->error('No available apartments found for tenants');
            return;
        }

        $tenants = [
            [
                'name' => 'Tenant',
                'email' => 'tenant@example.com',
                'phone' => '(555) 123-4567',
                'rent' => 2500,
                'cycle' => 'monthly'
            ],
            [
                'name' => 'Tenant',
                'email' => 'tenant.' . $id . '@example.com',
                'phone' => '(555) 123-4567',
                'rent' => 2500,
                'cycle' => 'monthly'
            ],
            [
                'name' => 'John Smith',
                'email' => 'john.smith.' . $id . '@example.com',
                'phone' => '(555) 123-4567',
                'rent' => 2500,
                'cycle' => 'monthly'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j.' . $id . '@example.com',
                'phone' => '(555) 234-5678',
                'rent' => 3000,
                'cycle' => 'monthly'
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'mchen.' . $id . '@example.com',
                'phone' => '(555) 345-6789',
                'rent' => 2800,
                'cycle' => 'monthly'
            ],
            [
                'name' => 'Emily Wilson',
                'email' => 'e.wilson.' . $id . '@example.com',
                'phone' => '(555) 456-7890',
                'rent' => 30000,
                'cycle' => 'annually'
            ],
            [
                'name' => 'David Miller',
                'email' => 'd.miller.' . $id . '@example.com',
                'phone' => '(555) 567-8901',
                'rent' => 2700,
                'cycle' => 'monthly'
            ],
            [
                'name' => 'Jessica Brown',
                'email' => 'jbrown.' . $id . '@example.com',
                'phone' => '(555) 678-9012',
                'rent' => 32000,
                'cycle' => 'annually'
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'rtaylor.' . $id . '@example.com',
                'phone' => '(555) 789-0123',
                'rent' => 2900,
                'cycle' => 'monthly'
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'l.anderson.' . $id . '@example.com',
                'phone' => '(555) 890-1234',
                'rent' => 3200,
                'cycle' => 'monthly'
            ],
            [
                'name' => 'Thomas Wilson',
                'email' => 't.wilson.' . $id . '@example.com',
                'phone' => '(555) 901-2345',
                'rent' => 3100,
                'cycle' => 'monthly'
            ],
            [
                'name' => 'Jennifer Lee',
                'email' => 'j.lee.' . $id . '@example.com',
                'phone' => '(555) 012-3456',
                'rent' => 35000,
                'cycle' => 'annually'
            ]
        ];

        $this->command->info('Creating tenants for society ' . $society->id);
        $bar = $this->command->getOutput()->createProgressBar(count($tenants));
        $bar->start();

        DB::beginTransaction();
        try {
            foreach ($tenants as $index => $tenant) {

                if ($society->id !== 1 && $tenant['name'] === 'Tenant') {
                    continue;
                }

                // Create user
                $user = User::create([
                    'society_id' => $society->id,
                    'name' => $tenant['name'],
                    'email' => $tenant['email'],
                    'phone_number' => $tenant['phone'],
                    'role_id' => $tenantRole->id,
                    'tenant' => true,
                    'status' => 'active',
                    'password' => bcrypt('123456'),
                ]);

                $user->assignRole($tenantRole);
                $this->command->info("\nCreated user: " . $tenant['name']);

                $tenantRecord = $user->tenant()->create([
                    'society_id' => $society->id,
                ]);

                $this->command->info("Created tenant record for: " . $tenant['name']);

                // Get next available apartment from collection
                if ($apartment = $availableApartments->shift()) {
                    ApartmentTenant::create([
                        'tenant_id' => $tenantRecord->id,
                        'apartment_id' => $apartment->id,
                        'contract_start_date' => $startDate = now()->subMonths(rand(1, 24))->format('Y-m-d'),
                        'contract_end_date' => now()->addMonths(rand(1, 24))->format('Y-m-d'),
                        'rent_amount' => $tenant['rent'],
                        'rent_billing_cycle' => $tenant['cycle'],
                        'status' => 'current_resident',
                        'move_in_date' => null,
                        'move_out_date' => null,
                    ]);
                    $this->command->info("Assigned apartment #" . $apartment->id . " to: " . $tenant['name']);
                } else {
                    $this->command->warn("No available apartment found for: " . $tenant['name']);
                }

                $bar->advance();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating tenants: ' . $e->getMessage());
            throw $e;
        }

        $bar->finish();
        $this->command->info("\nAll tenants created successfully for society " . $society->id);
    }
}
