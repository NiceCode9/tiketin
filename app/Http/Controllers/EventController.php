<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of published events
     */
    public function index(Request $request)
    {
        $events = Event::where('status', 'published')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->paginate(12);

        return view('events.index', compact('events'));
    }

    /**
     * Display the specified event
     */
    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->where('status', 'published')
            ->with(['venue', 'ticketCategories.venueSection'])
            ->firstOrFail();

        // Check if event has passed
        if ($event->event_date < now()) {
            abort(404, 'This event has already passed');
        }

        return view('events.show', compact('event'));
    }
}
