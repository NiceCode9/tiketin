@extends('layouts.app')

@section('title', 'Browse Events - Tiketin')

@section('content')
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-brand-primary to-brand-secondary py-16 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-in">
                Temukan Event Favoritmu
            </h1>
            <p class="text-xl text-white/90 mb-8 animate-slide-up">
                Jelajahi berbagai event menarik dan dapatkan tiketmu sekarang!
            </p>

            {{-- Search Bar --}}
            <form action="{{ route('events.index') }}" method="GET" class="max-w-2xl mx-auto">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari event berdasarkan nama..."
                        class="w-full px-6 py-4 pr-32 rounded-full text-gray-900 focus:ring-4 focus:ring-white/30 focus:outline-none shadow-lg">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 btn-yellow px-6 py-2">
                        <i class="fas fa-search mr-2"></i>
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Filter Sidebar --}}
            <aside class="lg:w-64 flex-shrink-0">
                <div class="card sticky top-20">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-filter mr-2 text-brand-primary"></i>
                            Filter
                        </h3>

                        <form action="{{ route('events.index') }}" method="GET" id="filterForm">
                            {{-- Preserve search query --}}
                            @if (request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif

                            {{-- Category Filter --}}
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Kategori</label>
                                <div class="space-y-2">
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="radio" name="category" value=""
                                            {{ !request('category') ? 'checked' : '' }}
                                            onchange="document.getElementById('filterForm').submit()"
                                            class="text-brand-primary focus:ring-brand-primary">
                                        <span class="ml-2 text-sm text-gray-700 group-hover:text-brand-primary">Semua
                                            Kategori</span>
                                    </label>
                                    @foreach ($categories as $cat)
                                        <label class="flex items-center cursor-pointer group">
                                            <input type="radio" name="category" value="{{ $cat->id }}"
                                                {{ request('category') == $cat->id ? 'checked' : '' }}
                                                onchange="document.getElementById('filterForm').submit()"
                                                class="text-brand-primary focus:ring-brand-primary">
                                            <span
                                                class="ml-2 text-sm text-gray-700 group-hover:text-brand-primary">{{ $cat->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Date Range Filter --}}
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Tanggal</label>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Dari</label>
                                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                                            class="input text-sm py-2">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Sampai</label>
                                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                                            class="input text-sm py-2">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary w-full text-sm">
                                <i class="fas fa-check mr-2"></i>
                                Terapkan Filter
                            </button>

                            @if (request()->hasAny(['category', 'date_from', 'date_to']))
                                <a href="{{ route('events.index', ['search' => request('search')]) }}"
                                    class="btn-ghost w-full text-sm mt-2">
                                    <i class="fas fa-times mr-2"></i>
                                    Reset Filter
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Events Grid --}}
            <div class="flex-1">
                @if ($events->count() > 0)
                    {{-- Results Count --}}
                    <div class="mb-6 flex items-center justify-between">
                        <p class="text-gray-600">
                            Menampilkan <span class="font-semibold text-gray-900">{{ $events->count() }}</span>
                            dari <span class="font-semibold text-gray-900">{{ $events->total() }}</span> event
                        </p>
                    </div>

                    {{-- Event Cards Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        @foreach ($events as $event)
                            <div class="event-card group animate-fade-in">
                                {{-- Event Image --}}
                                <div class="relative overflow-hidden">
                                    @if ($event->banner_image)
                                        <img src="{{ Storage::url($event->banner_image) }}" alt="{{ $event->name }}"
                                            class="event-card-image">
                                    @else
                                        <div class="h-48 bg-gradient-to-r from-brand-primary to-brand-secondary"></div>
                                    @endif

                                    {{-- Category Badge --}}
                                    @if ($event->eventCategory)
                                        <div class="absolute top-4 left-4">
                                            <span class="badge-primary backdrop-blur-sm bg-white/90">
                                                {{ $event->eventCategory->name }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Event Content --}}
                                <div class="event-card-content">
                                    <h3
                                        class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-brand-primary transition">
                                        {{ $event->name }}
                                    </h3>

                                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                                        {{-- Date --}}
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-alt w-5 text-brand-primary"></i>
                                            <span class="ml-2">{{ $event->event_date->format('d M Y, H:i') }} WIB</span>
                                        </div>

                                        {{-- Venue --}}
                                        @if ($event->venue)
                                            <div class="flex items-center">
                                                <i class="fas fa-map-marker-alt w-5 text-brand-primary"></i>
                                                <span class="ml-2 line-clamp-1">{{ $event->venue->name }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <a href="{{ route('events.show', $event->slug) }}" class="btn-primary w-full text-sm">
                                        Lihat Detail
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $events->appends(request()->query())->links() }}
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="text-center py-16">
                        <div class="mb-6">
                            <i class="fas fa-calendar-times text-6xl text-gray-300"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Tidak Ada Event Ditemukan</h3>
                        <p class="text-gray-600 mb-6">
                            @if (request()->hasAny(['search', 'category', 'date_from', 'date_to']))
                                Coba ubah filter atau kata kunci pencarian Anda
                            @else
                                Belum ada event yang tersedia saat ini
                            @endif
                        </p>
                        @if (request()->hasAny(['search', 'category', 'date_from', 'date_to']))
                            <a href="{{ route('events.index') }}" class="btn-primary">
                                <i class="fas fa-redo mr-2"></i>
                                Lihat Semua Event
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
