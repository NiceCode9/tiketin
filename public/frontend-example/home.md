@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <!-- Hero Section -->
    {{-- <section class="relative bg-slate-900 text-white py-20 lg:py-32 overflow-hidden"> --}}
    <section class="relative text-white py-20 lg:py-32 overflow-hidden bg-cover bg-center"
        style="background-image: url('{{ asset('bg-landing.jpg') }}');">

        <!-- Overlay gelap -->
        <div class="absolute inset-0 bg-slate-900/45 z-0"></div>

        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
            <div class="absolute -top-20 -right-20 w-96 h-96 bg-brand-yellow opacity-10 rounded-full blur-3xl">
            </div>
            <div class="absolute top-40 -left-20 w-72 h-72 bg-purple-600 opacity-10 rounded-full blur-3xl">
            </div>
        </div>
        <div class="container mx-auto px-4 relative z-10 text-center">
            {{-- <span
                class="inline-block py-1 px-3 rounded-full bg-slate-800 border border-slate-700 text-brand-yellow text-sm font-semibold mb-6 animate-bounce">
                🎉 Platform Ticketing #1 di Indonesia
            </span> --}}
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight text-black">
                Partner Terbaik untuk <br />
                <span class="text-black bg-clip-text bg-gradient-to-r from-brand-yellow to-yellow-200">Segala
                    Jenis Event</span>
            </h1>
            {{-- <p class="text-gray-400 text-lg md:text-xl max-w-2xl mx-auto mb-10">
                Temukan ribuan konser, workshop, dan festival seru.
            </p> --}}

            <!-- Search Bar -->
            <form action="{{ route('events.index') }}" method="GET"
                class="max-w-3xl mx-auto bg-white p-2 rounded-full shadow-2xl flex items-center mb-12">
                <div class="pl-6 text-gray-400"><i class="fas fa-search text-lg"></i></div>
                <input type="text" name="search"
                    class="w-full px-4 py-3 md:py-4 text-gray-700 focus:outline-none bg-transparent"
                    placeholder="Cari event, artis, atau lokasi...">
                <button type="submit"
                    class="bg-slate-900 hover:bg-slate-800 text-white px-8 py-3 md:py-4 rounded-full font-bold transition">
                    Cari
                </button>
            </form>
        </div>
    </section>

    <!-- Featured Events List -->
    <section id="events-section" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">Event Mendatang</h2>
                    <p class="text-gray-500">Jangan sampai ketinggalan!</p>
                </div>
                <a href="{{ route('events.index') }}"
                    class="text-blue-600 font-semibold hover:underline flex items-center gap-2">
                    Lihat Semua <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Events Grid -->
            <div id="events-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($upcomingEvents as $event)
                    <div
                        class="bg-white rounded-xl shadow-md overflow-hidden cursor-pointer hover:shadow-xl transition group">
                        <a href="{{ route('events.show', $event->slug) }}">
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ $event->poster_image ? asset('storage/' . $event->poster_image) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&h=400&fit=crop' }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                    alt="{{ $event->name }}">
                            </div>
                            <div class="p-4">
                                <div class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                                    <i class="far fa-calendar"></i>
                                    {{ $event->event_date->format('d M Y') }}
                                </div>
                                <h3 class="font-bold text-lg mb-2 leading-snug group-hover:text-blue-600 transition">
                                    {{ $event->name }}
                                </h3>
                                <p class="text-xs text-gray-500 mb-3">
                                    <i class="fas fa-map-marker-alt mr-1"></i>{{ $event->venue }}
                                </p>
                                <div class="flex justify-between items-center mt-3">
                                    <span class="text-xs text-gray-500">Mulai dari</span>
                                    <span class="font-bold text-slate-900">
                                        @if ($event->ticketTypes->count() > 0)
                                            Rp {{ number_format($event->ticketTypes->min('price'), 0, ',', '.') }}
                                        @else
                                            TBA
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Belum ada event mendatang saat ini.</p>
                        <p class="text-gray-400 text-sm mt-2">Pantau terus untuk event-event menarik!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Category Section -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-slate-900 mb-12">Kategori Populer</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <!-- Konser -->
                <a href="{{ route('events.index', ['category' => 'konser']) }}"
                    class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition text-center border border-gray-100">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center group-hover:bg-purple-200 transition">
                        <i class="fas fa-music text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 group-hover:text-purple-600 transition">Konser</h3>
                </a>

                <!-- Stand Up -->
                <a href="{{ route('events.index', ['category' => 'standup']) }}"
                    class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition text-center border border-gray-100">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-yellow-100 rounded-full flex items-center justify-center group-hover:bg-yellow-200 transition">
                        <i class="fas fa-laugh text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 group-hover:text-yellow-600 transition">Stand Up</h3>
                </a>

                <!-- Workshop -->
                <a href="{{ route('events.index', ['category' => 'workshop']) }}"
                    class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition text-center border border-gray-100">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-blue-200 transition">
                        <i class="fas fa-chalkboard-teacher text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 group-hover:text-blue-600 transition">Workshop</h3>
                </a>

                <!-- Olahraga -->
                <a href="{{ route('events.index', ['category' => 'olahraga']) }}"
                    class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition text-center border border-gray-100">
                    <div
                        class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition">
                        <i class="fas fa-running text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 group-hover:text-green-600 transition">Olahraga</h3>
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-slate-900 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Siap untuk Petualangan Baru?</h2>
            <p class="text-gray-400 text-lg mb-8 max-w-2xl mx-auto">
                Temukan event favoritmu dan buat kenangan tak terlupakan bersama Untix!
            </p>
            <a href="{{ route('events.index') }}"
                class="inline-block bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 px-10 rounded-full transition transform hover:scale-105 shadow-lg shadow-yellow-500/20">
                Jelajah Event Sekarang
            </a>
        </div>
    </section>
@endsection
