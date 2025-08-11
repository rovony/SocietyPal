<?php

namespace Database\Seeders;

use App\Models\Society;
use App\Models\Maintenance;
use App\Models\MaintenanceManagement;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MaintenanceManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $months = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];
        $maintenanceTypes = [
            ['title' => 'Security Upgrade', 'cost' => 500.00],
            ['title' => 'Garden Maintenance', 'cost' => 300.00],
            ['title' => 'Painting', 'cost' => 1000.00],
            ['title' => 'Plumbing Work', 'cost' => 450.00],
            ['title' => 'Electrical Maintenance', 'cost' => 600.00],
            ['title' => 'Elevator Service', 'cost' => 800.00],
            ['title' => 'Cleaning Service', 'cost' => 250.00]
        ];

        for ($i = 0; $i < 5; $i++) {
            $selectedMonth = $months[array_rand($months)];
            $selectedMaintenanceTypes = array_rand($maintenanceTypes, rand(1, 3));

            $additionalDetails = [];
            $totalCost = 0;

            if (!is_array($selectedMaintenanceTypes)) {
                $selectedMaintenanceTypes = [$selectedMaintenanceTypes];
            }

            foreach ($selectedMaintenanceTypes as $index) {
                $additionalDetails[] = $maintenanceTypes[$index];
                $totalCost += $maintenanceTypes[$index]['cost'];
            }

            MaintenanceManagement::create([
                'society_id' => $society->id,
                'month' => $selectedMonth,
                'year' => '2024',
                'additional_details' => json_encode($additionalDetails),
                'total_additional_cost' => $totalCost,
                'payment_due_date' => Carbon::now()->addDays(30 * ($i + 1))->format('Y-m-d'),
                'status' => rand(0, 1) ? 'published' : 'draft',
            ]);
        }
    }
}
