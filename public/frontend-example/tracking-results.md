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
                            <i class="fas fa-user mr-2"></i>
                            {{ $customer->full_name }}
                            <span class="text-gray-400">|</span>
                            <i class="fas fa-id-card ml-2 mr-2"></i>
                            {{ $customer->identity_number }}
                        </p>
                    </div>
                    <a href="{{ route('tracking.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-search mr-2"></i> Cari Lagi
                    </a>
                </div>
            </div>

            <!-- Orders List -->
            <div class="space-y-4">
                @foreach ($orders as $order)
                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                        <div class="p-6">
                            <!-- Order Header -->
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                                <div class="mb-4 md:mb-0">
                                    <h3 class="text-xl font-bold text-slate-900 mb-1">
                                        {{ $order->event->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-receipt mr-1"></i>
                                        Order: <span class="font-mono">{{ $order->order_number }}</span>
                                    </p>
                                </div>

                                <!-- Status Badge -->
                                <div>
                                    @if ($order->payment_status === 'paid')
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-2"></i> Lunas
                                        </span>
                                    @elseif($order->payment_status === 'pending')
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-2"></i> Menunggu Pembayaran
                                        </span>
                                    @elseif($order->payment_status === 'expired')
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-2"></i> Kadaluarsa
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gray-100 text-gray-800">
                                            <i class="fas fa-ban mr-2"></i> {{ ucfirst($order->payment_status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Order Details -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Tanggal Pemesanan</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Total Tiket</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $order->orderItems->sum('quantity') }} tiket
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 mb-1">Total Bayar</p>
                                    <p class="text-lg font-bold text-brand-yellow">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <!-- Tickets -->
                            <div class="mb-4">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Detail Tiket:</p>
                                <div class="space-y-1">
                                    @foreach ($order->orderItems as $item)
                                        <div class="flex justify-between text-sm bg-gray-50 px-3 py-2 rounded">
                                            <span class="text-gray-700">{{ $item->ticketType->name }}</span>
                                            <span class="font-semibold text-gray-900">{{ $item->quantity }}x</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col sm:flex-row gap-3">
                                @if ($order->payment_status === 'paid')
                                    <a href="{{ route('tracking.download-invoice', $order->order_number) }}"
                                        class="flex-1 text-center bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-4 rounded-lg transition transform hover:scale-105">
                                        <i class="fas fa-download mr-2"></i> Download Invoice
                                    </a>
                                    <a href="{{ route('tracking.detail', $order->order_number) }}"
                                        class="flex-1 text-center bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-4 rounded-lg transition">
                                        <i class="fas fa-eye mr-2"></i> Lihat Detail
                                    </a>
                                @elseif($order->payment_status === 'pending')
                                    <a href="{{ route('payment.waiting', $order->order_number) }}"
                                        class="flex-1 text-center bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-4 rounded-lg transition transform hover:scale-105">
                                        <i class="fas fa-credit-card mr-2"></i> Lanjutkan Pembayaran
                                    </a>
                                    <a href="{{ route('tracking.detail', $order->order_number) }}"
                                        class="flex-1 text-center bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-4 rounded-lg transition">
                                        <i class="fas fa-eye mr-2"></i> Lihat Detail
                                    </a>
                                @else
                                    <a href="{{ route('tracking.detail', $order->order_number) }}"
                                        class="flex-1 text-center bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-4 rounded-lg transition">
                                        <i class="fas fa-eye mr-2"></i> Lihat Detail
                                    </a>
                                    <a href="{{ route('events.show', $order->event->slug) }}"
                                        class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-lg transition">
                                        <i class="fas fa-redo mr-2"></i> Pesan Ulang
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Expiry Warning for Pending Orders -->
                        @if ($order->payment_status === 'pending' && $order->expired_at)
                            <div class="bg-yellow-50 border-t-2 border-yellow-200 px-6 py-3">
                                <p class="text-sm text-yellow-800">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Batas Pembayaran:</strong> {{ $order->expired_at->format('d M Y, H:i') }} WIB
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Info Box -->
            <div class="mt-8 bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 text-2xl mt-1 mr-4"></i>
                    <div>
                        <h3 class="font-bold text-blue-900 mb-2">Informasi Penting</h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Invoice dengan QR code diperlukan untuk menukar gelang tiket di lokasi event</li>
                            <li>• Pastikan membawa identitas asli yang sesuai dengan data pemesanan</li>
                            <li>• Pesanan pending akan otomatis dibatalkan setelah 24 jam</li>
                            <li>• Hubungi customer service jika ada kendala</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
