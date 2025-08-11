<?php

namespace Database\Seeders;

use App\Models\Notice;
use App\Models\NoticeRole;
use App\Models\Society;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Role;

class NoticeBoardSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $roles = Role::where('name', '<>', 'Super Admin')->get();

        $notices = [
            [
                'title' => 'Annual General Meeting Notice',
                'description' => 'The Annual General Meeting (AGM) of our society will be held on December 15th, 2023 at 6:00 PM in the Community Hall. All residents are requested to attend this important meeting where we will discuss the annual budget, maintenance plans, and elect new committee members. Please bring your resident ID cards.'
            ],
            [
                'title' => 'Building Maintenance Schedule',
                'description' => 'We will be conducting essential maintenance work in the building from January 5th to January 15th, 2024. This includes water tank cleaning, elevator maintenance, and electrical system checks. There might be temporary water and power interruptions during this period. We apologize for any inconvenience caused.'
            ],
            [
                'title' => 'New Security Measures Implementation',
                'description' => 'Starting from February 1st, 2024, we are implementing new security measures. All visitors must register through the society app and obtain a digital pass. Residents are requested to update their family member details in the system. Security staff will strictly enforce these new protocols.'
            ],
            [
                'title' => 'Festival Celebration Guidelines',
                'description' => 'As we approach the festive season, we would like to remind all residents about the society guidelines for celebrations. Fireworks are only allowed in designated areas from 7 PM to 10 PM. Please ensure noise levels are maintained within permissible limits and common areas are kept clean after celebrations.'
            ],
            [
                'title' => 'Green Initiative Program',
                'description' => 'Our society is launching a new green initiative program. We will be installing solar panels, implementing waste segregation, and starting a community garden. Interested residents can volunteer for the environmental committee. Training sessions will be conducted every Saturday at 11 AM in the community center.'
            ],
            [
                'title' => 'Swimming Pool Maintenance Notice',
                'description' => 'The swimming pool will be closed for maintenance from March 1st to March 5th, 2024. We will be conducting thorough cleaning, filter replacement, and safety equipment checks. The pool will reopen on March 6th. We appreciate your understanding.'
            ],
            [
                'title' => 'Parking Policy Update',
                'description' => 'Due to increasing vehicles, we are implementing new parking regulations. Each apartment will be allocated specific parking spots. Visitor parking will be limited to designated areas only. The new policy will be effective from April 1st, 2024.'
            ],
            [
                'title' => 'Community Health Camp',
                'description' => 'A free health camp is being organized in our society on March 15th, 2024. Services include general health check-up, eye examination, and dental check-up. All residents are encouraged to participate. Prior registration is required through the society office.'
            ],
            [
                'title' => 'Children\'s Park Renovation',
                'description' => 'The children\'s park will undergo renovation from February 20th to March 10th, 2024. New play equipment will be installed and safety features will be enhanced. During this period, children are requested to use the indoor play area.'
            ],
            [
                'title' => 'Society App Training Session',
                'description' => 'To help residents better utilize our society app, we are conducting training sessions every Sunday at 10 AM in the community hall. Learn about digital payments, maintenance requests, visitor management, and other features. All residents are welcome.'
            ]
        ];

        foreach ($notices as $notice) {
            $noticeId = Notice::insertGetId([
                'society_id' => $society->id,
                'title' => $notice['title'],
                'description' => $notice['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $assignedRoles = $roles->shuffle()->take(rand(2, $roles->count()));

            foreach ($assignedRoles as $role) {
                NoticeRole::insert([
                    'notice_id' => $noticeId,
                    'role_id' => $role->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
