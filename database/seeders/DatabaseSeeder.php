<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Society;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(GlobalSettingSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(EmailSettingSeeder::class);
        $this->call(GlobalCurrencySeeder::class);


        $this->call(SuperadminSeeder::class);
        $this->call(LanguageSettingSeeder::class);
        $this->call(ModuleSeeder::class);
        $this->call(PackageSeeder::class);
        $this->call(SuperadminPaymentGatewaySeeder::class);

        $this->call(PermissionSeeder::class);

        $this->call(SocietySeeder::class);
        $this->call(FrontDetailSeeder::class);

        $societies = Society::all();

        foreach ($societies as $society) {

            $this->call(VisitorTypeSeeder::class, false, ['society' => $society]);
            $this->call(RoleSeeder::class, false, ['society' => $society]);
            $this->call(UserSeeder::class, false, ['society' => $society]);
            $this->call(TicketTypeSeeder::class, false, ['society' => $society]);

            if (!app()->environment('codecanyon')) {
                $this->call(OwnerSeeder::class, false, ['society' => $society]);
                $this->call(TicketAgentSeeder::class, false, ['society' => $society]);
                $this->call(TowerSeeder::class, false, ['society' => $society]);
                $this->call(FloorSeeder::class, false, ['society' => $society]);

                $this->call(BillTypeSeeder::class, false, ['society' => $society]);
                $this->call(ParkingManagementSeeder::class, false, ['society' => $society]);
                $this->call(ApartmentManagementSeeder::class, false, ['society' => $society]);
                $this->call(AmenitiesSeeder::class, false, ['society' => $society]);
                $this->call(BookAmenitySeeder::class, false, ['society' => $society]);
                $this->call(NoticeBoardSeeder::class, false, ['society' => $society]);

                $this->call(VisitorManagementSeeder::class, false, ['society' => $society]);
                $this->call(UtilityBillsSeeder::class, false, ['society' => $society]);
                $this->call(TicketSeeder::class, false, ['society' => $society]);
                $this->call(TenantSeeder::class, false, ['society' => $society]);
                $this->call(RentSeeder::class, false, ['society' => $society]);
                $this->call(MaintenanceManagementSeeder::class, false, ['society' => $society]);
                $this->call(CommonAreaBillSeeder::class, false, ['society' => $society]);

                $this->call(ServiceManagementSeeder::class, false, ['society' => $society]);
                $this->call(ServiceClockInOutSeeder::class, false, ['society' => $society]);
                $this->call(AssetCategorySeeder::class, false, ['society' => $society]);
                $this->call(AssetManagementSeeder::class, false, ['society' => $society]);
                $this->call(EventSeeder::class, false, ['society' => $society]);
                $this->call(ForumCategorySeeder::class, false, ['society' => $society]);
                $this->call(ForumSeeder::class, false, ['society' => $society]);

            }
        }

        cache()->flush();
    }

}
