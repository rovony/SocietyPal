<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $ownerRole = Role::where('name', 'Owner_' . $society->id)->first();

        $id = $society->id;

        $owners = [
            [
                'name' => 'Owner',
                'email' => 'owner@example.com',
                'phone_number' => '(555) 123-4567'
            ],
            [
                'name' => 'Owner',
                'email' => 'owner.' . $id . '@example.com',
                'phone_number' => '(555) 123-4567'
            ],
            [
                'name' => 'John Doe',
                'email' => 'john.doe.' . $id . '@example.com',
                'phone_number' => '(555) 123-4567'
            ],
            [
                'name' => 'Mary Johnson',
                'email' => 'mary.johnson.' . $id . '@example.com',
                'phone_number' => '(555) 234-5678'
            ],
            [
                'name' => 'Robert Williams',
                'email' => 'robert.williams.' . $id . '@example.com',
                'phone_number' => '(555) 345-6789'
            ],
            [
                'name' => 'Patricia Brown',
                'email' => 'patricia.brown.' . $id . '@example.com',
                'phone_number' => '(555) 456-7890'
            ],
            [
                'name' => 'Michael Davis',
                'email' => 'michael.davis.' . $id . '@example.com',
                'phone_number' => '(555) 567-8901'
            ],
            [
                'name' => 'Jennifer Garcia',
                'email' => 'jennifer.garcia.' . $id . '@example.com',
                'phone_number' => '(555) 678-9012'
            ],
            [
                'name' => 'James Miller',
                'email' => 'james.miller.' . $id . '@example.com',
                'phone_number' => '(555) 789-0123'
            ],
            [
                'name' => 'Linda Martinez',
                'email' => 'linda.martinez.' . $id . '@example.com',
                'phone_number' => '(555) 890-1234'
            ],
            [
                'name' => 'William Anderson',
                'email' => 'william.anderson.' . $id . '@example.com',
                'phone_number' => '(555) 901-2345'
            ],
            [
                'name' => 'Elizabeth Thomas',
                'email' => 'elizabeth.thomas.' . $id . '@example.com',
                'phone_number' => '(555) 012-3456'
            ]
        ];

        $this->command->info('Creating owners for society ' . $society->id);

        $hashedPassword = Hash::make('123456');
        $usersToCreate = [];

        foreach ($owners as $ownerData) {

            if ($society->id !== 1 && $ownerData['name'] === 'Owner') {
                continue;
            }
            $usersToCreate[] = [
                'society_id' => $society->id,
                'name' => $ownerData['name'],
                'email' => $ownerData['email'],
                'phone_number' => $ownerData['phone_number'],
                'role_id' => $ownerRole->id,
                'owner' => true,
                'password' => $hashedPassword,
            ];
        }

        try {
            // Bulk insert users
            $createdUsers = User::insert($usersToCreate);

            // Bulk assign roles
            $users = User::where('society_id', $society->id)
                ->whereIn('email', array_column($owners, 'email'))
                ->get();

            foreach ($users as $user) {
                $user->assignRole($ownerRole);
            }
        } catch (\Exception $e) {
            Log::error('Error creating owners: ' . $e->getMessage());
        }
    }
}
