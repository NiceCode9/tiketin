@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-5xl">
            <!-- Breadcrumbs -->
            <nav class="flex mb-8 text-sm text-gray-500 font-bold" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="hover:text-brand-yellow">Beranda</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-[10px] mx-2"></i>
                            <a href="{{ route('tracking.index') }}" class="hover:text-brand-yellow">Lacak Pesanan</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-[10px] mx-2 text-gray-300"></i>
                            <span class="text-slate-900">Detail #{{ $order->order_number }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- LEFT: Order \u0026 Ticket Info -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- Order Status Card -->
                    <div class="bg-white rounded-3xl shadow-lg overflow-hidden border border-white">
                        <div class="p-8">
                            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Status Pesanan</p>
                                    <div class="flex items-center gap-3">
                                        @if ($order->payment_status === 'success')
                                            <span class="text-2xl font-black text-green-600">Terbayar \u0026 Lunas</span>
                                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                        @elseif($order->payment_status === 'pending')
                                            <span class="text-2xl font-black text-yellow-600">Menunggu Pembayaran</span>
                                            <i class="fas fa-clock text-yellow-500 text-xl animate-pulse"></i>
                                        @else
                                            <span class="text-2xl font-black text-red-600">{{ ucfirst($order->payment_status) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Nomor Pesanan</p>
                                    <p class="text-xl font-mono font-bold text-slate-900">#{{ $order->order_number }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 py-6 border-y border-gray-50">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Metode</p>
                                    <p class="text-sm font-bold text-slate-700">{{ strtoupper($order->payment_method ?? 'N/A') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal</p>
                                    <p class="text-sm font-bold text-slate-700">{{ $order->created_at->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Email</p>
                                    <p class="text-sm font-bold text-slate-700 truncate" title="{{ $order->consumer_email }}">{{ $order->consumer_email }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total</p>
                                    <p class="text-sm font-bold text-slate-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        @if($order->payment_status === 'pending')
                        <div class="bg-yellow-50 px-8 py-4 flex items-center justify-between gap-4">
                            <p class="text-xs text-yellow-800 font-bold">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Selesaikan pembayaran sebelum {{ $order->expires_at->format('H:i') }} WIB hari ini.
                            </p>
                            <a href="{{ route('orders.checkout', $order->order_token) }}" 
                                class="bg-brand-yellow hover:bg-yellow-400 text-black text-[10px] font-black py-2 px-4 rounded-full transition transform active:scale-95 shadow-sm">
                                BAYAR SEKARANG
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- E-Tickets Section -->
                    @if ($order->payment_status === 'success')
                        <h2 class="text-xl font-black text-slate-900 ml-2">
                            <i class="fas fa-ticket-alt text-brand-yellow mr-2"></i>
                            E-Ticket Anda ({{ $order->tickets->count() }})
                        </h2>
                        
                        <div class="grid grid-cols-1 gap-6">
                            @foreach ($order->tickets as $ticket)
                                <div class="bg-white rounded-3xl shadow-lg border-2 border-slate-100 flex flex-col md:flex-row overflow-hidden relative">
                                    <!-- Ticket Decoration Circle -->
                                    <div class="absolute -left-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-gray-50 rounded-full border-r-2 border-slate-100 hidden md:block"></div>
                                    <div class="absolute -right-4 top-1/2 -translate-y-1/2 w-8 h-8 bg-gray-50 rounded-full border-l-2 border-slate-100 hidden md:block"></div>

                                    <!-- QR Code Area -->
                                    <div class="md:w-1/3 bg-slate-900 p-8 flex flex-col items-center justify-center text-center relative overflow-hidden">
                                        <div class="absolute inset-0 opacity-10">
                                            <div class="grid grid-cols-4 gap-2 rotate-45 scale-150">
                                                @for($i=0; $i<16; $i++) <i class="fas fa-ticket text-white"></i> @endfor
                                            </div>
                                        </div>
                                        
                                        <div class="bg-white p-3 rounded-2xl relative z-10 mb-4 shadow-2xl">
                                            @php
                                                $qrData = $ticket->uuid . '|' . $ticket->checksum;
                                            @endphp
                                            {{-- Use external API if library not yet loaded, but prefer the library if available --}}
                                            @if(class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
                                                {!! QrCode::size(140)->generate($qrData) !!}
                                            @else
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($qrData) }}" alt="QR Code" class="w-24 h-24 md:w-32 md:h-32">
                                            @endif
                                        </div>
                                        <p class="text-[10px] font-mono font-bold text-slate-400 relative z-10 tracking-widest">{{ substr($ticket->uuid, 0, 18) }}...</p>
                                    </div>

                                    <!-- Ticket Info Area -->
                                    <div class="flex-grow p-8">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">{{ $ticket->ticketCategory->name }}</p>
                                                <h3 class="text-xl font-bold text-slate-900 leading-tight">
                                                    {{ $order->event->name }}
                                                </h3>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-bold rounded-full border border-green-100 uppercase tracking-widest">
                                                    Valid
                                                </span>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 py-4 border-t border-gray-50 mt-4">
                                            <div>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pemilik Tiket</p>
                                                <p class="text-sm font-bold text-slate-700">{{ $ticket->consumer_name ?? $order->consumer_name }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tempat Duduk</p>
                                                <p class="text-sm font-bold text-slate-700">{{ $ticket->seat_id ? ($ticket->seat->row . $ticket->seat->number) : 'Festival (Standing)' }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-6 mt-4 pt-4 border-t border-gray-50 bg-gray-50/50 -mx-8 -mb-8 px-8 py-4">
                                            <div class="flex items-center gap-2">
                                                <i class="far fa-calendar text-brand-yellow text-xs"></i>
                                                <span class="text-[10px] font-bold text-slate-600 uppercase">{{ $order->event->event_date->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="far fa-clock text-brand-yellow text-xs"></i>
                                                <span class="text-[10px] font-bold text-slate-600 uppercase">{{ $order->event->event_date->format('H:i') }} WIB</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Unpaid State Placeholder -->
                        <div class="bg-gray-100 rounded-3xl p-12 text-center border-2 border-dashed border-gray-200">
                            <i class="fas fa-ticket-alt text-gray-300 text-6xl mb-6"></i>
                            <h3 class="text-xl font-bold text-slate-800 mb-2">E-Ticket Belum Tersedia</h3>
                            <p class="text-gray-500 max-w-sm mx-auto">Selesaikan pembayaran Anda untuk melihat dan mengunduh E-Ticket pesanan ini.</p>
                        </div>
                    @endif
                </div>

                <!-- RIGHT: Event \u0026 Support Side -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-3xl shadow-lg p-8 border border-white">
                        <h3 class="font-black text-slate-900 mb-6 border-b border-gray-50 pb-3">Informasi Acara</h3>
                        
                        <div class="relative rounded-2xl overflow-hidden mb-6 aspect-video group">
                            <img src="{{ $order->event->poster_image ? asset('storage/' . $order->event->poster_image) : 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600\u0026h=400\u0026fit=crop' }}"
                                class="w-full h-full object-cover" alt="{{ $order->event->name }}">
                        </div>

                        <div class="space-y-4 text-sm">
                            <div class="flex gap-4">
                                <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-red-500"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase">Lokasi</p>
                                    <p class="font-bold text-slate-700 leading-snug">{{ $order->event->venue->name ?? 'TBA' }}</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-500"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase">Penyelenggara</p>
                                    <p class="font-bold text-slate-700 leading-snug">{{ $order->event->organizer ?? 'Tiketin Partner' }}</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('events.show', $order->event->slug) }}" 
                            class="block w-full text-center mt-8 text-xs font-black text-brand-yellow hover:text-black hover:bg-brand-yellow border-2 border-brand-yellow px-6 py-4 rounded-2xl transition">
                            LIHAT HALAMAN ACARA
                        </a>
                    </div>

                    <!-- Promo / Support Card -->
                    <div class="bg-slate-900 rounded-3xl shadow-lg p-8 text-white relative overflow-hidden">
                        <div class="absolute -right-5 -top-5 w-24 h-24 bg-brand-yellow opacity-10 rounded-full blur-2xl"></div>
                        <h3 class="font-black text-lg mb-4 relative z-10">Punya Masalah?</h3>
                        <p class="text-xs text-slate-400 leading-relaxed mb-6 relative z-10">Jika E-Ticket tidak muncul setelah pembayaran atau data salah, silakan hubungi tim Support kami.</p>
                        <a href="https://wa.me/628123456789" 
                            class="flex items-center justify-center gap-3 w-full bg-white/10 hover:bg-white/20 text-white font-black py-4 rounded-2xl transition text-sm">
                            <i class="fab fa-whatsapp text-green-400 text-lg"></i>
                            Bantuan WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
