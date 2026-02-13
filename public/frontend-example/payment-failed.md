@extends('layouts.app')

@section('title', 'Pembayaran Gagal')

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="container mx-auto px-4 max-w-2xl">
            <div class="bg-white rounded-2xl shadow-lg p-8">

                <!-- Failed Icon -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full mb-6">
                        <i class="fas fa-times-circle text-5xl text-red-600"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-3">Pembayaran Gagal</h2>

                    @if ($order->payment_status === 'expired')
                        <p class="text-gray-600 text-lg mb-2">
                            Waktu pembayaran telah habis
                        </p>
                        <p class="text-sm text-gray-500">
                            Pesanan Anda telah dibatalkan karena melebihi batas waktu pembayaran
                        </p>
                    @elseif($order->payment_status === 'cancelled')
                        <p class="text-gray-600 text-lg mb-2">
                            Pembayaran dibatalkan
                        </p>
                        <p class="text-sm text-gray-500">
                            Anda telah membatalkan proses pembayaran
                        </p>
                    @else
                        <p class="text-gray-600 text-lg mb-2">
                            Maaf, terjadi kesalahan saat memproses pembayaran
                        </p>
                        <p class="text-sm text-gray-500">
                            Silakan coba lagi atau hubungi customer service kami
                        </p>
                    @endif
                </div>

                <!-- Error Details -->
                <div class="bg-red-50 border-2 border-red-200 rounded-xl p-6 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-4 mt-1"></i>
                        <div class="flex-1">
                            <h3 class="font-bold text-red-900 mb-2">Status Pesanan</h3>
                            <div class="space-y-2 text-sm text-red-800">
                                <div class="flex justify-between">
                                    <span>Order Number:</span>
                                    <span class="font-mono">{{ $order->order_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Status:</span>
                                    <span class="font-semibold uppercase">
                                        @if ($order->payment_status === 'expired')
                                            Kadaluarsa
                                        @elseif($order->payment_status === 'cancelled')
                                            Dibatalkan
                                        @else
                                            Gagal
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Total:</span>
                                    <span class="font-semibold">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="font-bold text-sm text-gray-600 mb-4">DETAIL PESANAN</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Event</span>
                            <span class="font-semibold text-slate-900 text-right">{{ $order->event->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Event</span>
                            <span class="text-slate-900">
                                {{ \Carbon\Carbon::parse($order->event->event_date)->format('d M Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nama Pemesan</span>
                            <span class="text-slate-900">{{ $order->customer->full_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email</span>
                            <span class="text-slate-900 text-right">{{ $order->customer->email }}</span>
                        </div>

                        <div class="border-t border-gray-200 pt-3 mt-3">
                            <p class="text-gray-600 font-semibold mb-2">Tiket:</p>
                            @foreach ($order->orderItems as $item)
                                <div class="flex justify-between mb-1 pl-4">
                                    <span class="text-gray-700">
                                        {{ $item->ticketType->name }} x{{ $item->quantity }}
                                    </span>
                                    <span class="text-gray-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Possible Reasons -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-lightbulb text-yellow-600 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-yellow-800 font-semibold mb-2">Kemungkinan Penyebab:</p>
                            <ul class="text-xs text-yellow-700 space-y-1">
                                @if ($order->payment_status === 'expired')
                                    <li>• Pembayaran tidak diselesaikan dalam waktu 24 jam</li>
                                    <li>• Pesanan otomatis dibatalkan setelah melewati batas waktu</li>
                                @elseif($order->payment_status === 'cancelled')
                                    <li>• Anda membatalkan proses pembayaran</li>
                                    <li>• Popup pembayaran ditutup sebelum selesai</li>
                                @else
                                    <li>• Saldo tidak mencukupi</li>
                                    <li>• Koneksi internet terputus</li>
                                    <li>• Metode pembayaran tidak valid</li>
                                    <li>• Bank/payment gateway mengalami gangguan</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-4 mb-6">
                    <a href="{{ route('events.show', $order->event->slug) }}"
                        class="text-center bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 px-6 rounded-xl transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-redo mr-2"></i> Pesan Ulang Tiket
                    </a>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="{{ route('events.index') }}"
                            class="text-center bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-6 rounded-xl transition">
                            <i class="fas fa-calendar-alt mr-2"></i> Event Lainnya
                        </a>
                        <a href="{{ route('home') }}"
                            class="text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-xl transition">
                            <i class="fas fa-home mr-2"></i> Beranda
                        </a>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-question-circle text-blue-600 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-blue-800 font-semibold mb-2">Butuh Bantuan?</p>
                            <p class="text-xs text-blue-700 mb-3">
                                Tim customer service kami siap membantu Anda
                            </p>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <a href="https://wa.me/6281234567890" target="_blank"
                                    class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition">
                                    <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                                </a>
                                <a href="mailto:support@example.com"
                                    class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded-lg transition">
                                    <i class="fas fa-envelope mr-2"></i> Email Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
