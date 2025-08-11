<?php
namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\AssetMaintenance;
use App\Notifications\MaintenanceScheduled;

class ScheduleMaintenance extends Command
{
    protected $signature = 'maintenance:schedule';
    protected $description = 'Automatically schedule the next maintenance for assets';

    public function handle()
    {
        $completedMaintenances = AssetMaintenance::where('status', 'completed')
            ->where('reminder', true) // Only include records where reminder is true
            ->get();

        foreach ($completedMaintenances as $maintenance) {
            $nextDate = $this->getNextMaintenanceDate($maintenance->maintenance_date, $maintenance->schedule);

            if ($nextDate > Carbon::today()) {
                $assetMaintenance = new AssetMaintenance;
                $assetMaintenance->asset_id = $maintenance->asset_id;
                $assetMaintenance->maintenance_date = $nextDate;
                $assetMaintenance->schedule = $maintenance->schedule;
                $assetMaintenance->status = 'pending';
                $assetMaintenance->amount = null;
                $assetMaintenance->save();

                $asset = $assetMaintenance->asset()->with(['apartments.apartmentTenants.tenant'])->first();
                if ($asset && $asset->apartments) {
                    $apartments = $asset->apartments instanceof \Illuminate\Database\Eloquent\Collection
                        ? $asset->apartments
                        : collect([$asset->apartments]);

                    foreach ($apartments as $apartment) {
                        $owner = User::find($apartment->user_id);

                        if ($owner) {
                            $owner->notify(new MaintenanceScheduled($assetMaintenance));
                            $this->info("Notification sent to owner: {$owner->email}");
                        }

                        // Check if `apartmentTenants` exists before looping
                        if ($apartment->relationLoaded('apartmentTenants')) {
                            foreach ($apartment->apartmentTenants as $apartmentTenant) {
                                if ($apartmentTenant->relationLoaded('tenant') && $apartmentTenant->tenant) {
                                    $tenantUser = User::find($apartmentTenant->tenant->user_id);
                                    if ($tenantUser) {
                                        $tenantUser->notify(new MaintenanceScheduled($assetMaintenance));
                                        $this->info("Notification sent to tenant: {$tenantUser->email}");
                                    }
                                }
                            }
                        }
                    }
                }
                $this->info("Next maintenance scheduled for Asset ID {$maintenance->asset_id} on {$nextDate}");
            }
        }

        $this->info('Maintenance scheduling completed.');
    }

    private function getNextMaintenanceDate($currentDate, $schedule)
    {
        $date = Carbon::parse($currentDate);

        return match ($schedule) {
            'weekly'    => $date->addWeek(),
            'biweekly'  => $date->addWeeks(2),
            'monthly'   => $date->addMonth(),
            'half-year' => $date->addMonths(6),
            'yearly'    => $date->addYear(),
            default     => null,
        };
    }
}
