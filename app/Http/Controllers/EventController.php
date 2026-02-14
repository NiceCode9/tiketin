<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display the home page
     */
    public function home()
    {
        $upcomingEvents = Event::where('status', 'published')
            ->where('event_date', '>=', now())
            ->with(['venue', 'ticketCategories'])
            ->orderBy('event_date', 'asc')
            ->take(8)
            ->get();

        // Featured events (spotlight)
        $featuredEvents = Event::where('status', 'published')
            ->where('event_date', '>=', now())
            ->with(['venue', 'ticketCategories', 'eventCategory'])
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $categories = \App\Models\EventCategory::all();

        return view('home', compact('upcomingEvents', 'featuredEvents', 'categories'));
    }

    /**
     * Display a listing of published events
     */
    public function index(Request $request)
    {
        $query = Event::where('status', 'published')
            ->where('event_date', '>=', now())
            ->with(['venue', 'eventCategory', 'ticketCategories']);

        // Filter by City
        if ($request->filled('city')) {
            $cityId = $request->city;
            $cityName = \App\Models\City::find($cityId)?->name;
            if ($cityName) {
                // Normalize city name (remove "KABUPATEN " or "KOTA ")
                $normalizedCityName = str_replace(['KABUPATEN ', 'KOTA '], '', $cityName);
                $query->whereHas('venue', function ($q) use ($normalizedCityName) {
                    $q->where('city', 'like', '%'.$normalizedCityName.'%');
                });
            }
        }

        // Search by event name, venue name, or city
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhereHas('venue', function ($qv) use ($search) {
                        $qv->where('name', 'like', '%'.$search.'%')
                            ->orWhere('city', 'like', '%'.$search.'%');
                    });
            });
        }

        // Filter by category (Multiple)
        if ($request->filled('category')) {
            $categories = (array) $request->category;
            $query->whereHas('eventCategory', function ($q) use ($categories) {
                $q->whereIn('slug', $categories);
            });
        }

        // Filter by Price Range
        if ($request->filled('price_range') && $request->price_range !== 'all') {
            $parts = explode('-', $request->price_range);
            $min = $parts[0];
            $max = $parts[1] === 'max' ? 999999999 : $parts[1];

            $query->whereHas('ticketCategories', function ($q) use ($min, $max) {
                $q->where('price', '>=', $min)
                    ->where('price', '<=', $max);
            });
        }

        // Filter by Period
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('event_date', now()->today());
                    break;
                case 'week':
                    $query->whereBetween('event_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('event_date', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
            }
        }

        $events = $query->orderBy('event_date', 'asc')->paginate(9);
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

    /**
     * Get cities for Select2 AJAX with pagination
     */
    public function getCities(Request $request)
    {
        $search = $request->q;
        $page = $request->page ?: 1;
        $perPage = 10;

        $query = \App\Models\City::orderBy('name');

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $cities = $query->paginate($perPage, ['id', 'name as text'], 'page', $page);

        return response()->json([
            'results' => $cities->items(),
            'pagination' => [
                'more' => $cities->hasMorePages(),
            ],
        ]);
    }
}
