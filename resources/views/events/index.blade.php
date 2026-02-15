@extends('layouts.app')

@section('title', 'Browse Events - Tiketin')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            border-radius: 0.75rem;
            border-color: #e2e8f0;
            padding: 0.5rem;
            height: auto;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 50%;
            transform: translateY(-50%);
        }
    </style>
@endpush

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
                        placeholder="Cari event, kota, atau tempat..."
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
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
            <div>
                <h1 class="text-5xl font-bold text-slate-900 tracking-tight">Jelajah Event</h1>
                <p class="text-lg text-gray-500 mt-2">Temukan event seru di sekitarmu</p>
            </div>

            {{-- Search Bar --}}
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
                    class="w-full pl-6 pr-12 py-4 rounded-xl border-2 border-gray-100 focus:outline-none focus:border-brand-yellow focus:ring-4 focus:ring-brand-yellow/5 transition shadow-sm bg-white text-slate-900"
                    placeholder="Cari event, artis, lokasi...">
                <i class="fas fa-search absolute right-6 top-1/2 -translate-y-1/2 text-gray-400"></i>
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

                @if (request('city'))
                    <span
                        class="inline-flex items-center bg-white text-green-700 text-xs font-bold px-4 py-2 rounded-xl border border-green-200 shadow-sm">
                        <i class="fas fa-map-marker-alt text-[10px] mr-2 opacity-50"></i>
                        {{ \App\Models\City::find(request('city'))?->name }}
                        <a href="{{ route('events.index', request()->except('city')) }}"
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
            <aside class="lg:w-64 flex-shrink-0">
                <form action="{{ route('events.index') }}" method="GET" id="filterForm">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                                <i class="fas fa-filter text-brand-yellow"></i> Filter
                            </h3>
                            @if (request()->hasAny(['category', 'location', 'price_range', 'period']))
                                <a href="{{ route('events.index', request()->only('search')) }}"
                                    class="text-xs font-bold text-red-500 hover:text-red-600 transition-colors">
                                    Reset
                                </a>
                            @endif
                        </div>

                        {{-- Category --}}
                        <div class="mb-6 pb-6 border-b border-gray-100">
                            <h4 class="font-bold text-sm text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-tags text-brand-yellow text-xs"></i> Kategori
                            </h4>
                            <div class="space-y-3">
                                @foreach ($categories as $cat)
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="checkbox" name="category[]" value="{{ $cat->slug }}"
                                            {{ in_array($cat->slug, (array) request('category', [])) ? 'checked' : '' }}
                                            onchange="document.getElementById('filterForm').submit()"
                                            class="w-4 h-4 rounded border-gray-300 text-brand-yellow focus:ring-brand-yellow transition-all">
                                        <span
                                            class="ml-3 text-sm text-gray-700 group-hover:text-brand-yellow transition-colors flex items-center gap-2">
                                            <i class="{{ $cat->icon }} text-xs opacity-60"></i>
                                            {{ $cat->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- City Search (Select2) --}}
                        <div class="mb-6 pb-6 border-b border-gray-100">
                            <h4 class="font-bold text-sm text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-brand-yellow text-xs"></i> Lokasi (Kota)
                            </h4>
                            <select name="city" id="citySearch"
                                class="w-full border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-brand-yellow transition bg-white text-gray-700">
                                <option value="">Cari Kota...</option>
                                @if (request('city'))
                                    <option value="{{ request('city') }}" selected>
                                        {{ \App\Models\City::find(request('city'))?->name }}
                                    </option>
                                @endif
                            </select>
                        </div>

                        {{-- Price Range --}}
                        <div class="mb-6 pb-6 border-b border-gray-100">
                            <h4 class="font-bold text-sm text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-money-bill-wave text-brand-yellow text-xs"></i> Range Harga
                            </h4>
                            <div class="space-y-3">
                                @foreach (['all' => 'Semua Harga', '0-100000' => '< Rp 100.000', '100000-300000' => 'Rp 100K - 300K', '300000-500000' => 'Rp 300K - 500K', '500000-max' => '> Rp 500.000'] as $val => $label)
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="radio" name="price_range" value="{{ $val }}"
                                            {{ request('price_range', 'all') == $val ? 'checked' : '' }}
                                            onchange="document.getElementById('filterForm').submit()"
                                            class="w-4 h-4 text-brand-yellow focus:ring-brand-yellow border-gray-200">
                                        <span
                                            class="ml-3 text-sm text-gray-700 group-hover:text-brand-yellow transition-colors">
                                            {{ $label }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Period --}}
                        <div>
                            <h4 class="font-bold text-sm text-slate-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-brand-yellow text-xs"></i> Waktu
                            </h4>
                            <select name="period"
                                class="w-full border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-brand-yellow transition bg-white text-gray-700"
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
                </form>
            </aside>

            {{-- MAIN: EVENT GRID --}}
            <div class="flex-1">
                {{-- Sort & Count Bar --}}
                <div
                    class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                    <p class="text-sm text-slate-600">
                        Menampilkan <span class="font-bold text-slate-900">{{ $events->firstItem() ?? 0 }}</span> dari
                        <span class="font-bold text-slate-900">{{ $events->total() }}</span> event
                    </p>
                    <form action="{{ route('events.index') }}" method="GET" id="sortForm"
                        class="flex items-center gap-3">
                        @foreach (request()->except('sort') as $key => $value)
                            @if (is_array($value))
                                @foreach ($value as $item)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <label for="sort"
                            class="text-sm text-slate-500 font-medium whitespace-nowrap">Urutkan:</label>
                        <select name="sort" id="sort"
                            class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-brand-yellow transition bg-white text-slate-700 min-w-[140px]"
                            onchange="document.getElementById('sortForm').submit()">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru
                            </option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga
                                Terendah</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga
                                Tertinggi</option>
                        </select>
                    </form>
                </div>

                @if ($events->count() > 0)
                    {{-- Event Cards Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                        @foreach ($events as $event)
                            @php $isPassed = $event->event_date < now(); @endphp
                            <div
                                class="group bg-white rounded-2xl shadow-sm transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col h-full {{ $isPassed ? 'opacity-70 grayscale shadow-none' : 'hover:shadow-md' }}">
                                @if ($isPassed)
                                    <div class="flex flex-col h-full relative cursor-not-allowed">
                                        <div
                                            class="absolute inset-0 z-20 flex items-center justify-center pointer-events-none">
                                            <span
                                                class="bg-slate-800 text-white font-black px-6 py-2 rounded-xl rotate-[-15deg] shadow-2xl border-2 border-white/20 uppercase tracking-widest text-sm">
                                                Sudah Berakhir
                                            </span>
                                        </div>
                                    @else
                                        <a href="{{ route('events.show', $event->slug) }}" class="flex flex-col h-full">
                                @endif
                                    {{-- Image Container --}}
                                    <div class="relative h-48 overflow-hidden">
                                        @if ($event->banner_image)
                                            <img src="{{ Storage::url($event->banner_image) }}"
                                                alt="{{ $event->name }}"
                                                class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                        @else
                                            <div
                                                class="w-full h-full bg-slate-200 group-hover:scale-110 transition duration-500">
                                            </div>
                                        @endif

                                        {{-- Date Badge (Square style per screenshot) --}}
                                        <div
                                            class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm p-2 rounded-lg text-center shadow-md min-w-[50px]">
                                            <span
                                                class="block text-lg font-bold text-slate-900 leading-none">{{ $event->event_date->format('d') }}</span>
                                            <span
                                                class="block text-[10px] uppercase font-medium text-gray-500 mt-1">{{ $event->event_date->format('M') }}</span>
                                        </div>

                                        {{-- Category Badge --}}
                                        @if ($event->eventCategory)
                                            <div
                                                class="absolute top-3 left-3 bg-brand-yellow text-slate-900 text-[10px] font-bold px-3 py-1 rounded shadow-sm">
                                                {{ $event->eventCategory->name }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="p-5 flex-1 flex flex-col">
                                        <h3
                                            class="text-lg font-bold text-slate-900 mb-4 line-clamp-2 group-hover:text-brand-yellow transition-colors min-h-[3rem] leading-snug">
                                            {{ $event->name }}
                                        </h3>

                                        <div class="space-y-2 text-[13px] text-gray-500 mb-6">
                                            @if ($event->venue)
                                                <div class="flex items-center">
                                                    <i class="fas fa-map-marker-alt w-5 text-red-500 opacity-80"></i>
                                                    <span class="ml-1 line-clamp-1">{{ $event->venue->name }}</span>
                                                </div>
                                            @endif
                                            <div class="flex items-center">
                                                <i class="fas fa-clock w-5 text-blue-500 opacity-80"></i>
                                                <span class="ml-1">{{ $event->event_date->format('H:i') }}
                                                    WIB</span>
                                            </div>
                                        </div>

                                        <div
                                            class="mt-auto flex items-center justify-between pt-4 border-t border-gray-100">
                                            <p class="text-[12px] text-gray-400 font-medium">Mulai dari</p>
                                            <p class="text-xl font-bold text-brand-yellow">
                                                @php $minPrice = $event->ticketCategories->min('price'); @endphp
                                                @if ($minPrice !== null)
                                                    <span class="text-base">Rp</span>
                                                    {{ number_format($minPrice, 0, ',', '.') }}
                                                @else
                                                    <span class="text-gray-300">TBA</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                @if ($isPassed)
                                    </div>
                                @else
                                    </a>
                                @endif
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

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#citySearch').select2({
                    ajax: {
                        url: '{{ route('events.cities') }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term, // search term
                                page: params.page
                            };
                        },
                        processResults: function(data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    },
                    placeholder: 'Cari Kota...',
                    minimumInputLength: 0,
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    $('#filterForm').submit();
                });
            });
        </script>
    @endpush
