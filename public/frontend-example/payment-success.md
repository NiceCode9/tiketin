@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="container mx-auto px-4 max-w-2xl">
            <div class="bg-white rounded-2xl shadow-lg p-8">

                <!-- Success Icon -->
                <div class="text-center mb-8">
                    <div
                        class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6 animate-bounce">
                        <i class="fas fa-check-circle text-5xl text-green-600"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-3">Pembayaran Berhasil!</h2>
                    <p class="text-gray-600 text-lg mb-2">
                        Terima kasih atas pembelian Anda
                    </p>
                    <p class="text-sm text-gray-500">
                        Invoice dengan QR code sudah siap untuk di-download
                    </p>
                </div>

                <!-- Success Details -->
                <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6 mb-6">
                    <div class="flex items-start mb-4">
                        <i class="fas fa-file-invoice text-green-600 text-2xl mr-4 mt-1"></i>
                        <div class="flex-1">
                            <h3 class="font-bold text-green-900 mb-2">Invoice Siap Diunduh</h3>
                            <p class="text-sm text-green-800 mb-3">
                                Invoice berisi QR code untuk ditukarkan dengan gelang tiket di lokasi event.
                                Pastikan membawa identitas asli yang sesuai dengan data pemesanan.
                            </p>
                            <a href="{{ route('payment.download-invoice', $order->order_number) }}"
                                class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition">
                                <i class="fas fa-download mr-2"></i> Download Invoice (PDF)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="font-bold text-sm text-gray-600 mb-4">DETAIL PESANAN</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-start">
                            <span class="text-gray-600">Order Number</span>
                            <span class="font-mono text-xs bg-white px-3 py-1 rounded border border-gray-200">
                                {{ $order->order_number }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Event</span>
                            <span class="font-semibold text-slate-900 text-right">{{ $order->event->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal Event</span>
                            <span class="text-slate-900">
                                {{ \Carbon\Carbon::parse($order->event->event_date)->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lokasi</span>
                            <span class="text-slate-900 text-right">{{ $order->event->location }}</span>
                        </div>

                        <div class="border-t border-gray-200 pt-3 mt-3">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Nama Pemesan</span>
                                <span class="text-slate-900">{{ $order->customer->full_name }}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Email</span>
                                <span class="text-slate-900">{{ $order->customer->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">No. Telepon</span>
                                <span class="text-slate-900">{{ $order->customer->phone_number }}</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-3 mt-3">
                            <p class="text-gray-600 font-semibold mb-2">Tiket yang Dibeli:</p>
                            @foreach ($order->orderItems as $item)
                                <div class="flex justify-between mb-2 pl-4">
                                    <span class="text-gray-700">
                                        {{ $item->ticketType->name }}
                                        <span class="text-gray-500">x{{ $item->quantity }}</span>
                                    </span>
                                    <span class="text-gray-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t-2 border-gray-300 pt-3 mt-3 flex justify-between items-center">
                            <span class="font-bold text-slate-900 text-base">Total Dibayar</span>
                            <span class="font-bold text-xl text-green-600">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between text-xs">
                            <span class="text-gray-500">Tanggal Pembayaran</span>
                            <span class="text-gray-700">{{ $order->paid_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>
                </div>

                <!-- Important Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-blue-800 font-semibold mb-2">Informasi Penting:</p>
                            <ul class="text-xs text-blue-700 space-y-1">
                                <li>• Download invoice dan simpan dengan baik</li>
                                <li>• Bawa invoice dan identitas asli ke lokasi event</li>
                                <li>• Scan QR Code pada invoice untuk menukar gelang tiket</li>
                                <li>• Gelang tiket adalah akses masuk ke event venue</li>
                                <li>• QR code hanya bisa digunakan sekali</li>
                                <li>• Hubungi customer service jika ada pertanyaan</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('payment.download-invoice', $order->order_number) }}"
                        class="flex-1 text-center bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 px-6 rounded-xl transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-download mr-2"></i> Download Invoice
                    </a>
                    <a href="{{ route('home') }}"
                        class="flex-1 text-center bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 px-6 rounded-xl transition shadow-lg">
                        <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                    </a>
                </div>

                <!-- Help -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        Butuh bantuan?
                        <a href="mailto:support@example.com" class="text-brand-yellow hover:underline font-semibold">
                            Hubungi Kami
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Confetti animation on page load (optional)
        window.onload = function() {
            console.log('Payment successful!');
        };
    </script>
@endpush
