@extends('layouts.app')

@section('title', 'Detail Pesanan - ' . $order->order_number)

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-4xl">

            {{-- Success Alert --}}
            @if ($order->payment_status === 'paid')
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-green-700">Pembayaran Berhasil!</p>
                            <p class="text-sm text-green-600">Tiket Anda telah dikirim ke email</p>
                        </div>
                    </div>
                </div>
            @elseif($order->payment_status === 'pending')
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-yellow-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-yellow-700">Menunggu Pembayaran</p>
                            <p class="text-sm text-yellow-600">Pesanan berlaku sampai:
                                {{ $order->expired_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            @elseif($order->isExpired())
                <div class="bg-gray-50 border-l-4 border-gray-500 p-4 mb-6 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-ban text-gray-500 text-2xl mr-3"></i>
                        <div>
                            <p class="font-bold text-gray-700">Pesanan Kadaluarsa</p>
                            <p class="text-sm text-gray-600">Pesanan ini telah melewati batas waktu pembayaran</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Order Header -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 mb-2">Detail Pesanan</h1>
                        <p class="text-gray-500">Order Number: <span
                                class="font-mono text-slate-900">{{ $order->order_number }}</span></p>
                    </div>
                    <div>
                        @php
                            $badge = $order->getStatusBadge();
                            $colorClass = match ($badge['color']) {
                                'success' => 'bg-green-100 text-green-700',
                                'warning' => 'bg-yellow-100 text-yellow-700',
                                'danger' => 'bg-red-100 text-red-700',
                                'info' => 'bg-blue-100 text-blue-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <span class="px-4 py-2 rounded-full text-sm font-bold {{ $colorClass }}">
                            {{ $badge['label'] }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Customer Info -->
                        <div>
                            <h3 class="font-bold text-slate-900 mb-3">
                                <i class="fas fa-user text-brand-yellow mr-2"></i> Informasi Pembeli
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex">
                                    <span class="text-gray-600 w-32">Nama:</span>
                                    <span class="font-semibold text-slate-900">{{ $order->customer->full_name }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-32">Email:</span>
                                    <span class="text-slate-900">{{ $order->customer->email }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-32">No. Telepon:</span>
                                    <span class="text-slate-900">{{ $order->customer->phone_number }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Event Info -->
                        <div>
                            <h3 class="font-bold text-slate-900 mb-3">
                                <i class="fas fa-calendar-alt text-brand-yellow mr-2"></i> Informasi Event
                            </h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex">
                                    <span class="text-gray-600 w-32">Event:</span>
                                    <span class="font-semibold text-slate-900">{{ $order->event->name }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-32">Tanggal:</span>
                                    <span class="text-slate-900">{{ $order->event->event_date->format('d F Y') }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-600 w-32">Lokasi:</span>
                                    <span class="text-slate-900">{{ $order->event->venue }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
                <h3 class="font-bold text-xl text-slate-900 mb-6">
                    <i class="fas fa-ticket-alt text-brand-yellow mr-2"></i> Detail Tiket
                </h3>

                <div class="space-y-4">
                    @foreach ($order->orderItems as $item)
                        <div class="border border-gray-200 rounded-xl p-5">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h4 class="font-bold text-lg text-slate-900">{{ $item->ticketType->name }}</h4>
                                    @if ($item->isWarTicket())
                                        <span
                                            class="inline-block mt-1 bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded">
                                            <i class="fas fa-fire mr-1"></i> War Ticket
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">{{ $item->quantity }} tiket</p>
                                    <p class="font-bold text-slate-900">Rp
                                        {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <div class="text-sm text-gray-600">
                                <p>Harga per tiket: Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>

                            {{-- Show Individual Tickets if paid --}}
                            @if ($order->payment_status === 'paid' && $item->tickets->count() > 0)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">Tiket Individual:</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach ($item->tickets as $ticket)
                                            <div class="bg-gray-50 rounded-lg p-3 text-xs">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-mono text-gray-600">{{ $ticket->ticket_code }}</span>
                                                    @if ($ticket->is_checked_in)
                                                        <span
                                                            class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                                                            <i class="fas fa-check mr-1"></i> Digunakan
                                                        </span>
                                                    @else
                                                        <span
                                                            class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">
                                                            <i class="fas fa-qrcode mr-1"></i> Aktif
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h3 class="font-bold text-xl text-slate-900 mb-6">
                    <i class="fas fa-receipt text-brand-yellow mr-2"></i> Ringkasan Pembayaran
                </h3>

                <div class="space-y-3 mb-6">
                    @foreach ($order->orderItems as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $item->ticketType->name }} ({{ $item->quantity }}x)</span>
                            <span class="font-semibold text-slate-900">Rp
                                {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-slate-900">Total Pembayaran</span>
                        <span class="text-2xl font-bold text-brand-yellow">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @if ($order->payment_method)
                    <div class="mt-4 pt-4 border-t border-gray-200 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Metode Pembayaran:</span>
                            <span class="font-semibold text-slate-900">{{ strtoupper($order->payment_method) }}</span>
                        </div>
                        @if ($order->payment_channel)
                            <div class="flex justify-between mt-2">
                                <span>Channel:</span>
                                <span class="font-semibold text-slate-900">{{ strtoupper($order->payment_channel) }}</span>
                            </div>
                        @endif
                        @if ($order->paid_at)
                            <div class="flex justify-between mt-2">
                                <span>Dibayar pada:</span>
                                <span
                                    class="font-semibold text-slate-900">{{ $order->paid_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('home') }}"
                    class="inline-block bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-8 rounded-xl transition text-center">
                    <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                </a>
                <a href="{{ route('events.index') }}"
                    class="inline-block bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-8 rounded-xl transition text-center">
                    <i class="fas fa-calendar mr-2"></i> Lihat Event Lain
                </a>
            </div>
        </div>
    </div>
@endsection
