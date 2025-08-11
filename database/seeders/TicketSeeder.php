<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketAgentSetting;
use App\Models\TicketReply;
use App\Models\TicketTypeSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    public function run($society)
    {
        $ticketTypes = TicketTypeSetting::where('society_id', $society->id)->pluck('id');
        $agents = TicketAgentSetting::where('society_id', $society->id)->pluck('ticket_agent_id');
        $users = User::where('society_id', $society->id)->pluck('id');

        $tickets = [
            [
                'subject' => 'Water Leakage in Bathroom',
                'reply' => 'There is water leaking from the bathroom ceiling. Please send someone to check and fix it urgently.',
                'status' => 'open'
            ],
            [
                'subject' => 'Elevator Not Working',
                'reply' => 'The elevator in Tower A has been non-functional since morning. Many elderly residents are facing difficulties.',
                'status' => 'pending'
            ],
            [
                'subject' => 'Security Gate Issue',
                'reply' => 'The automatic security gate at the main entrance is not working properly. It gets stuck halfway.',
                'status' => 'resolved'
            ],
            [
                'subject' => 'Garden Maintenance Required',
                'reply' => 'The garden area needs maintenance. Several plants are dying and require immediate attention.',
                'status' => 'open'
            ],
            [
                'subject' => 'Parking Area Light Failure',
                'reply' => 'Multiple lights in the basement parking area have stopped working, making it difficult to park at night.',
                'status' => 'closed'
            ],
            [
                'subject' => 'Swimming Pool Maintenance',
                'reply' => 'The swimming pool water appears cloudy and needs immediate cleaning.',
                'status' => 'open'
            ],
            [
                'subject' => 'Gym Equipment Repair',
                'reply' => 'The treadmill in the gym is making unusual noises and needs repair.',
                'status' => 'pending'
            ],
            [
                'subject' => 'Fire Alarm System Check',
                'reply' => 'The fire alarm system on the 5th floor is beeping continuously. Please check.',
                'status' => 'open'
            ],
            [
                'subject' => 'Garbage Collection Issue',
                'reply' => 'Garbage has not been collected from Tower B for the last two days.',
                'status' => 'resolved'
            ],
            [
                'subject' => 'CCTV Camera Malfunction',
                'reply' => 'The CCTV camera near the parking entrance is not working.',
                'status' => 'pending'
            ],
            [
                'subject' => 'Pest Control Required',
                'reply' => 'Spotted several cockroaches in the building corridor. Need pest control service.',
                'status' => 'open'
            ],
            [
                'subject' => 'Internet Connection Problems',
                'reply' => 'Society WiFi connection is very slow in Block C.',
                'status' => 'pending'
            ],
            [
                'subject' => 'Door Intercom Not Working',
                'reply' => 'The intercom system at apartment 504 is not functioning.',
                'status' => 'closed'
            ],
            [
                'subject' => 'Water Supply Interruption',
                'reply' => 'No water supply in Tower D since morning.',
                'status' => 'resolved'
            ],
            [
                'subject' => 'Children\'s Play Area Safety',
                'reply' => 'The swing in the children\'s play area needs repair as it\'s making creaking sounds.',
                'status' => 'open'
            ],
            [
                'subject' => 'Lift Emergency Button',
                'reply' => 'Emergency button in Lift 2 is not working properly.',
                'status' => 'pending'
            ],
            [
                'subject' => 'Drainage Blockage',
                'reply' => 'Main drainage line near Tower C is blocked causing water logging.',
                'status' => 'open'
            ],
            [
                'subject' => 'Solar Panel Maintenance',
                'reply' => 'Solar panels need cleaning and maintenance check.',
                'status' => 'closed'
            ],
            [
                'subject' => 'Club House AC Issue',
                'reply' => 'Air conditioning in the club house is not cooling properly.',
                'status' => 'pending'
            ],
            [
                'subject' => 'Staircase Lighting',
                'reply' => 'Several lights in the emergency staircase are fused.',
                'status' => 'open'
            ]
        ];

        foreach ($tickets as $ticketData) {
            $ticket = Ticket::create([
                'society_id' => $society->id,
                'ticket_number' => rand(10000000, 99999999),
                'user_id' => $users->random(),
                'type_id' => $ticketTypes->random(),
                'status' => $ticketData['status'],
                'agent_id' => $agents->random(),
                'subject' => $ticketData['subject'],
                'reply' => $ticketData['reply'],
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30))
            ]);

            // Create 5 replies for each ticket
            $replies = [
                [
                    'message' => 'Thank you for reporting the issue. Our maintenance team will look into it shortly.',
                    'created_at' => $ticket->created_at->addHours(2)
                ],
                [
                    'message' => 'Our team has been assigned and will resolve this issue within 24 hours.',
                    'created_at' => $ticket->created_at->addHours(4)
                ],
                [
                    'message' => 'The maintenance team has inspected the issue and ordered necessary parts.',
                    'created_at' => $ticket->created_at->addHours(8)
                ],
                [
                    'message' => 'Repair work is in progress. We expect to complete it by tomorrow.',
                    'created_at' => $ticket->created_at->addHours(24)
                ],
                [
                    'message' => 'The issue has been resolved. Please confirm if everything is working properly.',
                    'created_at' => $ticket->created_at->addHours(36)
                ]
            ];

            foreach ($replies as $replyData) {
                TicketReply::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $ticket->user_id,
                    'message' => $replyData['message'],
                    'created_at' => $replyData['created_at'],
                    'updated_at' => $replyData['created_at']
                ]);
            }
        }
    }
}
