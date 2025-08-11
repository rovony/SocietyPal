<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\MaintenanceManagement;
use App\Models\Society;


class GenerateMonthlyMaintenance extends Command
{
    protected $signature = 'maintenance:generate';
    protected $description = 'Generate maintenance record for the previous month';

    public function handle()
    {
        $societies = Society::all();

        foreach ($societies as $society) {
            // Get last maintenance for this society only
            $lastMaintenance = MaintenanceManagement::where('society_id', $society->id)
                ->orderBy('year', 'desc')
                ->orderByRaw("FIELD(month, 'december','november','october','september','august','july','june','may','april','march','february','january')")
                ->first();

            if (!$lastMaintenance) {
                // If no maintenance yet, start with current month
                $date = now();
            } else {
                // If maintenance exists, start with next month
                $date = Carbon::createFromFormat('F Y', ucfirst($lastMaintenance->month) . ' ' . $lastMaintenance->year)->addMonth();
            }

            // Check if this month's record already exists for the society
            $exists = MaintenanceManagement::where('society_id', $society->id)
                ->where('month', strtolower($date->format('F')))
                ->where('year', $date->year)
                ->exists();

            if ($exists) {
                $this->info("⏭️ Maintenance for {$date->format('F Y')} already exists for Society ID: {$society->id}");
                continue;
            }

            // Prepare data (copy from last if exists)
            $additionalCosts = $lastMaintenance ? json_decode($lastMaintenance->additional_details, true) ?? [] : [];
            $totalAdditionalCost = $lastMaintenance ? $lastMaintenance->total_additional_cost : 0;
            $paymentDueDate = $date->copy()->endOfMonth()->format('Y-m-d');

            MaintenanceManagement::create([
                'society_id' => $society->id,
                'month' => strtolower($date->format('F')),
                'year' => $date->year,
                'additional_details' => json_encode($additionalCosts),
                'total_additional_cost' => $totalAdditionalCost,
                'payment_due_date' => $paymentDueDate,
                'status' => 'draft',
            ]);

            $this->info("✅ Maintenance created for {$date->format('F Y')} — Society ID: {$society->id}");
        }

        return 0;
    }
}