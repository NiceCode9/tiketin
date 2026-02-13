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
        $query = Event::where('status', 'published')
            ->where('event_date', '>=', now())
            ->with(['venue', 'eventCategory']);

        // Search by event name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('event_category_id', $request->category);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('event_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('event_date', '<=', $request->date_to);
        }

        $events = $query->orderBy('event_date', 'asc')->paginate(12);
        $categories = \App\Models\EventCategory::all();

        return view('events.index', compact('events', 'categories'));
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
