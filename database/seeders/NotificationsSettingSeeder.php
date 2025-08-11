<?php

namespace Database\Seeders;

use App\Models\NotificationSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationsSettingSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notificationTypes = [
            [
                'type' => 'maintenance',
                'send_email' => 1
            ],
            [
                'type' => 'noticeboard',
                'send_email' => 1
            ],
            [
                'type' => 'visitor_arrival',
                'send_email' => 1
            ],
            [
                'type' => 'ticket',
                'send_email' => 1
            ],
            [
                'type' => 'contract_end_date_reminder',
                'send_email' => 1
            ],
            [
                'type' => 'rent_payment',
                'send_email' => 1
            ],
            [
                'type' => 'rent_payment_due',
                'send_email' => 1
            ],
            [
                'type' => 'amenitites',
                'send_email' => 1
            ],

        ];

        NotificationSetting::insert($notificationTypes);
    }

}
