@extends('layouts.app')

@section('title', $event->name . ' - Tiketin')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Event Status Alert --}}
        @if ($event->event_date < now())
            <div class="bg-slate-900 border-l-4 border-slate-500 p-6 mb-8 rounded-2xl shadow-xl animate-slide-down">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-slate-800 rounded-full flex items-center justify-center mr-4 border border-slate-700">
                        <i class="fas fa-calendar-check text-slate-400 text-2xl"></i>
                    </div>
                    <div>
                        <p class="font-black text-white text-lg uppercase tracking-tight">Event Telah Berakhir</p>
                        <p class="text-sm text-slate-400">Maaf, penjualan tiket untuk event ini sudah ditutup karena periode pelaksanaan event telah terlaksana atau berakhir.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Event Header with Image --}}
        <div class="card mb-8 animate-fade-in">
            {{-- Banner Image --}}
            <div class="relative h-96 overflow-hidden">
                @if ($event->banner_image)
                    <img src="{{ Storage::url($event->banner_image) }}" alt="{{ $event->name }}"
                        class="w-full h-full object-cover">
                @else
                    <div class="h-full bg-gradient-to-r from-brand-primary to-brand-secondary"></div>
                @endif

                {{-- Category Badge Overlay --}}
                @if ($event->eventCategory)
                    <div class="absolute top-6 left-6">
                        <span class="badge-primary backdrop-blur-sm bg-white/90 text-base px-4 py-2">
                            {{ $event->eventCategory->name }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Event Info --}}
            <div class="p-8">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">{{ $event->name }}</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    {{-- Date & Time --}}
                    <div class="flex items-start gap-4">
                        <div
                            class="flex-shrink-0 w-12 h-12 bg-brand-primary/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-brand-primary text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Tanggal & Waktu</h3>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $event->event_date->format('l, d F Y') }}
                            </p>
                            <p class="text-md text-gray-600">
                                {{ $event->event_date->format('H:i') }} WIB
                            </p>
                        </div>
                    </div>

                    {{-- Venue --}}
                    @if ($event->venue)
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-brand-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-brand-primary text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-1">Lokasi</h3>
                                <p class="text-lg font-semibold text-gray-900">{{ $event->venue->name }}</p>
                                <p class="text-md text-gray-600">{{ $event->venue->city }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Description --}}
                @if ($event->description)
                    <div class="border-t pt-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Tentang Event Ini</h3>
                        <div class="prose max-w-none text-gray-700">
                            {!! $event->description !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Ticket Categories --}}
        <div class="card animate-slide-up">
            <div class="p-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-ticket-alt text-brand-primary mr-3"></i>
                    Kategori Tiket
                </h2>

                @if ($event->ticketCategories->count() > 0)
                    <div class="space-y-4 mb-8">
                        @foreach ($event->ticketCategories as $category)
                            <div
                                class="border-2 border-gray-200 rounded-xl p-6 hover:border-brand-primary hover:shadow-lg transition-all duration-300">
                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div class="flex-1">
                                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $category->name }}</h3>
                                        @if ($category->is_seated && $category->venueSection)
                                            <p class="text-sm text-gray-600 flex items-center">
                                                <i class="fas fa-couch mr-2"></i>
                                                Section: {{ $category->venueSection->name }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-6">
                                        {{-- Availability --}}
                                        <div>
                                            @if ($category->hasAvailableTickets())
                                                <span class="badge-success text-base px-4 py-2">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    {{ $category->available_count }} tersedia
                                                </span>
                                            @else
                                                <span class="badge-danger text-base px-4 py-2">
                                                    <i class="fas fa-times-circle mr-1"></i>
                                                    Sold Out
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Price --}}
                                        <div class="text-right">
                                            <p class="text-3xl font-bold text-brand-primary">
                                                Rp {{ number_format($category->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Buy Tickets Button --}}
                    <div class="text-center">
                        @if ($event->event_date < now())
                            <div class="inline-flex items-center gap-3 bg-slate-100 text-slate-400 border border-slate-200 text-lg px-12 py-4 rounded-2xl font-black cursor-not-allowed">
                                <i class="fas fa-lock"></i>
                                PENJUALAN DITUTUP
                            </div>
                        @else
                            <a href="{{ route('orders.create', $event->slug) }}"
                                class="btn-yellow text-lg px-12 py-4 inline-flex items-center gap-3 shadow-xl hover:shadow-2xl">
                                <i class="fas fa-shopping-cart"></i>
                                Beli Tiket Sekarang
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-ticket-alt text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Belum ada kategori tiket untuk event ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
