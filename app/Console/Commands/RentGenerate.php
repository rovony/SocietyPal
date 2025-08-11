<?php

namespace App\Console\Commands;

use App\Models\Rent;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\ApartmentTenant;
use App\Models\ApartmentManagement;
use App\Models\Society;

class RentGenerate extends Command
{
    protected $signature = 'app:rent-generate';

    protected $description = 'Automatically generates rent entries for tenants based on billing cycle';

    public function handle()
    {
        $today = Carbon::now();

        $societies = Society::all();

        foreach ($societies as $society) {
            $this->info("Processing society: {$society->name}");

            $apartmentTenants = ApartmentTenant::with(['tenant', 'apartment'])
                ->where('status', 'current_resident')
                ->whereHas('apartment', function ($query) use ($society) {
                    $query->where('society_id', $society->id);
                })->get();

            foreach ($apartmentTenants as $apartmentTenant) {
                $tenant = $apartmentTenant->tenant;

                if ($apartmentTenant->rent_billing_cycle === 'annually') {
                    $this->handleAnnualBilling($apartmentTenant, $tenant, $today);
                } else {
                    $this->handleMonthlyBilling($apartmentTenant, $tenant, $today);
                }
            }
        }

        $this->info('Rent generation completed for all societies.');
    }

    private function handleAnnualBilling($apartmentTenant, $tenant, $today)
    {
        $contractStartDate = Carbon::parse($apartmentTenant->contract_start_date);
        $contractEndDate = $apartmentTenant->contract_end_date
            ? Carbon::parse($apartmentTenant->contract_end_date)
            : null;

        if ($contractEndDate && $contractEndDate->isPast()) {
            return;
        }

        $latestAnnualRent = Rent::where('tenant_id', $tenant->id)
            ->where('apartment_id', $apartmentTenant->apartment_id)
            ->orderByDesc('created_at')
            ->first();

        $nextBillingDate = $latestAnnualRent
            ? Carbon::createFromDate($latestAnnualRent->rent_for_year, Carbon::parse($latestAnnualRent->rent_for_month)->month, 1)->addYear()
            : $contractStartDate->copy()->startOfMonth();

        if ($today->greaterThanOrEqualTo($nextBillingDate->copy()->startOfMonth())) {
            $this->createRentEntry(
                $tenant->id,
                $apartmentTenant->apartment_id,
                $nextBillingDate,
                $apartmentTenant->rent_amount,
                false
            );
        } else {
            $this->info("Skipping annual rent for Tenant ID: {$tenant->id}. Next due: {$nextBillingDate->format('F Y')}.");
        }
    }

    private function handleMonthlyBilling($apartmentTenant, $tenant, $today)
    {
        $contractStartDate = Carbon::parse($apartmentTenant->contract_start_date);
        $contractEndDate = $apartmentTenant->contract_end_date
            ? Carbon::parse($apartmentTenant->contract_end_date)
            : null;

        if ($contractEndDate && $contractEndDate->isPast()) {
            return;
        }

        $latestRent = Rent::where('tenant_id', $tenant->id)
            ->where('apartment_id', $apartmentTenant->apartment_id)
            ->latest('created_at')
            ->first();

        $billingMonth = $latestRent
            ? Carbon::createFromDate($latestRent->rent_for_year, Carbon::parse($latestRent->rent_for_month)->month, 1)->addMonth()
            : $contractStartDate->copy();

        $startOfMonth = $billingMonth->copy()->startOfMonth();
        $endOfMonth = $billingMonth->copy()->endOfMonth();

        if ($contractStartDate->lessThanOrEqualTo($startOfMonth)) {
            $this->createRentEntry(
                $tenant->id,
                $apartmentTenant->apartment_id,
                $billingMonth,
                $apartmentTenant->rent_amount,
                false
            );
        } elseif ($contractStartDate->between($startOfMonth, $endOfMonth)) {
            $daysRented = $endOfMonth->diffInDays($contractStartDate) + 1;
            $rentAmount = ($apartmentTenant->rent_amount / $endOfMonth->daysInMonth) * $daysRented;

            $this->createRentEntry(
                $tenant->id,
                $apartmentTenant->apartment_id,
                $billingMonth,
                $rentAmount,
                true
            );
        } else {
            $this->info("Skipping monthly rent for Tenant ID: {$tenant->id}. Contract starts after {$billingMonth->format('F Y')}.");
        }
    }

    private function createRentEntry(int $tenantId, int $apartmentId, Carbon $month, float $rentAmount, bool $isProrated): void
    {
        $monthFormatted = strtolower($month->format('F'));

        $existing = Rent::where('tenant_id', $tenantId)
            ->where('apartment_id', $apartmentId)
            ->where('rent_for_year', $month->year)
            ->where('rent_for_month', $monthFormatted)
            ->first();

        if ($existing) {
            $this->info("Rent already exists for Tenant ID: {$tenantId} for {$month->format('F Y')}.");
            return;
        }

        $apartment = ApartmentManagement::find($apartmentId);

        if (!$apartment) {
            $this->error("Apartment ID {$apartmentId} not found. Skipping rent creation.");
            return;
        }

        Rent::create([
            'society_id' => $apartment->society_id,
            'tenant_id' => $tenantId,
            'apartment_id' => $apartmentId,
            'rent_for_year' => $month->year,
            'rent_for_month' => $monthFormatted,
            'rent_amount' => round($rentAmount, 2),
            'status' => 'unpaid',
        ]);

        $rentType = $isProrated ? 'Prorated' : 'Full';
        $this->info("{$rentType} rent generated for Tenant ID: {$tenantId} for {$month->format('F Y')}. Amount: " . round($rentAmount, 2));
    }
}