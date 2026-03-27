<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class SeatStatusController extends Controller
{
    /**
     * Get seat availability for an event
     */
    public function index(string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        // Fetch all seats for this event through ticket categories -> venue sections
        // We only need the status and ID for minimal payload
        $seats = \App\Models\Seat::whereIn('venue_section_id', function($query) use ($event) {
            $query->select('venue_section_id')
                ->from('ticket_categories')
                ->where('event_id', $event->id)
                ->where('is_seated', true);
        })
        ->select('id', 'status', 'row_label', 'seat_number')
        ->get();

        return response()->json([
            'seats' => $seats
        ]);
    }
}
