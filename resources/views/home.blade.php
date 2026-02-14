@extends('layouts.app')

@section('title', 'Beranda - Platform Ticketing Terpercaya')

@section('content')
    <!-- Hero Section -->
    <section class="relative min-h-[80vh] flex items-center pt-20 overflow-hidden">
        {{-- Background Image with Parallax-like effect --}}
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('bg-landing.jpg') }}" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-slate-900/80 to-transparent"></div>
        </div>

        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 w-1/3 h-full overflow-hidden pointer-events-none z-10">
            <div class="absolute -top-20 -right-20 w-96 h-96 bg-brand-yellow/20 rounded-full blur-3xl animate-pulse-slow">
            </div>
            <div class="absolute top-1/2 -right-40 w-80 h-80 bg-brand-primary/10 rounded-full blur-3xl"></div>
        </div>

        <div class="container mx-auto px-4 relative z-20">
            <div class="max-w-3xl">
                <span
                    class="inline-flex items-center gap-2 py-2 px-4 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-brand-yellow text-sm font-bold mb-8 animate-fade-in">
                    <span class="relative flex h-3 w-3">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-yellow opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-brand-yellow"></span>
                    </span>
                    Platform Ticketing #1 di Indonesia
                </span>

                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight animate-slide-up">
                    Partner Terbaik untuk <br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-yellow to-yellow-200">
                        Segala Jenis Event
                    </span>
                </h1>

                <p class="text-gray-300 text-lg md:text-xl mb-10 max-w-xl animate-slide-up" style="animation-delay: 0.1s">
                    Temukan ribuan konser, workshop, dan festival seru yang tak terlupakan. Booking tiketmu dengan mudah dan
                    aman.
                </p>

                <!-- Search Bar -->
                <div class="animate-slide-up" style="animation-delay: 0.2s">
                    <form action="{{ route('events.index') }}" method="GET"
                        class="bg-white/10 backdrop-blur-xl p-2 rounded-2xl shadow-2xl border border-white/20 flex flex-col md:flex-row items-center gap-2 group focus-within:ring-2 focus-within:ring-brand-yellow/50 transition-all duration-300">
                        <div class="flex-1 flex items-center w-full">
                            <div class="pl-6 text-brand-yellow"><i class="fas fa-search text-lg"></i></div>
                            <input type="text" name="search"
                                class="w-full px-4 py-4 text-white placeholder-gray-400 focus:outline-none bg-transparent"
                                placeholder="Cari event, artis, atau lokasi...">
                        </div>
                        <button type="submit"
                            class="w-full md:w-auto bg-brand-yellow hover:bg-yellow-400 text-black px-10 py-4 rounded-xl font-bold transition transform active:scale-95 shadow-lg">
                            Cari Sekarang
                        </button>
                    </form>

                    <div class="mt-6 flex flex-wrap gap-4 text-gray-400 text-sm">
                        <span>Populer:</span>
                        <a href="{{ route('events.index', ['search' => 'Konser']) }}"
                            class="hover:text-brand-yellow transition">Konser Musik</a>
                        <a href="{{ route('events.index', ['search' => 'Stand Up']) }}"
                            class="hover:text-brand-yellow transition">Stand Up Comedy</a>
                        <a href="{{ route('events.index', ['search' => 'Workshop']) }}"
                            class="hover:text-brand-yellow transition">Workshop Kreatif</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Section -->
    <section class="py-24 bg-white relative overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-4xl font-bold text-slate-900 mb-4">Pilih Kategori Favoritmu</h2>
                <div class="h-1.5 w-24 bg-brand-yellow mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach ($categories as $category)
                    <a href="{{ route('events.index', ['category' => $category->slug]) }}"
                        class="group relative bg-gray-50 p-8 rounded-3xl shadow-sm hover:shadow-xl transition-all duration-500 text-center border border-gray-100 hover:-translate-y-2 overflow-hidden">
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
                            <h3 class="font-bold text-slate-900 group-hover:text-white transition-colors duration-500">
                                {{ $category->name }}
                            </h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Upcoming Events Grid -->
    <section class="py-24 bg-gray-50 relative">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                <div class="max-w-xl">
                    <h2 class="text-4xl font-bold text-slate-900 mb-4">Event Mendatang</h2>
                    <p class="text-gray-500 text-lg italic">Temukan pengalaman baru yang tak terlupakan bersama orang-orang
                        tersayang.</p>
                </div>
                <a href="{{ route('events.index') }}"
                    class="group inline-flex items-center gap-2 bg-slate-900 text-white px-8 py-4 rounded-2xl font-bold hover:bg-slate-800 transition shadow-lg">
                    Lihat Semua Event
                    <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($upcomingEvents as $event)
                    <div
                        class="group bg-white rounded-[2rem] shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden border border-gray-100 flex flex-col h-full">
                        <a href="{{ route('events.show', $event->slug) }}" class="flex flex-col h-full">
                            {{-- Image Container --}}
                            <div class="relative h-64 overflow-hidden">
                                <img src="{{ $event->banner_image ? Storage::url($event->banner_image) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&h=400&fit=crop' }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition duration-700"
                                    alt="{{ $event->name }}">

                                {{-- Date Badge --}}
                                <div
                                    class="absolute top-4 right-4 bg-white/90 backdrop-blur-md p-2 rounded-2xl text-center shadow-lg min-w-[60px]">
                                    <span
                                        class="block text-xl font-bold text-slate-900 leading-none">{{ $event->event_date->format('d') }}</span>
                                    <span
                                        class="block text-[10px] uppercase font-bold text-brand-primary mt-1">{{ $event->event_date->format('M Y') }}</span>
                                </div>

                                {{-- Category Badge --}}
                                @if ($event->eventCategory)
                                    <div class="absolute top-4 left-4">
                                        <span
                                            class="bg-brand-yellow text-slate-900 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                                            {{ $event->eventCategory->name }}
                                        </span>
                                    </div>
                                @endif

                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-end p-6">
                                    <span class="text-white font-bold text-sm">Lihat Detail <i
                                            class="fas fa-arrow-right ml-2"></i></span>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-8 flex-1 flex flex-col">
                                <h3
                                    class="font-bold text-xl mb-3 leading-tight group-hover:text-brand-primary transition-colors duration-300 line-clamp-2">
                                    {{ $event->name }}
                                </h3>

                                <div class="flex items-center text-gray-500 text-sm mb-6">
                                    <i class="fas fa-map-marker-alt text-brand-primary mr-2"></i>
                                    <span
                                        class="line-clamp-1">{{ $event->venue ? $event->venue->name : 'Lokasi Segera Diumumkan' }}</span>
                                </div>

                                <div class="mt-auto pt-6 border-t border-gray-100 flex justify-between items-center">
                                    <div>
                                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Mulai Dari</p>
                                        <p class="text-xl font-black text-slate-900">
                                            @php $minPrice = $event->ticketCategories->min('price'); @endphp
                                            @if ($minPrice !== null)
                                                <span class="text-sm font-bold">Rp</span>
                                                {{ number_format($minPrice, 0, ',', '.') }}
                                            @else
                                                <span class="text-gray-400">TBA</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div
                                        class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center group-hover:bg-brand-yellow transition-colors duration-500">
                                        <i class="fas fa-ticket-alt text-gray-400 group-hover:text-slate-900"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-24 bg-white rounded-[3rem] shadow-sm animate-fade-in">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-calendar-times text-4xl text-gray-300"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-2">Belum ada event mendatang</h3>
                        <p class="text-gray-500">Pantau terus untuk koleksi event-event seru lainnya!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="text-center group">
                    <div
                        class="w-20 h-20 bg-blue-50 rounded-3xl flex items-center justify-center mx-auto mb-8 group-hover:bg-blue-600 group-hover:rotate-12 transition-all duration-500">
                        <i class="fas fa-shield-alt text-3xl text-blue-600 group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Keamanan Terjamin</h3>
                    <p class="text-gray-500 leading-relaxed">Sistem pembayaran aman dan terenkripsi menggunakan teknologi
                        Midtrans terbaru.</p>
                </div>
                <div class="text-center group">
                    <div
                        class="w-20 h-20 bg-yellow-50 rounded-3xl flex items-center justify-center mx-auto mb-8 group-hover:bg-brand-yellow group-hover:rotate-12 transition-all duration-500">
                        <i class="fas fa-bolt text-3xl text-brand-yellow group-hover:text-slate-900"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4">Proses Instan</h3>
                    <p class="text-gray-500 leading-relaxed">Dapatkan e-tiket QR Code secara instan setelah pembayaran
                        berhasil dikonfirmasi.</p>
                </div>
                <div class="text-center group">
                    <div
                        class="w-20 h-20 bg-purple-50 rounded-3xl flex items-center justify-center mx-auto mb-8 group-hover:bg-brand-secondary group-hover:rotate-12 transition-all duration-500">
                        <i class="fas fa-headset text-3xl text-brand-secondary group-hover:text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-4">24/7 Support</h3>
                    <p class="text-gray-500 leading-relaxed">Tim bantuan kami siap melayani Anda kapan saja jika menemui
                        kendala pemesanan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-slate-900"></div>
        {{-- Decorative circles --}}
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-brand-primary opacity-20 rounded-full blur-[100px]"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-brand-yellow opacity-10 rounded-full blur-[100px]"></div>

        <div class="container mx-auto px-4 relative z-10 text-center">
            <h2 class="text-4xl md:text-5xl font-black text-white mb-8 leading-tight">
                Siap Melangkah <br class="md:hidden" /> Menuju Pengalaman Baru?
            </h2>
            <p class="text-gray-400 text-lg mb-12 max-w-2xl mx-auto">
                Ribuan orang sudah mempercayakan Tiketin untuk mengelola kebahagiaan mereka. Sekarang giliran Anda!
            </p>
            <a href="{{ route('events.index') }}"
                class="inline-flex items-center gap-3 bg-brand-yellow hover:bg-yellow-400 text-black font-black py-6 px-12 rounded-2xl transition transform hover:scale-105 shadow-2xl hover:shadow-yellow-500/25">
                JELAJAHI EVENT SEKARANG
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>
@endsection
