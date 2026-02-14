@extends('layouts.app')

@section('title', 'Beranda - Platform Ticketing Terpercaya')

@push('styles')
    <style>
        .ticket-shape {
            position: relative;
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
        }

        .ticket-shape::after {
            content: '';
            position: absolute;
            left: -10px;
            top: 70%;
            width: 20px;
            height: 20px;
            background: #f9fafb;
            /* Same as section bg */
            border-radius: 50%;
            z-index: 10;
        }

        .ticket-shape::before {
            content: '';
            position: absolute;
            right: -10px;
            top: 70%;
            width: 20px;
            height: 20px;
            background: #f9fafb;
            /* Same as section bg */
            border-radius: 50%;
            z-index: 10;
        }

        .ticket-divider {
            position: absolute;
            top: 70%;
            left: 10px;
            right: 10px;
            border-top: 2px dashed #f3f4f6;
            margin-top: 10px;
        }

        .featured-slider-dot.active {
            background-color: #FACC15;
            width: 2rem;
        }
    </style>
@endpush

@section('content')
    <!-- Hero Spotlight Section -->
    <section class="relative bg-slate-900 pt-32 pb-20 overflow-hidden" x-data="{ activeSlide: 0, slidesCount: {{ $featuredEvents->count() }} }">
        {{-- Background Image --}}
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('bg-landing.jpg') }}" class="w-full h-full object-cover opacity-50" alt="Background">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-900/40 to-transparent"></div>
            {{-- Secondary Glows --}}
            <div class="absolute top-0 right-0 w-1/3 h-full bg-brand-primary/20 blur-[120px] rounded-full"></div>
            <div class="absolute bottom-0 left-0 w-1/4 h-2/3 bg-brand-secondary/10 blur-[100px] rounded-full"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                {{-- Left Content: Headline & Search --}}
                <div class="lg:col-span-5 space-y-8">
                    {{-- <div
                        class="inline-flex items-center gap-2 py-2 px-4 rounded-full bg-white/5 border border-white/10 text-brand-yellow text-xs font-black tracking-widest uppercase animate-fade-in">
                        <span class="w-2 h-2 rounded-full bg-brand-yellow animate-ping"></span>
                        Platform Tiket Terbesar
                    </div> --}}

                    <h1 class="text-5xl md:text-7xl font-black text-white leading-[1.1] animate-slide-up">
                        Platform <br>
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-brand-yellow to-yellow-200">Terbaik</span>
                        <br>
                        Dalam Segala Jenis Event
                    </h1>

                    <p class="text-gray-400 text-lg md:text-xl max-w-md leading-relaxed animate-slide-up"
                        style="animation-delay: 0.1s">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-yellow to-yellow-200">Smarter
                            Ticketing.</span> Smarter Event.
                    </p>

                    {{-- Dynamic Search Bar --}}
                    <div class="animate-slide-up" style="animation-delay: 0.2s">
                        <form action="{{ route('events.index') }}" method="GET" class="relative group">
                            <input type="text" name="search" placeholder="Cari event, artis, atau lokasi..."
                                class="w-full bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl py-5 px-8 pr-36 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-yellow/50 transition-all duration-300">
                            <button type="submit"
                                class="absolute right-2 top-2 bottom-2 bg-brand-yellow hover:bg-yellow-400 text-black px-8 rounded-xl font-black text-sm transition-all transform active:scale-95">
                                CARI
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Right Content: Spotlight Slider --}}
                <div class="lg:col-span-7 relative group">
                    <div class="relative rounded-[2.5rem] overflow-hidden aspect-[16/9] shadow-2xl border border-white/5">
                        @foreach ($featuredEvents as $index => $event)
                            <div class="absolute inset-0 transition-all duration-700 ease-out transform"
                                x-show="activeSlide === {{ $index }}" x-transition:enter="opacity-0 translate-x-12"
                                x-transition:enter-start="opacity-0 translate-x-12"
                                x-transition:enter-end="opacity-100 translate-x-0"
                                x-transition:leave="opacity-100 translate-x-0"
                                x-transition:leave-start="opacity-100 translate-x-0"
                                x-transition:leave-end="opacity-0 -translate-x-12">

                                <img src="{{ $event->banner_image ? Storage::url($event->banner_image) : 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=800&q=80' }}"
                                    class="w-full h-full object-cover" alt="{{ $event->name }}">

                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent">
                                </div>

                                <div class="absolute bottom-0 left-0 right-0 p-10 space-y-4">
                                    <span
                                        class="bg-brand-yellow text-black text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-tighter">
                                        {{ $event->eventCategory->name ?? 'FEATURED' }}
                                    </span>
                                    <h3
                                        class="text-3xl font-black text-white hover:text-brand-yellow transition-colors cursor-pointer">
                                        <a href="{{ route('events.show', $event->slug) }}">{{ $event->name }}</a>
                                    </h3>
                                    <div class="flex items-center gap-6 text-gray-300 text-sm">
                                        <span class="flex items-center gap-2"><i
                                                class="fas fa-map-marker-alt text-brand-yellow"></i>
                                            {{ $event->venue->city ?? 'Location TBA' }}</span>
                                        <span class="flex items-center gap-2"><i
                                                class="fas fa-calendar-alt text-brand-yellow"></i>
                                            {{ $event->event_date->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Slider Navigation --}}
                    <div class="flex justify-center mt-6 gap-2">
                        @foreach ($featuredEvents as $index => $event)
                            <button @click="activeSlide = {{ $index }}"
                                :class="{ 'active': activeSlide === {{ $index }} }"
                                class="featured-slider-dot w-3 h-3 rounded-full bg-white/20 hover:bg-white/40 transition-all duration-300">
                            </button>
                        @endforeach
                    </div>

                    {{-- Control Arrows (Optional visibility) --}}
                    <button @click="activeSlide = (activeSlide === 0) ? slidesCount - 1 : activeSlide - 1"
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-black/20 hover:bg-black/40 backdrop-blur-md rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button @click="activeSlide = (activeSlide === slidesCount - 1) ? 0 : activeSlide + 1"
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-black/20 hover:bg-black/40 backdrop-blur-md rounded-full text-white opacity-0 group-hover:opacity-100 transition-opacity">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Category Section (Reverted to iconic style) --}}
    <section class="py-24 bg-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-4xl font-black text-slate-900 mb-4 tracking-tight">Pilih Kategori Favoritmu</h2>
                <div class="h-1.5 w-24 bg-brand-yellow mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach ($categories as $category)
                    <a href="{{ route('events.index', ['category' => $category->slug]) }}"
                        class="group relative bg-gray-50 p-8 rounded-[2rem] shadow-sm hover:shadow-xl transition-all duration-500 text-center border border-gray-100 hover:-translate-y-2 overflow-hidden">
                        {{-- Hover Effect Background --}}
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-brand-primary to-brand-secondary opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        </div>

                        <div class="relative z-10">
                            <div
                                class="w-16 h-16 mx-auto mb-4 bg-white rounded-2xl flex items-center justify-center shadow-sm group-hover:bg-white/20 group-hover:rotate-12 transition-all duration-500">
                                <i
                                    class="{{ $category->icon ?: 'fas fa-star' }} text-brand-primary text-2xl group-hover:text-white transition-colors duration-500"></i>
                            </div>
                            <h3
                                class="font-bold text-slate-900 group-hover:text-white transition-colors duration-500 text-sm">
                                {{ $category->name }}
                            </h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Trust Stats --}}
    {{-- <section class="py-16 border-y border-gray-100 bg-gray-50/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center space-y-2">
                    <p class="text-4xl font-black text-slate-900">500+</p>
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-primary">Event Aktif</p>
                </div>
                <div class="text-center space-y-2">
                    <p class="text-4xl font-black text-slate-900">10k+</p>
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-primary">Pembeli Puas</p>
                </div>
                <div class="text-center space-y-2">
                    <p class="text-4xl font-black text-slate-900">100%</p>
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-primary">Sistem Aman</p>
                </div>
                <div class="text-center space-y-2">
                    <p class="text-4xl font-black text-slate-900">24/7</p>
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-primary">Layanan CS</p>
                </div>
            </div>
        </div>
    </section> --}}

    {{-- Trending & Upcoming Events --}}
    <section class="py-24 bg-gray-50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                <div class="max-w-xl">
                    <h2
                        class="text-4xl font-black text-slate-900 tracking-tight underline decoration-brand-yellow decoration-4 underline-offset-8">
                        Trending Pekan Ini</h2>
                    <p class="text-gray-500 text-lg mt-4 italic">Event paling dinanti yang tidak boleh Anda lewatkan.</p>
                </div>
                <a href="{{ route('events.index') }}"
                    class="group inline-flex items-center gap-2 bg-slate-900 text-white px-10 py-4 rounded-2xl font-black hover:bg-slate-800 transition shadow-xl">
                    LIHAT SEMUA
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($upcomingEvents as $event)
                    <div
                        class="group h-full flex flex-col ticket-shape shadow-sm hover:shadow-2xl transition-all duration-500">
                        <a href="{{ route('events.show', $event->slug) }}" class="flex flex-col h-full relative">
                            {{-- Image --}}
                            <div class="relative h-60 overflow-hidden">
                                <img src="{{ $event->banner_image ? Storage::url($event->banner_image) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&q=80' }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-700"
                                    alt="{{ $event->name }}">

                                <div class="absolute top-4 left-4">
                                    <span
                                        class="bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-black px-3 py-1 rounded-lg uppercase">
                                        {{ $event->eventCategory->name ?? 'EVENT' }}
                                    </span>
                                </div>

                                {{-- Date Float --}}
                                <div
                                    class="absolute bottom-4 right-4 bg-white p-2 rounded-xl text-center shadow-lg min-w-[50px]">
                                    <span
                                        class="block text-lg font-black text-slate-900 leading-none">{{ $event->event_date->format('d') }}</span>
                                    <span
                                        class="block text-[8px] uppercase font-black text-brand-primary mt-1">{{ $event->event_date->format('M Y') }}</span>
                                </div>
                            </div>

                            <div class="ticket-divider"></div>

                            {{-- Content --}}
                            <div class="p-6 flex-1 flex flex-col pt-10">
                                <h3
                                    class="font-black text-lg mb-3 leading-tight group-hover:text-brand-primary transition-colors duration-300 line-clamp-2">
                                    {{ $event->name }}
                                </h3>

                                <div class="flex items-center text-gray-400 text-xs mb-6">
                                    <i class="fas fa-map-marker-alt text-brand-yellow mr-2"></i>
                                    <span
                                        class="line-clamp-1 italic">{{ $event->venue ? $event->venue->city : 'Lokasi Segera Diumumkan' }}</span>
                                </div>

                                <div class="mt-auto flex justify-between items-end">
                                    <div class="space-y-1">
                                        <p class="text-[9px] uppercase font-black text-gray-300 tracking-tighter">EST.
                                            PRICE</p>
                                        <p class="text-xl font-black text-slate-800">
                                            @php $minPrice = $event->ticketCategories->min('price'); @endphp
                                            @if ($minPrice !== null)
                                                <span class="text-xs font-bold">IDR</span>
                                                {{ number_format($minPrice, 0, ',', '.') }}
                                            @else
                                                <span class="text-gray-300">TBA</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div
                                        class="w-10 h-10 rounded-full border border-gray-100 flex items-center justify-center group-hover:bg-brand-yellow group-hover:border-transparent transition-all">
                                        <i class="fas fa-ticket-alt text-gray-300 group-hover:text-black"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-24 bg-white rounded-[3rem] shadow-sm">
                        <i class="fas fa-calendar-times text-4xl text-gray-200 mb-6"></i>
                        <h3 class="text-2xl font-bold text-slate-900">Belum ada event mendatang</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Marketing CTA Section -->
    <section class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-slate-950"></div>
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-primary opacity-20 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-brand-yellow opacity-10 rounded-full blur-[100px]"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div
                class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-[3rem] p-12 md:p-20 border border-white/5 flex flex-col items-center text-center shadow-2xl">
                <h2 class="text-4xl md:text-6xl font-black text-white mb-8 max-w-3xl leading-tight">
                    Jangan Lewatkan <br> Barisan <span class="text-brand-yellow">Kesenangan</span> Berikutnya
                </h2>
                <p class="text-gray-400 text-lg mb-12 max-w-xl">
                    Gabung dengan ribuan pengguna lain dan dapatkan akses prioritas ke tiket-tiket eksklusif.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 w-full justify-center">
                    <a href="{{ route('events.index') }}"
                        class="bg-brand-yellow hover:bg-yellow-400 text-black font-black py-6 px-12 rounded-2xl transition transform hover:scale-105 shadow-2xl hover:shadow-yellow-500/25">
                        JELAJAHI EVENT
                    </a>
                    <a href="#"
                        class="bg-white/5 hover:bg-white/10 text-white border border-white/10 font-bold py-6 px-12 rounded-2xl transition">
                        BANTUAN CS
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
