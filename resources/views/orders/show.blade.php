@extends('layouts.app')

@section('title', 'Status Pesanan - ' . $order->order_number)

@section('content')
    <div class="py-20 bg-gray-50 min-h-[80vh] flex items-center justify-center">
        <div class="container mx-auto px-4 max-w-2xl">
            
            {{-- SUCCESS STATE --}}
            @if ($order->payment_status === 'success')
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-white text-center">
                <div class="bg-green-500 px-8 py-12 relative">
                    <div class="absolute inset-0 opacity-10">
                        <div class="grid grid-cols-6 gap-4 rotate-12 scale-150">
                            @for($i=0; $i<24; $i++) <i class="fas fa-check-circle text-white text-xl"></i> @endfor
                        </div>
                    </div>
                    <div class="relative z-10">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg animate-bounce">
                            <i class="fas fa-check text-green-500 text-4xl"></i>
                        </div>
                        <h1 class="text-3xl font-black text-white mb-2 tracking-tight">Pembayaran Berhasil!</h1>
                        <p class="text-white/80 font-bold text-sm">Terima kasih, tiket Anda telah kami amankan.</p>
                    </div>
                </div>

                <div class="p-8 md:p-12">
                    <div class="bg-gray-50 rounded-2xl p-6 mb-8 text-left border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Ringkasan Pesanan</p>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Nomor Pesanan</span>
                                <span class="font-mono font-bold text-slate-900">#{{ $order->order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Event</span>
                                <span class="font-bold text-slate-700 text-sm">{{ $order->event->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-sm">Metode Bayar</span>
                                <span class="font-bold text-slate-700 text-sm uppercase">{{ $order->payment_method }}</span>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-gray-200">
                                <span class="font-bold text-slate-900">Total Dibayar</span>
                                <span class="font-black text-green-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4">
                        <a href="{{ route('tracking.detail', $order->order_number) }}" 
                            class="w-full bg-slate-900 hover:bg-slate-800 text-white font-black py-5 rounded-2xl transition transform active:scale-95 shadow-lg flex items-center justify-center gap-3">
                            <i class="fas fa-ticket-alt text-brand-yellow"></i> LIHAT E-TICKET SEKARANG
                        </a>
                        <a href="{{ route('home') }}" 
                            class="w-full bg-white border-2 border-slate-100 text-slate-400 font-bold py-4 rounded-2xl hover:bg-slate-50 hover:text-slate-600 transition">
                            KEMBALI KE BERANDA
                        </a>
                    </div>

                    <p class="mt-8 text-xs text-gray-400 leading-relaxed">
                        <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                        E-Ticket juga telah dikirimkan ke email <span class="font-bold text-slate-600">{{ $order->consumer_email }}</span>. Silakan cek folder Inbox atau Spam Anda.
                    </p>
                </div>
            </div>

            {{-- FAILED / CANCELLED STATE --}}
            @elseif(in_array($order->payment_status, ['failed', 'cancelled', 'expired']))
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-white text-center">
                <div class="bg-red-500 px-8 py-12 relative">
                    <div class="absolute inset-0 opacity-10">
                        <div class="grid grid-cols-6 gap-4 rotate-12 scale-150">
                            @for($i=0; $i<24; $i++) <i class="fas fa-times-circle text-white text-xl"></i> @endfor
                        </div>
                    </div>
                    <div class="relative z-10">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i class="fas fa-times text-red-500 text-4xl"></i>
                        </div>
                        <h1 class="text-3xl font-black text-white mb-2 tracking-tight">
                            @if($order->payment_status === 'expired') Waktu Habis! @else Pembayaran Gagal @endif
                        </h1>
                        <p class="text-white/80 font-bold text-sm">
                            @if($order->payment_status === 'expired') Batas waktu pembayaran untuk pesanan ini telah berakhir. @else Mohon maaf, transaksi Anda tidak dapat diproses. @endif
                        </p>
                    </div>
                </div>

                <div class="p-8 md:p-12">
                    <div class="bg-red-50 rounded-2xl p-8 mb-8 text-left border border-red-100 flex items-start gap-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-xl mt-1"></i>
                        <div>
                            <h4 class="font-black text-red-900 mb-1">Kenapa ini terjadi?</h4>
                            <p class="text-sm text-red-700/80 leading-relaxed">
                                @if($order->payment_status === 'expired') Pesanan otomatis dibatalkan oleh sistem karena pembayaran tidak diterima dalam batas waktu yang ditentukan. @else Transaksi ditolak oleh bank atau penyedia layanan pembayaran Anda. @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4">
                        <a href="{{ route('events.show', $order->event->slug) }}" 
                            class="w-full bg-brand-yellow hover:bg-yellow-400 text-black font-black py-5 rounded-2xl transition transform active:scale-95 shadow-lg shadow-yellow-500/20 flex items-center justify-center gap-3">
                            <i class="fas fa-redo"></i> COBA PESAN ULANG
                        </a>
                        <a href="{{ route('home') }}" 
                            class="w-full bg-white border-2 border-slate-100 text-slate-400 font-bold py-4 rounded-2xl hover:bg-slate-50 hover:text-slate-600 transition">
                            KEMBALI KE BERANDA
                        </a>
                    </div>
                </div>
            </div>

            {{-- PENDING STATE (Redirect to checkout or show info) --}}
            @else
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-white text-center">
                <div class="bg-yellow-500 px-8 py-12 relative">
                    <div class="relative z-10">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <i class="fas fa-clock text-yellow-500 text-4xl animate-pulse"></i>
                        </div>
                        <h1 class="text-3xl font-black text-white mb-2 tracking-tight">Menunggu Konfirmasi</h1>
                        <p class="text-white/80 font-bold text-sm">Kami sedang memverifikasi pembayaran Anda.</p>
                    </div>
                </div>

                <div class="p-8 md:p-12">
                    <p class="text-gray-500 mb-8 leading-relaxed">Silakan tunggu beberapa saat atau refresh halaman ini untuk memperbarui status pesanan Anda.</p>
                    
                    <div class="flex flex-col gap-4">
                        <button onclick="window.location.reload()" 
                            class="w-full bg-slate-900 hover:bg-slate-800 text-white font-black py-5 rounded-2xl transition shadow-lg">
                            <i class="fas fa-sync-alt mr-2"></i> CEK STATUS TERBARU
                        </button>
                        <a href="{{ route('orders.checkout', $order->order_token) }}" 
                            class="w-full bg-brand-yellow hover:bg-yellow-400 text-black font-black py-4 rounded-2xl transition">
                            HALAMAN PEMBAYARAN
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
@endsection
