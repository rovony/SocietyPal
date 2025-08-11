<?php

namespace App\Http\Controllers;

use App\Exports\TicketExport;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Tickets', society_modules()) || !in_array('Tickets', society_role_modules()), 403);
        abort_if((!user_can('Show Tickets')) && (isRole() != 'Owner') && (isRole() != 'Tenant') && (isRole() != 'Guard'), 403);
        return view('tickets.index');
    }

    public function show($id)
    {
        abort_if(!in_array('Tickets', society_modules()) || !in_array('Tickets', society_role_modules()), 403);

        $ticket = Ticket::with(['user', 'ticketType', 'agent'])->findOrFail($id);

        if (user_can('Show Tickets')) {
            return view('tickets.show', compact('ticket'));
        }

        if ($ticket->user_id === user()->id || $ticket->agent_id === user()->id) {
            return view('tickets.show', compact('ticket'));
        }

        abort(403);
    }

}
