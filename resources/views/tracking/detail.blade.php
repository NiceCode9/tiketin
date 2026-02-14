@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-4xl">
            <!-- Back Button -->
            <div class="mb-8">
                <a href="{{ route('tracking.results', ['id' => $order->consumer_identity_number]) }}"
                    class="inline-flex items-center gap-2 text-gray-400 hover:text-brand-primary font-bold transition">
                    <i class="fas fa-arrow-left"></i> Kembali ke Hasil Pencarian
                </a>
            </div>

            <!-- Order Header Card -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 md:p-12 mb-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Detail Pesanan</h1>
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">
                            NOMOR ORDER: <span class="font-mono text-slate-900">{{ $order->order_number }}</span>
                        </p>
                    </div>

                    <!-- Status -->
                    <div>
                        @php $status = $order->getStatusBadge(); @endphp
                        <div class="px-8 py-4 rounded-2xl text-sm font-black uppercase tracking-widest bg-{{ $status['color'] === 'success' ? 'green' : ($status['color'] === 'warning' ? 'yellow' : 'red') }}-100 text-{{ $status['color'] === 'success' ? 'green' : ($status['color'] === 'warning' ? 'yellow' : 'red') }}-800 text-center">
                            {{ $status['label'] }}
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                @if ($order->isPaid())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('orders.invoice', $order->order_token) }}"
                            class="flex items-center justify-center gap-3 bg-brand-yellow hover:bg-yellow-400 text-black font-black py-5 rounded-2xl transition transform active:scale-95 shadow-xl shadow-brand-yellow/20">
                            <i class="fas fa-download"></i> DOWNLOAD INVOICE
                        </a>
                        <a href="{{ route('home') }}"
                            class="flex items-center justify-center gap-3 bg-slate-900 hover:bg-slate-800 text-white font-black py-5 rounded-2xl transition">
                            <i class="fas fa-home"></i> BERANDA
                        </a>
                    </div>
                @elseif($order->payment_status === 'pending')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('payment.initiate', $order->order_token) }}"
                            class="flex items-center justify-center gap-3 bg-brand-yellow hover:bg-yellow-400 text-black font-black py-5 rounded-2xl transition transform active:scale-95 shadow-xl shadow-brand-yellow/20">
                            <i class="fas fa-credit-card"></i> BAYAR SEKARANG
                        </a>
                        <a href="{{ route('home') }}"
                            class="flex items-center justify-center gap-3 bg-gray-100 hover:bg-gray-200 text-slate-900 font-black py-5 rounded-2xl transition">
                            <i class="fas fa-home"></i> BERANDA
                        </a>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Details -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Event Info -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 group">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-brand-primary/10 flex items-center justify-center text-brand-primary group-hover:rotate-12 transition-transform">
                                <i class="fas fa-calendar-alt text-xl"></i>
                            </div>
                            <h2 class="text-xl font-black text-slate-900">Informasi Event</h2>
                        </div>

                        <div class="space-y-6">
                            <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Acara</p>
                                <h3 class="text-lg font-black text-slate-900">{{ $order->event->name }}</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal</p>
                                    <p class="font-black text-slate-900">{{ $order->event->event_date->format('d M Y') }}</p>
                                    <p class="text-xs font-bold text-gray-500">{{ $order->event->event_date->format('H:i') }} WIB</p>
                                </div>
                                <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Lokasi</p>
                                    <p class="font-black text-slate-900 flex items-center gap-2">
                                        <i class="fas fa-map-marker-alt text-brand-primary"></i>
                                        {{ $order->event->venue->city ?? 'Location TBA' }}
                                    </p>
                                    <p class="text-xs font-bold text-gray-500">{{ $order->event->venue->name ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Itemized Tickets -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-brand-primary/10 flex items-center justify-center text-brand-primary">
                                <i class="fas fa-ticket-alt text-xl"></i>
                            </div>
                            <h2 class="text-xl font-black text-slate-900">Detail Tiket</h2>
                        </div>

                        <div class="space-y-4 mb-8">
                            @foreach ($order->orderItems as $item)
                                <div class="flex items-center justify-between p-6 rounded-2xl bg-gray-50 hover:bg-gray-100 transition-colors">
                                    <div class="space-y-1">
                                        <p class="font-black text-slate-900">{{ $item->ticketType->name }}</p>
                                        <p class="text-xs font-bold text-gray-400">
                                            Rp {{ number_format($item->unit_price, 0, ',', '.') }} × {{ $item->quantity }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-black text-slate-900">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Grand Total -->
                        <div class="pt-8 border-t-2 border-dashed border-gray-100">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="space-y-1 text-center md:text-left">
                                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">TOTAL TIKET</p>
                                    <p class="text-xl font-black text-slate-900">{{ $order->orderItems->sum('quantity') }} Tiket</p>
                                </div>
                                <div class="space-y-1 text-center md:text-right">
                                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">TOTAL PEMBAYARAN</p>
                                    <p class="text-3xl font-black text-brand-primary">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8 pb-4 border-b border-gray-50">
                            Penerima Tiket
                        </h3>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nama</p>
                                    <p class="text-sm font-black text-slate-900 leading-tight">{{ $order->consumer_name }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Email</p>
                                    <p class="text-sm font-black text-slate-900 break-all leading-tight">{{ $order->consumer_email }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Identitas</p>
                                    <p class="text-sm font-black text-slate-900 uppercase leading-tight">{{ $order->consumer_identity_type }}: {{ $order->consumer_identity_number }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8 pb-4 border-b border-gray-50">
                            Waktu Transaksi
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="font-bold text-gray-400">Pemesanan</span>
                                <span class="font-black text-slate-900">{{ $order->created_at->format('d/m/y H:i') }}</span>
                            </div>
                            @if ($order->paid_at)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-bold text-gray-400">Pembayaran</span>
                                    <span class="font-black text-green-600">{{ $order->paid_at->format('d/m/y H:i') }}</span>
                                </div>
                            @endif
                            @if ($order->payment_status === 'pending' && $order->expires_at)
                                <div class="pt-4 border-t border-gray-50">
                                    <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-2">BATAS PEMBAYARAN</p>
                                    <p class="text-lg font-black text-red-600 leading-none">{{ $order->expires_at->format('H:i') }} WIB</p>
                                    <p class="text-[10px] font-bold text-red-400 mt-2">{{ $order->expires_at->format('d M Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Help Box -->
                    <div class="bg-brand-primary p-8 rounded-[2.5rem] text-white">
                        <h3 class="font-black text-lg mb-4">Butuh Bantuan?</h3>
                        <p class="text-sm font-bold text-white/70 mb-8 leading-relaxed">Tim CS kami siap membantu Anda 24/7 jika ada kendala.</p>
                        <a href="https://wa.me/6285190021551" target="_blank" class="block w-full text-center bg-white text-brand-primary font-black py-4 rounded-xl transition hover:bg-slate-950 hover:text-white">
                            CONTACT SUPPORT
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
