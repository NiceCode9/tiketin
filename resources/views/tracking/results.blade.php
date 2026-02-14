@extends('layouts.app')

@section('title', 'Hasil Pencarian Pesanan')

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-5xl">
            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 mb-2">Pesanan Ditemukan</h1>
                        <p class="text-gray-600 flex items-center flex-wrap gap-2 text-sm md:text-base">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-user text-brand-primary"></i>
                                {{ $customer->full_name }}
                            </span>
                            <span class="hidden md:inline text-gray-300">|</span>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-id-card text-brand-primary"></i>
                                {{ $customer->identity_number }}
                            </span>
                        </p>
                    </div>
                    <a href="{{ route('tracking.index') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-slate-900 font-bold py-3 px-6 rounded-xl transition flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i> Cari Lagi
                    </a>
                </div>
            </div>

            <!-- Orders List -->
            <div class="grid gap-6">
                @foreach ($orders as $order)
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-100 group">
                        <div class="p-6 md:p-8">
                            <!-- Order Header -->
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 pb-6 border-b border-gray-50">
                                <div class="mb-4 md:mb-0">
                                    <h3 class="text-xl font-black text-slate-900 mb-2 group-hover:text-brand-primary transition-colors">
                                        {{ $order->event->name }}
                                    </h3>
                                    <div class="flex items-center gap-3">
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                            Order: <span class="font-mono text-slate-900">{{ $order->order_number }}</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div>
                                    @php $status = $order->getStatusBadge(); @endphp
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-xs font-black uppercase tracking-tighter bg-{{ $status['color'] === 'success' ? 'green' : ($status['color'] === 'warning' ? 'yellow' : 'red') }}-100 text-{{ $status['color'] === 'success' ? 'green' : ($status['color'] === 'warning' ? 'yellow' : 'red') }}-800">
                                        <i class="fas {{ $status['color'] === 'success' ? 'fa-check-circle' : ($status['color'] === 'warning' ? 'fa-clock' : 'fa-times-circle') }} mr-2"></i>
                                        {{ $status['label'] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Order Details -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Waktu Pesan</p>
                                    <p class="font-bold text-slate-900">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Total Tiket</p>
                                    <p class="font-bold text-slate-900">
                                        {{ $order->orderItems->sum('quantity') }} tiket
                                    </p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Total Bayar</p>
                                    <p class="text-2xl font-black text-brand-primary">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Tickets Summary -->
                            <div class="mb-8 p-4 bg-gray-50 rounded-xl space-y-2">
                                @foreach ($order->orderItems as $item)
                                    <div class="flex justify-between items-center text-sm font-bold">
                                        <span class="text-gray-500">{{ $item->ticketType->name }}</span>
                                        <span class="text-slate-900">{{ $item->quantity }}x</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row gap-4">
                                @if ($order->isPaid())
                                    <a href="{{ route('orders.invoice', $order->order_token) }}"
                                        class="flex-1 text-center bg-brand-yellow hover:bg-yellow-400 text-black font-black py-4 rounded-xl transition transform hover:scale-105 shadow-xl">
                                        <i class="fas fa-download mr-2"></i> DOWNLOAD INVOICE
                                    </a>
                                    <a href="{{ route('tracking.show', $order->order_number) }}"
                                        class="flex-1 text-center bg-slate-900 hover:bg-slate-800 text-white font-black py-4 rounded-xl transition">
                                        <i class="fas fa-eye mr-2"></i> LIHAT DETAIL
                                    </a>
                                @elseif($order->payment_status === 'pending')
                                    <a href="{{ route('orders.checkout', $order->order_number) }}"
                                        class="flex-1 text-center bg-brand-yellow hover:bg-yellow-400 text-black font-black py-4 rounded-xl transition transform hover:scale-105 shadow-xl">
                                        <i class="fas fa-credit-card mr-2"></i> LANJUTKAN PEMBAYARAN
                                    </a>
                                    <a href="{{ route('tracking.show', $order->order_number) }}"
                                        class="flex-1 text-center bg-slate-900 hover:bg-slate-800 text-white font-black py-4 rounded-xl transition">
                                        <i class="fas fa-eye mr-2"></i> LIHAT DETAIL
                                    </a>
                                @else
                                    <a href="{{ route('tracking.show', $order->order_number) }}"
                                        class="flex-1 text-center bg-slate-900 hover:bg-slate-800 text-white font-black py-4 rounded-xl transition">
                                        <i class="fas fa-eye mr-2"></i> LIHAT DETAIL
                                    </a>
                                    <a href="{{ route('events.show', $order->event->slug) }}"
                                        class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-slate-900 font-black py-4 rounded-xl transition">
                                        <i class="fas fa-redo mr-2"></i> PESAN ULANG
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Expiry Warning for Pending Orders -->
                        @if ($order->payment_status === 'pending' && $order->expires_at)
                            <div class="bg-yellow-50 px-6 py-4 flex items-center justify-center gap-3 border-t border-yellow-100">
                                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                <span class="text-xs font-bold text-yellow-800 uppercase tracking-widest">
                                    Batas Pembayaran: {{ $order->expires_at->format('d M Y, H:i') }} WIB
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Info Box -->
            <div class="mt-12 bg-brand-primary/5 border border-brand-primary/10 rounded-2xl p-8">
                <div class="flex items-start gap-4">
                    <div class="bg-brand-primary/10 p-3 rounded-xl">
                        <i class="fas fa-info-circle text-brand-primary text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 mb-3">Informasi Penting</h3>
                        <ul class="text-sm text-gray-600 space-y-2 font-medium">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                Invoice dengan QR code diperlukan untuk menukar gelang tiket di lokasi event
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                Pastikan membawa identitas asli yang sesuai dengan data pemesanan
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                Pesanan yang belum dibayar akan otomatis dibatalkan setelah batas waktu berakhir
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
