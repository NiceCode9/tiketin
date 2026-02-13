@extends('layouts.app')

@section('title', 'Hasil Pencarian Pesanan')

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-5xl">
            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 mb-2">Pesanan Ditemukan</h1>
                        <p class="text-gray-600">
                            <i class="fas fa-id-card mr-2 text-brand-yellow"></i>
                            ID: <span class="font-bold text-slate-900">{{ $identityNumber }}</span>
                        </p>
                    </div>
                    <a href="{{ route('tracking.index') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-slate-600 font-bold py-2 px-6 rounded-xl transition flex items-center gap-2">
                        <i class="fas fa-search text-xs"></i> Cari Lagi
                    </a>
                </div>
            </div>

            <!-- Orders List -->
            <div class="space-y-6">
                @forelse ($orders as $order)
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                        <div class="p-6 md:p-8">
                            <!-- Order Header -->
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
                                <div>
                                    <h3 class="text-2xl font-bold text-slate-900 mb-1">
                                        {{ $order->event->name }}
                                    </h3>
                                    <div class="flex items-center gap-3 text-sm">
                                        <span class="text-gray-400">ID Pesanan:</span>
                                        <span class="font-mono font-bold bg-slate-100 px-2 py-1 rounded text-slate-700">{{ $order->order_number }}</span>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div>
                                    @if ($order->payment_status === 'success')
                                        <span class="inline-flex items-center px-5 py-2 rounded-full text-sm font-bold bg-green-50 text-green-600">
                                            <i class="fas fa-check-circle mr-2"></i> Pembayaran Berhasil
                                        </span>
                                    @elseif($order->payment_status === 'pending')
                                        <span class="inline-flex items-center px-5 py-2 rounded-full text-sm font-bold bg-yellow-50 text-yellow-600">
                                            <i class="fas fa-clock mr-2"></i> Menunggu Pembayaran
                                        </span>
                                    @elseif($order->payment_status === 'expired')
                                        <span class="inline-flex items-center px-5 py-2 rounded-full text-sm font-bold bg-red-50 text-red-600">
                                            <i class="fas fa-times-circle mr-2"></i> Kadaluarsa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-5 py-2 rounded-full text-sm font-bold bg-gray-50 text-gray-500">
                                            <i class="fas fa-ban mr-2"></i> {{ ucfirst($order->payment_status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Order Details Summary -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 p-6 bg-gray-50/50 rounded-2xl border border-gray-100">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Waktu Pemesanan</p>
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Tiket</p>
                                    <p class="text-sm font-bold text-slate-900">
                                        {{ $order->orderItems->sum('quantity') }} Tiket
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pembayaran</p>
                                    <p class="text-lg font-black text-brand-yellow">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row gap-4">
                                @if ($order->payment_status === 'success')
                                    <a href="{{ route('tracking.detail', $order->order_number) }}"
                                        class="flex-1 text-center bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 px-6 rounded-2xl transition shadow-lg">
                                        <i class="fas fa-ticket-alt mr-2 text-brand-yellow"></i> Lihat E-Ticket
                                    </a>
                                    {{-- <a href="{{ route('tracking.download-invoice', $order->order_number) }}"
                                        class="bg-white border border-gray-200 text-slate-900 font-bold py-4 px-8 rounded-2xl hover:bg-gray-50 transition">
                                        <i class="fas fa-download mr-2 text-blue-500"></i> Invoice
                                    </a> --}}
                                @elseif($order->payment_status === 'pending')
                                    <a href="{{ route('orders.checkout', $order->order_token) }}"
                                        class="flex-1 text-center bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 px-6 rounded-2xl transition transform active:scale-95 shadow-lg shadow-yellow-500/20">
                                        <i class="fas fa-credit-card mr-2"></i> Lanjutkan Pembayaran
                                    </a>
                                    <a href="{{ route('tracking.detail', $order->order_number) }}"
                                        class="text-center bg-white border border-gray-200 text-slate-700 font-bold py-4 px-8 rounded-2xl hover:bg-gray-50 transition">
                                        Detail Pesanan
                                    </a>
                                @else
                                    <a href="{{ route('tracking.detail', $order->order_number) }}"
                                        class="flex-1 text-center bg-white border border-gray-200 text-slate-700 font-bold py-4 px-6 rounded-2xl hover:bg-gray-50 transition">
                                        Detail Pesanan
                                    </a>
                                    <a href="{{ route('events.show', $order->event->slug) }}"
                                        class="flex-1 text-center bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 px-6 rounded-2xl transition">
                                        <i class="fas fa-redo mr-2"></i> Pesan Ulang
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Expiry Warning for Pending Orders -->
                        @if ($order->payment_status === 'pending' && $order->expires_at)
                            <div class="bg-yellow-50 px-8 py-3 flex items-center justify-center gap-3 border-t border-yellow-100">
                                <i class="fas fa-exclamation-circle text-yellow-600 animate-pulse text-xs"></i>
                                <span class="text-xs text-yellow-800 font-semibold">
                                    Batas pembayaran berakhir pada: <span class="font-bold underline">{{ $order->expires_at->format('d M Y, H:i') }} WIB</span>
                                </span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-3xl shadow-lg p-16 text-center">
                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-receipt text-gray-200 text-4xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900 mb-2">Tidak Ada Pesanan</h2>
                        <p class="text-gray-500 mb-10 max-w-sm mx-auto">Kami tidak menemukan riwayat pesanan untuk nomor identitas <span class="font-bold text-slate-700">{{ $identityNumber }}</span>.</p>
                        <a href="{{ route('tracking.index') }}"
                            class="inline-block bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 px-8 rounded-2xl transition">
                            <i class="fas fa-arrow-left mr-2"></i> Cari Kembali
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Help Box -->
            <div class="mt-12 bg-slate-900 border-2 border-slate-800 rounded-3xl p-8 text-white relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-brand-yellow opacity-5 rounded-full blur-3xl"></div>
                <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
                    <div class="w-16 h-16 bg-brand-yellow/10 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-question-circle text-brand-yellow text-3xl"></i>
                    </div>
                    <div class="text-center md:text-left">
                        <h3 class="font-bold text-xl mb-2">Butuh bantuan terkait pesanan Anda?</h3>
                        <p class="text-slate-400 text-sm">Jika pesanan Anda tidak muncul atau ada kendala dalam pembayaran, jangan ragu untuk menghubungi kami.</p>
                    </div>
                    <a href="https://wa.me/628123456789" class="ml-auto bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 px-8 rounded-2xl transition whitespace-nowrap">
                        WhatsApp CS
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
