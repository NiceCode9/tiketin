@extends('layouts.app')

@section('title', 'Jelajah Event')

@section('content')
    <div class="pt-8 pb-20 bg-gray-50">
        <div class="container mx-auto px-4">

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-slate-900">Jelajah Event</h1>
                    <p class="text-gray-600 mt-2">Temukan event seru di sekitarmu</p>
                </div>

                <!-- Search -->
                <form action="{{ route('events.index') }}" method="GET" class="relative w-full md:w-96">
                    @foreach (request()->except('search') as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $item)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach

                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-300 focus:outline-none focus:border-brand-yellow transition"
                        placeholder="Cari event, artis, lokasi...">
                    <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>

                    @if (request('search'))
                        <a href="{{ route('events.index', request()->except('search')) }}"
                            class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Active Filters Display -->
            @if (request()->hasAny(['search', 'category', 'location', 'price_range', 'period']))
                <div class="mb-6 flex flex-wrap gap-2 items-center">
                    <span class="text-sm text-gray-600">Filter aktif:</span>

                    @if (request('search'))
                        <span
                            class="inline-flex items-center bg-brand-yellow text-black text-xs font-semibold px-3 py-1 rounded-full">
                            Search: "{{ request('search') }}"
                            <a href="{{ route('events.index', request()->except('search')) }}"
                                class="ml-2 hover:text-red-600">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif

                    @if (request('category'))
                        @foreach (request('category') as $cat)
                            <span
                                class="inline-flex items-center bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">
                                {{ ucfirst($cat) }}
                                <a href="{{ route('events.index', array_merge(request()->except('category'), ['category' => array_diff(request('category', []), [$cat])])) }}"
                                    class="ml-2 hover:text-red-600">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        @endforeach
                    @endif

                    @if (request('location'))
                        <span
                            class="inline-flex items-center bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                            {{ ucfirst(request('location')) }}
                            <a href="{{ route('events.index', request()->except('location')) }}"
                                class="ml-2 hover:text-red-600">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif

                    @if (request('price_range') && request('price_range') !== 'all')
                        <span
                            class="inline-flex items-center bg-purple-100 text-purple-800 text-xs font-semibold px-3 py-1 rounded-full">
                            Price: {{ request('price_range') }}
                            <a href="{{ route('events.index', request()->except('price_range')) }}"
                                class="ml-2 hover:text-red-600">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif

                    @if (request('period'))
                        <span
                            class="inline-flex items-center bg-orange-100 text-orange-800 text-xs font-semibold px-3 py-1 rounded-full">
                            {{ ucfirst(request('period')) }}
                            <a href="{{ route('events.index', request()->except('period')) }}"
                                class="ml-2 hover:text-red-600">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    @endif

                    <a href="{{ route('events.index') }}" class="text-xs text-red-600 hover:underline ml-2">
                        <i class="fas fa-times-circle mr-1"></i> Hapus Semua Filter
                    </a>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                <!-- SIDEBAR: FILTER (Kiri) -->
                <aside class="lg:col-span-1">
                    <form action="{{ route('events.index') }}" method="GET" id="filter-form">
                        <!-- Preserve search query -->
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 sticky top-28">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="font-bold text-lg text-slate-900">
                                    <i class="fas fa-filter text-brand-yellow mr-2"></i> Filter
                                </h3>
                                @if (request()->hasAny(['category', 'location', 'price_range', 'period']))
                                    <a href="{{ route('events.index', request()->only('search')) }}"
                                        class="text-xs text-red-600 hover:underline font-semibold">
                                        <i class="fas fa-redo mr-1"></i> Reset
                                    </a>
                                @endif
                            </div>

                            <!-- Filter: Kategori -->
                            <div class="mb-6 pb-6 border-b border-gray-200">
                                <h4 class="font-semibold text-sm mb-3 text-slate-900">
                                    <i class="fas fa-tags text-brand-yellow mr-2"></i> Kategori
                                </h4>
                                <div class="space-y-2">
                                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                        <input type="checkbox" name="category[]" value="konser"
                                            {{ in_array('konser', request('category', [])) ? 'checked' : '' }}
                                            class="rounded text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-3 text-gray-700 text-sm">🎸 Konser Musik</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                        <input type="checkbox" name="category[]" value="standup"
                                            {{ in_array('standup', request('category', [])) ? 'checked' : '' }}
                                            class="rounded text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-3 text-gray-700 text-sm">😂 Stand Up Comedy</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                        <input type="checkbox" name="category[]" value="workshop"
                                            {{ in_array('workshop', request('category', [])) ? 'checked' : '' }}
                                            class="rounded text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-3 text-gray-700 text-sm">📚 Seminar & Workshop</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                        <input type="checkbox" name="category[]" value="olahraga"
                                            {{ in_array('olahraga', request('category', [])) ? 'checked' : '' }}
                                            class="rounded text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-3 text-gray-700 text-sm">⚽ Olahraga</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                        <input type="checkbox" name="category[]" value="festival"
                                            {{ in_array('festival', request('category', [])) ? 'checked' : '' }}
                                            class="rounded text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-3 text-gray-700 text-sm">🎉 Festival</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Filter: Lokasi -->
                            <div class="mb-6 pb-6 border-b border-gray-200">
                                <h4 class="font-semibold text-sm mb-3 text-slate-900">
                                    <i class="fas fa-map-marker-alt text-brand-yellow mr-2"></i> Lokasi
                                </h4>
                                <select name="location"
                                    class="w-full border-2 border-gray-300 rounded-lg p-2 text-sm focus:outline-none focus:border-brand-yellow transition"
                                    onchange="document.getElementById('filter-form').submit()">
                                    <option value="">Semua Lokasi</option>
                                    <option value="jakarta" {{ request('location') == 'jakarta' ? 'selected' : '' }}>
                                        Jakarta</option>
                                    <option value="bandung" {{ request('location') == 'bandung' ? 'selected' : '' }}>
                                        Bandung</option>
                                    <option value="surabaya" {{ request('location') == 'surabaya' ? 'selected' : '' }}>
                                        Surabaya</option>
                                    <option value="yogyakarta"
                                        {{ request('location') == 'yogyakarta' ? 'selected' : '' }}>Yogyakarta</option>
                                    <option value="bali" {{ request('location') == 'bali' ? 'selected' : '' }}>Bali
                                    </option>
                                    <option value="medan" {{ request('location') == 'medan' ? 'selected' : '' }}>Medan
                                    </option>
                                    <option value="semarang" {{ request('location') == 'semarang' ? 'selected' : '' }}>
                                        Semarang</option>
                                </select>
                            </div>

                            <!-- Filter: Harga -->
                            <div class="mb-6 pb-6 border-b border-gray-200">
                                <h4 class="font-semibold text-sm mb-3 text-slate-900">
                                    <i class="fas fa-money-bill-wave text-brand-yellow mr-2"></i> Range Harga
                                </h4>
                                <div class="space-y-2">
                                    <label
                                        class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                        <input type="radio" name="price_range" value="all"
                                            {{ request('price_range', 'all') == 'all' ? 'checked' : '' }}
                                            class="text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-3 text-gray-700 text-sm">Semua Harga</span>
                                    </label>
                                    <label
                                        class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                        <input type="radio" name="price_range" value="0-100000"
                                            {{ request('price_range') == '0-100000' ? 'checked' : '' }}
                                            class="text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-3 text-gray-700 text-sm">&lt; Rp 100.000</span>
                                    </label>
                                    <label
                                        class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                        <input type="radio" name="price_range" value="100000-300000"
                                            {{ request('price_range') == '100000-300000' ? 'checked' : '' }}
                                            class="text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-3 text-gray-700 text-sm">Rp 100K - 300K</span>
                                    </label>
                                    <label
                                        class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                        <input type="radio" name="price_range" value="300000-500000"
                                            {{ request('price_range') == '300000-500000' ? 'checked' : '' }}
                                            class="text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-3 text-gray-700 text-sm">Rp 300K - 500K</span>
                                    </label>
                                    <label
                                        class="flex items-center cursor-pointer hover:bg-gray-50 p-2 rounded transition">
                                        <input type="radio" name="price_range" value="500000-999999999"
                                            {{ request('price_range') == '500000-999999999' ? 'checked' : '' }}
                                            class="text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-3 text-gray-700 text-sm">&gt; Rp 500K</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Filter: Tanggal -->
                            <div>
                                <h4 class="font-semibold text-sm mb-3 text-slate-900">
                                    <i class="fas fa-calendar-alt text-brand-yellow mr-2"></i> Periode
                                </h4>
                                <select name="period"
                                    class="w-full border-2 border-gray-300 rounded-lg p-2 text-sm focus:outline-none focus:border-brand-yellow transition"
                                    onchange="document.getElementById('filter-form').submit()">
                                    <option value="">Semua Waktu</option>
                                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini
                                    </option>
                                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Minggu Ini
                                    </option>
                                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Bulan Ini
                                    </option>
                                </select>
                            </div>
                        </div>
                    </form>
                </aside>

                <!-- MAIN: EVENT GRID (Kanan) -->
                <div class="lg:col-span-3">
                    <!-- Sort & Count -->
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 bg-white p-4 rounded-xl shadow-sm">
                        <p class="text-gray-700 text-sm">
                            Menampilkan <strong class="text-brand-yellow">{{ $events->count() }}</strong> dari
                            <strong class="text-slate-900">{{ $events->total() }}</strong> event
                        </p>
                        <form action="{{ route('events.index') }}" method="GET" id="sort-form"
                            class="flex items-center gap-2">
                            @foreach (request()->except('sort') as $key => $value)
                                @if (is_array($value))
                                    @foreach ($value as $item)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach

                            <label for="sort" class="text-sm text-gray-600">Urutkan:</label>
                            <select name="sort" id="sort"
                                class="border-2 border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-brand-yellow transition"
                                onchange="document.getElementById('sort-form').submit()">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru
                                </option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga
                                    Terendah</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga
                                    Tertinggi</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler
                                </option>
                            </select>
                        </form>
                    </div>

                    <!-- Events Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        @forelse($events as $event)
                            <div
                                class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden hover:shadow-xl transition-all duration-300 group cursor-pointer transform hover:-translate-y-1">
                                <a href="{{ route('events.show', $event->slug) }}">
                                    <div class="relative h-48 overflow-hidden">
                                        <img src="{{ $event->poster_image ? asset('storage/' . $event->poster_image) : 'https://images.unsplash.com/photo-1533174072545-e8d4aa97edf9?auto=format&fit=crop&w=800&q=80' }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                            alt="{{ $event->name ?? $event->title }}">

                                        <!-- Category Badge -->
                                        <span
                                            class="absolute top-3 left-3 bg-brand-yellow text-black text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                            {{ ucfirst($event->category) }}
                                        </span>

                                        <!-- Date Badge -->
                                        <div
                                            class="absolute top-3 right-3 bg-white/95 backdrop-blur text-center px-2 py-1 rounded-lg shadow-lg">
                                            <div class="text-xs font-bold text-brand-yellow">
                                                {{ \Carbon\Carbon::parse($event->event_date)->format('d') }}
                                            </div>
                                            <div class="text-xs text-gray-600">
                                                {{ \Carbon\Carbon::parse($event->event_date)->format('M') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-5">
                                        <h3
                                            class="font-bold text-lg mb-2 text-slate-900 group-hover:text-brand-yellow transition line-clamp-2">
                                            {{ $event->name ?? $event->title }}
                                        </h3>

                                        <div class="flex items-center text-gray-600 text-sm mb-3">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                            <span class="line-clamp-1">{{ $event->venue }}</span>
                                        </div>

                                        <div class="flex items-center text-gray-600 text-sm mb-4">
                                            <i class="fas fa-clock text-blue-500 mr-2"></i>
                                            <span>{{ \Carbon\Carbon::parse($event->event_date)->format('H:i') }} WIB</span>
                                        </div>

                                        <div class="border-t border-gray-200 pt-3 flex justify-between items-center">
                                            <span class="text-xs text-gray-500">Mulai dari</span>
                                            <span class="font-bold text-lg text-brand-yellow">
                                                Rp
                                                {{ number_format($event->min_price ?? ($event->price_regular ?? 0), 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-span-full">
                                <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-xl font-bold text-gray-700 mb-2">Tidak ada event ditemukan</h3>
                                    <p class="text-gray-500 mb-6">Coba ubah filter atau kata kunci pencarian Anda</p>
                                    <a href="{{ route('events.index') }}"
                                        class="inline-block bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-6 rounded-xl transition">
                                        <i class="fas fa-redo mr-2"></i> Reset Filter
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if ($events->hasPages())
                        <div class="flex justify-center">
                            <div class="bg-white rounded-xl shadow-md p-4">
                                {{ $events->links() }}
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Custom pagination styling */
        .pagination {
            display: flex;
            gap: 0.5rem;
        }

        .pagination .page-link {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }

        .pagination .page-item.active .page-link {
            background-color: #FCD34D;
            color: #000;
            font-weight: bold;
        }
    </style>
@endpush
