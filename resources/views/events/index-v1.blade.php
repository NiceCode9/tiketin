@extends('layouts.app')

@section('title', 'Browse Events - Tiketin')

@section('content')
    {{-- Premium Hero Section --}}
    <div class="relative py-20 mb-12 overflow-hidden">
        {{-- Background with mesh gradient --}}
        <div class="absolute inset-0 bg-slate-900 z-0">
            <div class="absolute top-0 right-0 w-1/2 h-full bg-brand-primary opacity-20 blur-[120px] rounded-full"></div>
            <div class="absolute bottom-0 left-0 w-1/2 h-full bg-brand-secondary opacity-20 blur-[120px] rounded-full"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-5xl md:text-6xl font-black text-white mb-6 animate-fade-in tracking-tight">
                Temukan <span class="text-brand-yellow">Event</span> Favoritmu
            </h1>
            <p class="text-xl text-gray-400 mb-10 max-w-2xl mx-auto animate-slide-up">
                Jelajahi ribuan pengalaman seru dari konser, festival, hingga workshop eksklusif yang dikurasi khusus
                untukmu.
            </p>

            {{-- Search Bar --}}
            <form action="{{ route('events.index') }}" method="GET" class="max-w-2xl mx-auto animate-slide-up"
                style="animation-delay: 0.1s">
                <div class="relative group">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari event berdasarkan nama..."
                        class="w-full px-8 py-5 pr-40 rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-500 focus:ring-4 focus:ring-brand-yellow/20 focus:outline-none shadow-2xl transition-all duration-300">
                    <button type="submit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-brand-yellow hover:bg-yellow-400 text-black px-8 py-3 rounded-xl font-bold transition transform active:scale-95 shadow-lg">
                        <i class="fas fa-search mr-2"></i>
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tight">Jelajah Event</h2>
                <p class="text-gray-500 mt-2 font-medium">Temukan pengalaman seru yang dikurasi khusus untukmu</p>
            </div>

            {{-- Secondary Search Bar (Matches Reference Layout) --}}
            <form action="{{ route('events.index') }}" method="GET" class="relative w-full md:w-96 group">
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
                    class="w-full pl-12 pr-12 py-4 rounded-2xl bg-white border-2 border-gray-100 focus:outline-none focus:border-brand-primary focus:ring-4 focus:ring-brand-primary/5 transition-all shadow-sm"
                    placeholder="Cari event, artis, lokasi...">
                <i
                    class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-brand-primary transition-colors"></i>

                @if (request('search'))
                    <a href="{{ route('events.index', request()->except('search')) }}"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors">
                        <i class="fas fa-times-circle"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- Active Filters Display --}}
        @if (request()->hasAny(['search', 'category', 'location', 'price_range', 'period']))
            <div class="mb-10 flex flex-wrap gap-2 items-center p-5 bg-blue-50/50 rounded-2xl border border-blue-100/50">
                <span class="text-sm font-bold text-slate-500 mr-2 flex items-center gap-2">
                    <i class="fas fa-filter text-xs"></i> Filter Aktif:
                </span>

                @if (request('search'))
                    <span
                        class="inline-flex items-center bg-brand-yellow text-slate-900 text-xs font-bold px-4 py-2 rounded-xl border border-brand-yellow/50 shadow-sm">
                        Cari: "{{ request('search') }}"
                        <a href="{{ route('events.index', request()->except('search')) }}"
                            class="ml-3 hover:scale-110 transition-transform">
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    </span>
                @endif

                @if (request('category'))
                    @foreach ((array) request('category') as $catSlug)
                        @php $catName = $categories->where('slug', $catSlug)->first()?->name ?? $catSlug; @endphp
                        <span
                            class="inline-flex items-center bg-white text-brand-primary text-xs font-bold px-4 py-2 rounded-xl border border-brand-primary/20 shadow-sm">
                            <i class="fas fa-tag text-[10px] mr-2 opacity-50"></i>
                            {{ $catName }}
                            <a href="{{ route('events.index', array_merge(request()->except('category'), ['category' => array_diff((array) request('category', []), [$catSlug])])) }}"
                                class="ml-3 hover:text-red-500 hover:scale-110 transition-all">
                                <i class="fas fa-times text-[10px]"></i>
                            </a>
                        </span>
                    @endforeach
                @endif

                @if (request('location'))
                    <span
                        class="inline-flex items-center bg-white text-green-700 text-xs font-bold px-4 py-2 rounded-xl border border-green-200 shadow-sm">
                        <i class="fas fa-map-marker-alt text-[10px] mr-2 opacity-50"></i>
                        {{ ucfirst(request('location')) }}
                        <a href="{{ route('events.index', request()->except('location')) }}"
                            class="ml-3 hover:text-red-500 hover:scale-110 transition-all">
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    </span>
                @endif

                @if (request('price_range') && request('price_range') !== 'all')
                    <span
                        class="inline-flex items-center bg-white text-purple-700 text-xs font-bold px-4 py-2 rounded-xl border border-purple-200 shadow-sm">
                        <i class="fas fa-money-bill-wave text-[10px] mr-2 opacity-50"></i>
                        @php
                            $labels = [
                                '0-100000' => '< 100K',
                                '100000-300000' => '100K - 300K',
                                '300000-500000' => '300K - 500K',
                                '500000-999999999' => '> 500K',
                            ];
                        @endphp
                        {{ $labels[request('price_range')] ?? request('price_range') }}
                        <a href="{{ route('events.index', request()->except('price_range')) }}"
                            class="ml-3 hover:text-red-500 hover:scale-110 transition-all">
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    </span>
                @endif

                @if (request('period'))
                    <span
                        class="inline-flex items-center bg-white text-brand-secondary text-xs font-bold px-4 py-2 rounded-xl border border-brand-secondary/20 shadow-sm">
                        <i class="fas fa-calendar-alt text-[10px] mr-2 opacity-50"></i>
                        {{ ucfirst(request('period')) }}
                        <a href="{{ route('events.index', request()->except('period')) }}"
                            class="ml-3 hover:text-red-500 hover:scale-110 transition-all">
                            <i class="fas fa-times text-[10px]"></i>
                        </a>
                    </span>
                @endif

                <a href="{{ route('events.index') }}"
                    class="text-xs font-black text-red-500 hover:text-red-600 ml-4 flex items-center gap-1.5 px-3 py-2 rounded-xl hover:bg-red-50 transition-colors">
                    <i class="fas fa-trash-alt text-[10px]"></i> Hapus Semua
                </a>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- SIDEBAR: FILTER --}}
            <aside class="lg:w-80 flex-shrink-0">
                <form action="{{ route('events.index') }}" method="GET" id="filterForm" class="sticky top-24">
                    {{-- Preserve search query --}}
                    @if (request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8">
                            <div class="flex justify-between items-center mb-8">
                                <h3 class="text-xl font-black text-slate-900 flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-brand-yellow rounded-xl flex items-center justify-center shadow-sm">
                                        <i class="fas fa-sliders-h text-sm"></i>
                                    </div>
                                    Filter
                                </h3>
                                @if (request()->hasAny(['category', 'location', 'price_range', 'period']))
                                    <a href="{{ route('events.index', request()->only('search')) }}"
                                        class="text-xs font-black text-red-500 hover:underline flex items-center gap-1">
                                        <i class="fas fa-redo text-[10px]"></i> Reset
                                    </a>
                                @endif
                            </div>

                            {{-- Filter: Kategori (Checkboxes) --}}
                            <div class="mb-8 pb-8 border-b border-gray-50">
                                <h4 class="font-black text-sm text-slate-900 mb-5 flex items-center gap-2">
                                    <i class="fas fa-tags text-brand-primary text-xs"></i> Kategori
                                </h4>
                                <div class="space-y-4">
                                    @foreach ($categories as $cat)
                                        <label class="flex items-center cursor-pointer group">
                                            <div class="relative flex items-center">
                                                <input type="checkbox" name="category[]" value="{{ $cat->slug }}"
                                                    {{ in_array($cat->slug, (array) request('category', [])) ? 'checked' : '' }}
                                                    onchange="document.getElementById('filterForm').submit()"
                                                    class="w-5 h-5 rounded-lg border-2 border-gray-200 text-brand-primary focus:ring-brand-primary/20 transition-all checked:border-brand-primary">
                                            </div>
                                            <span
                                                class="ml-3 text-sm font-bold text-gray-500 group-hover:text-brand-primary transition-colors flex items-center gap-2">
                                                <i class="{{ $cat->icon }} text-[10px] opacity-40"></i>
                                                {{ $cat->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Filter: Lokasi --}}
                            <div class="mb-8 pb-8 border-b border-gray-50">
                                <h4 class="font-black text-sm text-slate-900 mb-5 flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-brand-primary text-xs"></i> Lokasi
                                </h4>
                                <select name="location"
                                    class="w-full bg-gray-50 border-2 border-transparent rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:border-brand-primary/30 transition-all outline-none shadow-inner"
                                    onchange="document.getElementById('filterForm').submit()">
                                    <option value="">Semua Lokasi</option>
                                    @foreach (['Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Bali', 'Medan', 'Semarang'] as $city)
                                        <option value="{{ strtolower($city) }}"
                                            {{ request('location') == strtolower($city) ? 'selected' : '' }}>
                                            {{ $city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter: Harga (Radios) --}}
                            <div class="mb-8 pb-8 border-b border-gray-50">
                                <h4 class="font-black text-sm text-slate-900 mb-5 flex items-center gap-2">
                                    <i class="fas fa-money-bill-wave text-brand-primary text-xs"></i> Range Harga
                                </h4>
                                <div class="space-y-4">
                                    @php
                                        $ranges = [
                                            'all' => 'Semua Harga',
                                            '0-100000' => '< Rp 100.000',
                                            '100000-300000' => 'Rp 100K - 300K',
                                            '300000-500000' => 'Rp 300K - 500K',
                                            '500000-999999999' => '> Rp 500.000',
                                        ];
                                    @endphp
                                    @foreach ($ranges as $value => $label)
                                        <label class="flex items-center cursor-pointer group">
                                            <input type="radio" name="price_range" value="{{ $value }}"
                                                {{ request('price_range', 'all') == $value ? 'checked' : '' }}
                                                onchange="document.getElementById('filterForm').submit()"
                                                class="w-5 h-5 text-brand-primary focus:ring-brand-primary/20 border-2 border-gray-200 transition-all">
                                            <span
                                                class="ml-3 text-sm font-bold text-gray-500 group-hover:text-brand-primary transition-colors">
                                                {{ $label }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Filter: Periode --}}
                            <div>
                                <h4 class="font-black text-sm text-slate-900 mb-5 flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-brand-primary text-xs"></i> Masanya
                                </h4>
                                <select name="period"
                                    class="w-full bg-gray-50 border-2 border-transparent rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:border-brand-primary/30 transition-all outline-none shadow-inner"
                                    onchange="document.getElementById('filterForm').submit()">
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
                    </div>
                </form>
            </aside>

            {{-- MAIN: EVENT GRID --}}
            <div class="flex-1">
                @if ($events->count() > 0)
                    {{-- Event Cards Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                        @foreach ($events as $event)
                            <div
                                class="group bg-white rounded-[2.5rem] shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden border border-gray-100 flex flex-col h-full">
                                <a href="{{ route('events.show', $event->slug) }}" class="flex flex-col h-full">
                                    {{-- Image Container --}}
                                    <div class="relative h-56 overflow-hidden">
                                        @if ($event->banner_image)
                                            <img src="{{ Storage::url($event->banner_image) }}"
                                                alt="{{ $event->name }}"
                                                class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                                        @else
                                            <div
                                                class="w-full h-full bg-gradient-to-br from-brand-primary to-brand-secondary group-hover:scale-110 transition duration-500">
                                            </div>
                                        @endif

                                        {{-- Date Badge --}}
                                        <div
                                            class="absolute top-4 right-4 bg-white/95 backdrop-blur-md p-2 rounded-2xl text-center shadow-lg min-w-[60px]">
                                            <span
                                                class="block text-xl font-black text-slate-900 leading-none">{{ $event->event_date->format('d') }}</span>
                                            <span
                                                class="block text-[10px] uppercase font-black text-brand-primary mt-1">{{ $event->event_date->format('M') }}</span>
                                        </div>

                                        {{-- Category Badge --}}
                                        @if ($event->eventCategory)
                                            <div class="absolute top-4 left-4">
                                                <span
                                                    class="bg-brand-yellow text-slate-900 text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest shadow-lg">
                                                    {{ $event->eventCategory->name }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="p-8 flex-1 flex flex-col">
                                        <h3
                                            class="text-xl font-black text-slate-900 mb-4 line-clamp-2 group-hover:text-brand-primary transition-colors min-h-[3.5rem] leading-tight">
                                            {{ $event->name }}
                                        </h3>

                                        <div class="space-y-3 text-sm font-bold text-slate-400 mb-8">
                                            @if ($event->venue)
                                                <div class="flex items-center">
                                                    <i class="fas fa-map-marker-alt w-5 text-brand-primary"></i>
                                                    <span class="ml-2 line-clamp-1">{{ $event->venue->name }}</span>
                                                </div>
                                            @endif
                                            <div class="flex items-center">
                                                <i class="fas fa-clock w-5 text-brand-primary"></i>
                                                <span class="ml-2">{{ $event->event_date->format('H:i') }} WIB</span>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-auto flex items-center justify-between pt-6 border-t border-gray-50">
                                            <div>
                                                <p class="text-[10px] uppercase font-black text-slate-300 mb-1">Mulai Dari
                                                </p>
                                                <p class="text-2xl font-black text-slate-900 tracking-tight">
                                                    @php $minPrice = $event->ticketCategories->min('price'); @endphp
                                                    @if ($minPrice !== null)
                                                        <span class="text-sm font-bold">Rp</span>
                                                        {{ number_format($minPrice, 0, ',', '.') }}
                                                    @else
                                                        <span class="text-slate-300">TBA</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div
                                                class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center group-hover:bg-brand-yellow transition-all duration-500 shadow-sm">
                                                <i
                                                    class="fas fa-arrow-right text-slate-300 group-hover:text-slate-900 group-hover:translate-x-1 transition-all"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="flex justify-center">
                        {{ $events->appends(request()->query())->links() }}
                    </div>
                @else
                    {{-- Empty State --}}
                    <div
                        class="text-center py-24 bg-white rounded-[3rem] border border-gray-100 shadow-sm animate-fade-in">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-8">
                            <i class="fas fa-calendar-times text-4xl text-gray-200"></i>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4">No Events Found</h3>
                        <p class="text-gray-500 font-medium mb-10 max-w-sm mx-auto">
                            Coba ubah filter atau kata kunci pencarian Anda untuk menemukan event lainnya.
                        </p>
                        <a href="{{ route('events.index') }}"
                            class="inline-flex items-center gap-3 bg-brand-yellow hover:bg-yellow-400 text-black font-black py-5 px-10 rounded-2xl transition shadow-xl hover:shadow-yellow-500/20 active:scale-95">
                            <i class="fas fa-redo"></i> Lihat Semua Event
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endsection
