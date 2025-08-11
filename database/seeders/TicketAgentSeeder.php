<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\TicketAgentSetting;
use App\Models\TicketTypeSetting;

class TicketAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $users = User::where('society_id', $society->id)->get()->shuffle();
        $users = User::where('society_id', $society->id)->get()->shuffle();
        $userCount = $users->count();
        $ticketTypeId = TicketTypeSetting::where('society_id', $society->id)->pluck('id')->first();

        $ticketTypeId = TicketTypeSetting::where('society_id', $society->id)->pluck('id')->first();


        if ($userCount > 0) {
            $ticketAgentsToCreate = 3;
            $users = $users->take($ticketAgentsToCreate);
            foreach ($users as $user) {
                TicketAgentSetting::create([
                    'ticket_agent_id' => $user->id,
                    'ticket_type_id' => $ticketTypeId,
                    'society_id' => $society->id,
                ]);
            }
        }
    }
}

