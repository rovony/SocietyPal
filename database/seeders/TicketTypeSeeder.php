<?php

namespace Database\Seeders;

use App\Models\Society;
use Illuminate\Database\Seeder;
use App\Models\TicketTypeSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TicketTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run($society): void
    {
        $ticketTypes = ['Management', 'Problem', 'Suggestion', 'Parking'];

        foreach ($ticketTypes as $type) {
            TicketTypeSetting::create([
                'type_name' => $type,
                'society_id' => $society->id,
            ]);
        }
    }
}
