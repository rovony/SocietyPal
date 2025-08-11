<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_if(!in_array('Event', society_modules()) || !in_array('Event', society_role_modules()), 403);
        abort_if((!user_can('Show Event')) && (isRole() != 'Owner') && (isRole() != 'Tenant') && (isRole() != 'Guard'), 403);
        return view('events.index');
    }


    public function loadEvents(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);
        $query = Event::query();

        if (!user_can('Show Event')) {
            $query->whereHas('attendee', function ($q) {
                $q->where('user_id', user()->id);
            });
        }
        $query->whereBetween('start_date_time', [$start, $end]);

        $events = $query->get();

        $formatted = $events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->event_name,
                'start' => $event->start_date_time->timezone(timezone())->format('Y-m-d H:i:s'),
                'end'   => $event->end_date_time->timezone(timezone())->format('Y-m-d H:i:s'),
                'description' => $event->description,
                'status' => $event->status,
                'location' => $event->where,
            ];
        });

        return response()->json($formatted);
    }
}
