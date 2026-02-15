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
                    <div class="border-t pt-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Tentang Event Ini</h3>
                        <div class="prose prose-lg max-w-none text-gray-700">
                            {!! $event->description !!}
                        </div>
                    </div>
                @endif

                {{-- Additional Images Gallery --}}
                @if ($event->additional_images && count($event->additional_images) > 0)
                    <div class="border-t mt-10 pt-8" x-data="{ showModal: false, modalImage: '' }">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 font-display">Galeri Event</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach ($event->additional_images as $image)
                                <div class="relative aspect-square overflow-hidden rounded-xl group cursor-pointer shadow-sm hover:shadow-md transition-all border border-slate-100"
                                    @click="showModal = true; modalImage = '{{ Storage::url($image) }}'">
                                    <img src="{{ Storage::url($image) }}" alt="Event image" 
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <i class="fas fa-search-plus text-white text-2xl"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Lightbox Modal (Teleported to Body to avoid stacking issues) --}}
                        <template x-teleport="body">
                            <div x-show="showModal" 
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-md cursor-zoom-out"
                                style="display: none;"
                                @click="showModal = false"
                                @keydown.escape.window="showModal = false">
                                
                                <button @click="showModal = false" class="absolute top-8 right-8 text-white/70 hover:text-white text-4xl transition-all duration-300 z-[10000] hover:scale-110 active:scale-95">
                                    <i class="fas fa-times"></i>
                                </button>

                                <div class="relative max-w-5xl w-full max-h-[90vh] flex items-center justify-center"
                                    @click.stop
                                    x-transition:enter="transition ease-out duration-300 transform"
                                    x-transition:enter-start="scale-95 translate-y-4"
                                    x-transition:enter-end="scale-100 translate-y-0">
                                    <img :src="modalImage" alt="Preview Image" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl border-4 border-white/20 select-none cursor-default">
                                </div>
                            </div>
                        </template>
                    </div>
                @endif
            </div>
        </div>

        {{-- Venue Information --}}
        @if ($event->venue)
            <div class="card mb-8 animate-slide-up">
                <div class="p-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6 font-display">
                        <i class="fas fa-map-marked-alt text-brand-primary mr-3"></i>
                        Informasi Venue
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {{-- Venue Image --}}
                        <div class="md:col-span-1">
                            <div class="rounded-2xl overflow-hidden shadow-lg aspect-video md:aspect-square">
                                @if ($event->venue->image)
                                    <img src="{{ Storage::url($event->venue->image) }}" alt="{{ $event->venue->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                        <i class="fas fa-building text-slate-300 text-5xl"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Venue Text Info --}}
                        <div class="md:col-span-2 flex flex-col justify-center">
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $event->venue->name }}</h3>
                            <p class="text-lg text-gray-700 mb-4 flex items-start gap-2">
                                <i class="fas fa-map-marker-alt text-brand-secondary mt-1"></i>
                                {{ $event->venue->address }}, {{ $event->venue->city }}
                            </p>
                            
                            <div class="flex flex-wrap gap-4 mt-2">
                                <div class="bg-slate-50 border border-slate-100 px-4 py-2 rounded-xl flex items-center gap-2">
                                    <i class="fas fa-users text-brand-primary"></i>
                                    <span class="text-sm font-semibold">Kapasitas: {{ number_format($event->venue->capacity, 0, ',', '.') }}</span>
                                </div>
                                @if($event->venue->has_seating)
                                    <div class="bg-blue-50 border border-blue-100 px-4 py-2 rounded-xl flex items-center gap-2 text-blue-700">
                                        <i class="fas fa-couch"></i>
                                        <span class="text-sm font-semibold">Tersedia Tempat Duduk</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
        {{-- Terms and Conditions --}}
        @if ($event->terms_and_conditions)
            <div class="card mt-8 animate-slide-up">
                <div class="p-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6 font-display">
                        <i class="fas fa-file-contract text-brand-primary mr-3"></i>
                        Syarat & Ketentuan
                    </h2>
                    <div class="prose prose-slate max-w-none text-gray-600 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        {!! $event->terms_and_conditions !!}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
