@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-4xl">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('tracking.index') }}"
                    class="inline-flex items-center text-gray-600 hover:text-gray-900 transition">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Tracking
                </a>
            </div>

            <!-- Order Header -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">Detail Pesanan</h1>
                        <p class="text-gray-600">
                            <i class="fas fa-receipt mr-2"></i>
                            <span class="font-mono">{{ $order->order_number }}</span>
                        </p>
                    </div>

                    <!-- Status Badge -->
                    <div class="mt-4 md:mt-0">
                        @if ($order->payment_status === 'paid')
                            <span
                                class="inline-flex items-center px-6 py-3 rounded-full text-base font-bold bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-2"></i> LUNAS
                            </span>
                        @elseif($order->payment_status === 'pending')
                            <span
                                class="inline-flex items-center px-6 py-3 rounded-full text-base font-bold bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-2"></i> MENUNGGU PEMBAYARAN
                            </span>
                        @elseif($order->payment_status === 'expired')
                            <span
                                class="inline-flex items-center px-6 py-3 rounded-full text-base font-bold bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-2"></i> KADALUARSA
                            </span>
                        @elseif($order->payment_status === 'cancelled')
                            <span
                                class="inline-flex items-center px-6 py-3 rounded-full text-base font-bold bg-gray-100 text-gray-800">
                                <i class="fas fa-ban mr-2"></i> DIBATALKAN
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-6 py-3 rounded-full text-base font-bold bg-red-100 text-red-800">
                                <i class="fas fa-exclamation-circle mr-2"></i> {{ strtoupper($order->payment_status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                @if ($order->payment_status === 'paid')
                    <div class="flex flex-col sm:flex-row gap-3 mt-6">
                        <a href="{{ route('tracking.download-invoice', $order->order_number) }}"
                            class="flex-1 text-center bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-6 rounded-xl transition transform hover:scale-105 shadow-lg">
                            <i class="fas fa-download mr-2"></i> Download Invoice
                        </a>
                        <a href="{{ route('home') }}"
                            class="flex-1 text-center bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-6 rounded-xl transition">
                            <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                        </a>
                    </div>
                @elseif($order->payment_status === 'pending')
                    <div class="flex flex-col sm:flex-row gap-3 mt-6">
                        <a href="{{ route('payment.waiting', $order->order_number) }}"
                            class="flex-1 text-center bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-6 rounded-xl transition transform hover:scale-105 shadow-lg">
                            <i class="fas fa-credit-card mr-2"></i> Lanjutkan Pembayaran
                        </a>
                        <a href="{{ route('home') }}"
                            class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-xl transition">
                            <i class="fas fa-home mr-2"></i> Beranda
                        </a>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Event Information -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-bold text-slate-900 mb-4 flex items-center">
                            <i class="fas fa-calendar-alt text-brand-yellow mr-3"></i>
                            Informasi Event
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Nama Event</p>
                                <p class="text-lg font-bold text-slate-900">{{ $order->event->name }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Tanggal & Waktu</p>
                                    <p class="font-semibold text-slate-900">
                                        {{ \Carbon\Carbon::parse($order->event->event_date)->format('d M Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($order->event->event_date)->format('H:i') }} WIB
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Status Event</p>
                                    @if (\Carbon\Carbon::parse($order->event->event_date)->isFuture())
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                            <i class="fas fa-calendar-check mr-1"></i> Akan Datang
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                            <i class="fas fa-calendar-times mr-1"></i> Selesai
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600 mb-1">Lokasi</p>
                                <p class="font-semibold text-slate-900">
                                    <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                    {{ $order->event->location }}
                                </p>
                            </div>

                            @if ($order->event->description)
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Deskripsi</p>
                                    <p class="text-sm text-gray-700">{{ $order->event->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-xl font-bold text-slate-900 mb-4 flex items-center">
                            <i class="fas fa-ticket-alt text-brand-yellow mr-3"></i>
                            Detail Tiket
                        </h2>

                        <div class="space-y-3">
                            @foreach ($order->orderItems as $item)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex-1">
                                        <p class="font-semibold text-slate-900">{{ $item->ticketType->name }}</p>
                                        <p class="text-sm text-gray-600">
                                            Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Subtotal</p>
                                        <p class="text-lg font-bold text-slate-900">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Total -->
                        <div class="mt-4 pt-4 border-t-2 border-gray-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Tiket</p>
                                    <p class="text-lg font-bold text-slate-900">
                                        {{ $order->orderItems->sum('quantity') }} Tiket
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Total Bayar</p>
                                    <p class="text-2xl font-bold text-brand-yellow">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center">
                            <i class="fas fa-user text-brand-yellow mr-2"></i>
                            Data Pemesan
                        </h2>

                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-600 mb-1">Nama Lengkap</p>
                                <p class="font-semibold text-slate-900">{{ $order->customer->full_name }}</p>
                            </div>

                            <div>
                                <p class="text-gray-600 mb-1">Email</p>
                                <p class="font-semibold text-slate-900 break-all">{{ $order->customer->email }}</p>
                            </div>

                            <div>
                                <p class="text-gray-600 mb-1">No. Telepon</p>
                                <p class="font-semibold text-slate-900">{{ $order->customer->phone_number }}</p>
                            </div>

                            <div>
                                <p class="text-gray-600 mb-1">Jenis Identitas</p>
                                <p class="font-semibold text-slate-900">{{ strtoupper($order->customer->identity_type) }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-600 mb-1">Nomor Identitas</p>
                                <p class="font-mono font-semibold text-slate-900">{{ $order->customer->identity_number }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center">
                            <i class="fas fa-credit-card text-brand-yellow mr-2"></i>
                            Info Pembayaran
                        </h2>

                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-gray-600 mb-1">Tanggal Pesan</p>
                                <p class="font-semibold text-slate-900">
                                    {{ $order->created_at->format('d M Y, H:i') }} WIB
                                </p>
                            </div>

                            @if ($order->paid_at)
                                <div>
                                    <p class="text-gray-600 mb-1">Tanggal Bayar</p>
                                    <p class="font-semibold text-green-600">
                                        {{ $order->paid_at->format('d M Y, H:i') }} WIB
                                    </p>
                                </div>
                            @endif

                            @if ($order->expired_at && $order->payment_status === 'pending')
                                <div>
                                    <p class="text-gray-600 mb-1">Batas Pembayaran</p>
                                    <p class="font-semibold text-red-600">
                                        {{ $order->expired_at->format('d M Y, H:i') }} WIB
                                    </p>
                                    @if ($order->expired_at->isFuture())
                                        <p class="text-xs text-gray-500 mt-1">
                                            Sisa waktu: {{ $order->expired_at->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            @if ($order->transaction_id)
                                <div>
                                    <p class="text-gray-600 mb-1">Transaction ID</p>
                                    <p class="font-mono text-xs text-slate-900 break-all">{{ $order->transaction_id }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- QR Code Info (untuk order yang sudah paid) -->
                    @if ($order->payment_status === 'paid')
                        <div class="bg-brand-yellow bg-opacity-10 border-2 border-brand-yellow rounded-xl p-6">
                            <div class="text-center mb-3">
                                <i class="fas fa-qrcode text-5xl text-brand-yellow mb-3"></i>
                                <h3 class="font-bold text-slate-900 text-lg mb-2">QR Code Tersedia</h3>
                            </div>
                            <p class="text-sm text-gray-700 text-center mb-4">
                                Download invoice untuk mendapatkan QR code yang digunakan untuk menukar gelang tiket di
                                lokasi event
                            </p>
                            <a href="{{ route('tracking.download-invoice', $order->order_number) }}"
                                class="block w-full text-center bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-4 rounded-lg transition">
                                <i class="fas fa-download mr-2"></i> Download Invoice
                            </a>
                        </div>
                    @endif

                    <!-- Important Notes -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <h3 class="font-bold text-blue-900 text-sm mb-2 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Catatan Penting
                        </h3>
                        <ul class="text-xs text-blue-800 space-y-1">
                            @if ($order->payment_status === 'paid')
                                <li>• Bawa invoice dan identitas asli ke event</li>
                                <li>• QR code untuk tukar gelang tiket</li>
                                <li>• Identitas harus sesuai dengan data pesanan</li>
                            @elseif($order->payment_status === 'pending')
                                <li>• Selesaikan pembayaran sebelum expired</li>
                                <li>• Pesanan otomatis dibatalkan jika tidak dibayar</li>
                                <li>• Simpan nomor order untuk tracking</li>
                            @else
                                <li>• Pesanan tidak dapat diproses lebih lanjut</li>
                                <li>• Silakan buat pesanan baru</li>
                                <li>• Hubungi CS jika ada pertanyaan</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-white rounded-xl shadow-md p-6">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="mb-4 md:mb-0">
                        <h3 class="font-bold text-slate-900 mb-1">Butuh Bantuan?</h3>
                        <p class="text-sm text-gray-600">Tim customer service kami siap membantu Anda</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="https://wa.me/6281234567890" target="_blank"
                            class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                            <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                        </a>
                        <a href="mailto:support@example.com"
                            class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                            <i class="fas fa-envelope mr-2"></i> Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto refresh untuk pending orders
        @if ($order->payment_status === 'pending' && $order->expired_at && $order->expired_at->isFuture())
            setInterval(() => {
                // Check status setiap 30 detik
                fetch("{{ route('payment.check-status', $order->order_number) }}")
                    .then(response => response.json())
                    .then(data => {
                        if (data.status !== 'pending') {
                            // Reload page jika status berubah
                            window.location.reload();
                        }
                    });
            }, 30000);
        @endif
    </script>
@endpush
