@extends('layouts.app')

@section('title', $event->name . ' - Tiketin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Event Header -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="h-64 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
        <div class="p-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $event->name }}</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Date & Time</h3>
                    <p class="text-lg text-gray-900">
                        {{ $event->event_date->format('l, d F Y') }}<br>
                        {{ $event->event_date->format('H:i') }} WIB
                    </p>
                </div>
                
                @if($event->venue)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Venue</h3>
                        <p class="text-lg text-gray-900">
                            {{ $event->venue->name }}<br>
                            <span class="text-gray-600">{{ $event->venue->city }}</span>
                        </p>
                    </div>
                @endif
            </div>

            @if($event->description)
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">About This Event</h3>
                    <div class="prose max-w-none">
                        {!! $event->description !!}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Ticket Categories -->
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Ticket Categories</h2>
        
        @if($event->ticketCategories->count() > 0)
            <div class="space-y-4">
                @foreach($event->ticketCategories as $category)
                    <div class="border rounded-lg p-6 hover:border-indigo-500 transition">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ $category->name }}</h3>
                                @if($category->is_seated && $category->venueSection)
                                    <p class="text-sm text-gray-600">Section: {{ $category->venueSection->name }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-indigo-600">
                                    Rp {{ number_format($category->price, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mt-4">
                            <div>
                                @if($category->hasAvailableTickets())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        {{ $category->available_count }} tickets available
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        Sold Out
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                <a href="{{ route('orders.create', $event->slug) }}" 
                   class="block w-full md:w-auto text-center bg-indigo-600 text-white py-3 px-8 rounded-md hover:bg-indigo-700 transition text-lg font-semibold">
                    Buy Tickets
                </a>
            </div>
        @else
            <p class="text-gray-500">No ticket categories available for this event.</p>
        @endif
    </div>
</div>
@endsection
