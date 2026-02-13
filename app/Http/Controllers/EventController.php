<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display the landing page
     */
    public function home()
    {
        $upcomingEvents = Event::where('status', 'published')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->limit(8)
            ->get();

        return view('home', compact('upcomingEvents'));
    }

    /**
     * Display a listing of published events with filtering
     */
    public function index(Request $request)
    {
        $query = Event::where('status', 'published')
            ->where('event_date', '>=', now())
            ->with(['eventCategory', 'venue']);

        // Filter by Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by Category
        if ($request->filled('category')) {
            $categories = (array) $request->category;
            $query->whereHas('eventCategory', function ($q) use ($categories) {
                $q->whereIn('slug', $categories);
            });
        }

        // Filter by Location
        if ($request->filled('location')) {
            $query->whereHas('venue', function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->location . '%');
            });
        }

        // Filter by Period
        if ($request->filled('period')) {
            if ($request->period === 'today') {
                $query->whereDate('event_date', today());
            } elseif ($request->period === 'week') {
                $query->whereBetween('event_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($request->period === 'month') {
                $query->whereMonth('event_date', now()->month)
                      ->whereYear('event_date', now()->year);
            }
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        if ($sort === 'price_low') {
            $query->join('ticket_categories', 'events.id', '=', 'ticket_categories.event_id')
                ->select('events.*')
                ->orderBy('ticket_categories.price', 'asc');
        } elseif ($sort === 'price_high') {
            $query->join('ticket_categories', 'events.id', '=', 'ticket_categories.event_id')
                ->select('events.*')
                ->orderBy('ticket_categories.price', 'desc');
        } else {
            $query->orderBy('event_date', 'asc');
        }

        $events = $query->distinct()->paginate(12)->withQueryString();

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
