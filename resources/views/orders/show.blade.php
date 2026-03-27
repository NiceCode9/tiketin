@extends('layouts.app')

@section('title', 'Detail Pesanan - ' . $order->order_number)

@section('content')
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-4xl">

            {{-- Polling and Manual Refresh for Pending Orders --}}
            @if ($order->payment_status === 'pending')
                <div x-data="{
                    status: '{{ $order->payment_status }}',
                    checkStatus() {
                        fetch('{{ route('orders.status', $order->order_token) }}')
                            .then(response => response.json())
                            .then(data => {
                                if (data.status !== this.status) {
                                    window.location.reload();
                                }
                            });
                    }
                }" x-init="setInterval(() => checkStatus(), 5000)">
                </div>
            @endif

            {{-- Status Alerts --}}
            @if ($order->isPaid())
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-6 mb-8 rounded-2xl shadow-sm animate-slide-down">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-emerald-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-black text-emerald-900 text-lg">Pembayaran Berhasil!</p>
                            <p class="text-sm text-emerald-700">Tiket elektronik Anda telah aktif dan siap digunakan. Detail
                                tiket ada di bawah.</p>
                        </div>
                    </div>
                </div>
            @elseif($order->payment_status === 'pending')
                <div class="bg-amber-50 border-l-4 border-amber-500 p-6 mb-8 rounded-2xl shadow-sm animate-slide-down">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-amber-600 text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-black text-amber-900 text-lg">Menunggu Pembayaran</p>
                            <p class="text-sm text-amber-700 font-medium">Batas waktu pembayaran:
                                <span class="font-black underline">{{ $order->expires_at->format('d M Y, H:i') }} WIB</span>
                            </p>
                        </div>
                        <div class="ml-auto flex flex-wrap items-center justify-end gap-3 sm:gap-4">
                            <form action="{{ route('orders.cancel', $order->order_token) }}" method="POST"
                                onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                @csrf
                                <button type="submit"
                                    class="text-slate-400 hover:text-red-500 text-[10px] font-black uppercase tracking-wider transition-colors">
                                    Batalkan Pesanan
                                </button>
                            </form>
                            
                            @if($order->snap_token)
                                <form action="{{ route('orders.refreshPayment', $order->order_token) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-brand-primary hover:text-brand-secondary text-[10px] font-black uppercase tracking-wider transition-colors">
                                        Ganti Metode Pembayaran
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('payment.initiate', $order->order_token) }}"
                                class="bg-brand-yellow hover:bg-yellow-400 text-black font-black py-2.5 px-6 rounded-xl transition shadow-md text-sm flex items-center gap-2">
                                <i class="fas fa-wallet text-xs"></i>
                                {{ $order->snap_token ? 'Lanjutkan Pembayaran' : 'Bayar Sekarang' }}
                            </a>
                        </div>
                    </div>
                </div>
            @elseif($order->isExpired() || $order->payment_status === 'canceled')
                <div class="bg-slate-200 border-l-4 border-slate-500 p-6 mb-8 rounded-2xl shadow-sm animate-slide-down">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-ban text-slate-500 text-2xl"></i>
                        </div>
                        <div>
                            <p class="font-black text-slate-700 text-lg">
                                {{ $order->payment_status === 'canceled' ? 'Pesanan Dibatalkan' : 'Pesanan Kadaluarsa' }}
                            </p>
                            <p class="text-sm text-slate-500">
                                {{ $order->payment_status === 'canceled' ? 'Pesanan ini telah dibatalkan oleh Anda.' : 'Maaf, waktu pembayaran untuk pesanan ini telah habis.' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Order Header Card -->
            <div class="card p-8 mb-8 animate-slide-up">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Order Number</span>
                            <span
                                class="px-2 py-0.5 bg-slate-100 text-[10px] font-bold rounded text-slate-600 font-mono">#{{ $order->id }}</span>
                        </div>
                        <h1 class="text-3xl font-black text-slate-900 tracking-tight">{{ $order->order_number }}</h1>
                    </div>
                    <div>
                        @php $badge = $order->getStatusBadge(); @endphp
                        <span
                            class="px-6 py-2.5 rounded-2xl text-sm font-black shadow-sm flex items-center gap-2
                            @if ($badge['color'] === 'success') bg-emerald-100 text-emerald-700 border border-emerald-200
                            @elseif($badge['color'] === 'warning') bg-brand-yellow/20 text-yellow-700 border border-brand-yellow/30
                            @elseif($badge['color'] === 'danger') bg-red-100 text-red-700 border border-red-200
                            @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                            <span
                                class="w-2.5 h-2.5 rounded-full animate-pulse
                                @if ($badge['color'] === 'success') bg-emerald-500
                                @elseif($badge['color'] === 'warning') bg-yellow-500
                                @elseif($badge['color'] === 'danger') bg-red-500
                                @else bg-slate-500 @endif"></span>
                            {{ strtoupper($badge['label']) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 pt-8 border-t border-slate-100">
                    <!-- Customer Info -->
                    <div>
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-user text-brand-yellow mr-2"></i> Informasi Pembeli
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nama Lengkap
                                </p>
                                <p class="font-bold text-slate-900">{{ $order->customer->full_name }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Email
                                    </p>
                                    <p class="text-sm font-medium text-slate-700 break-all">{{ $order->customer->email }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">No.
                                        WhatsApp</p>
                                    <p class="text-sm font-medium text-slate-700">{{ $order->customer->phone_number }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Info -->
                    <div>
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-calendar-alt text-brand-yellow mr-2"></i> Informasi Event
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nama Event
                                </p>
                                <p class="font-bold text-slate-900 line-clamp-1">{{ $order->event->name }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Tanggal
                                    </p>
                                    <p class="text-sm font-medium text-slate-700">
                                        {{ $order->event->event_date->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Lokasi
                                    </p>
                                    <p class="text-sm font-medium text-slate-700 line-clamp-1">
                                        {{ $order->event->venue->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ticket Details (Iterating over Order Items) -->
            <div class="space-y-6 mb-12 animate-slide-up" style="animation-delay: 100ms">
                <h3 class="text-xl font-black text-slate-900 flex items-center">
                    <i class="fas fa-ticket-alt text-brand-yellow mr-3"></i>
                    E-Ticket Individual
                </h3>

                @foreach ($order->orderItems as $item)
                    <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden mb-6 shadow-sm">
                        {{-- Group Header --}}
                        <div class="bg-slate-50 px-8 py-4 border-b border-slate-100 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <span
                                    class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $item->ticketType->name }}</span>
                                @if ($order->promo_code)
                                    <span
                                        class="bg-brand-yellow/20 text-yellow-700 text-[10px] font-black px-2 py-0.5 rounded shadow-sm">
                                        PROMO: {{ $order->promo_code }}
                                    </span>
                                @endif
                                <span
                                    class="bg-slate-200 text-slate-600 text-[10px] font-black px-2 py-0.5 rounded">{{ $item->quantity }}
                                    Tiket</span>
                            </div>
                            <span class="text-xs font-bold text-slate-400">Rp
                                {{ number_format($item->unit_price, 0, ',', '.') }} / item</span>
                        </div>

                        {{-- Individual Tickets Render --}}
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if ($order->isPaid() && $order->tickets->where('ticket_category_id', $item->ticket_category_id)->count() > 0)
                                @foreach ($order->tickets->where('ticket_category_id', $item->ticket_category_id) as $ticket)
                                    <div class="relative group animate-scale-in">
                                        {{-- Ticket Body --}}
                                        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden flex flex-col h-full transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
                                            {{-- Top Section: Event & Brand --}}
                                            <div class="p-6 bg-gradient-to-br from-slate-50 to-white border-b border-slate-50 relative overflow-hidden">
                                                <div class="absolute top-0 right-0 p-4 opacity-5">
                                                    <i class="fas fa-ticket-alt text-6xl rotate-12"></i>
                                                </div>
                                                <div class="flex justify-between items-start relative z-10">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-8 h-8 bg-brand-primary rounded-lg flex items-center justify-center text-white text-[10px] font-black italic shadow-lg shadow-brand-primary/20">T</div>
                                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tiketin Verified</span>
                                                    </div>
                                                    <div class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded-md text-[8px] font-black uppercase tracking-wider border border-emerald-100">
                                                        Active
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Middle Section: Content --}}
                                            <div class="p-8 flex-grow">
                                                <div class="mb-6">
                                                    <h4 class="text-[10px] font-black text-brand-primary uppercase tracking-widest mb-1">{{ $item->ticketType->name }}</h4>
                                                    <p class="text-xl font-black text-slate-900 leading-tight">{{ $order->event->name }}</p>
                                                </div>

                                                <div class="grid grid-cols-2 gap-6 mb-6">
                                                    <div>
                                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Holder</p>
                                                        <p class="text-xs font-black text-slate-900 truncate">{{ $order->consumer_name }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Reference</p>
                                                        <p class="text-xs font-black text-slate-700 font-mono">#{{ $ticket->uuid ? substr($ticket->uuid, 0, 8) : $ticket->id }}</p>
                                                    </div>
                                                </div>

                                                @if ($ticket->seat)
                                                    <div class="bg-slate-50 rounded-2xl p-4 flex justify-around items-center border border-slate-100">
                                                        <div class="text-center">
                                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Section</p>
                                                            <p class="text-xs font-black text-brand-primary truncate max-w-[80px]">{{ $ticket->seat->venueSection->name }}</p>
                                                        </div>
                                                        <div class="w-px h-8 bg-slate-200"></div>
                                                        <div class="text-center">
                                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Row</p>
                                                            <p class="text-sm font-black text-slate-900">{{ $ticket->seat->row_label }}</p>
                                                        </div>
                                                        <div class="w-px h-8 bg-slate-200"></div>
                                                        <div class="text-center">
                                                            <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">Seat</p>
                                                            <p class="text-sm font-black text-slate-900">{{ $ticket->seat->seat_number }}</p>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="flex items-center gap-3 px-4 py-3 bg-indigo-50/50 rounded-2xl border border-indigo-100/50">
                                                        <div class="w-8 h-8 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                                            <i class="fas fa-users text-brand-primary text-xs"></i>
                                                        </div>
                                                        <p class="text-[10px] font-black text-indigo-700 uppercase tracking-widest">General Admission</p>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Ticket Stub Cutout --}}
                                            <div class="relative h-6 flex items-center py-3">
                                                <div class="absolute left-0 -ml-3 w-6 h-6 bg-slate-50 rounded-full border border-slate-100 shadow-inner"></div>
                                                <div class="flex-1 border-t-2 border-dashed border-slate-100 mx-4"></div>
                                                <div class="absolute right-0 -mr-3 w-6 h-6 bg-slate-50 rounded-full border border-slate-100 shadow-inner"></div>
                                            </div>

                                            {{-- Bottom Section: QR --}}
                                            <div class="px-8 pb-8 pt-4 flex items-center justify-between bg-white">
                                                <div class="flex flex-col gap-1">
                                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">Check-in QR</p>
                                                    <div class="flex items-center gap-2 text-emerald-600">
                                                        <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
                                                        <span class="text-[9px] font-black uppercase">Ready to Scan</span>
                                                    </div>
                                                </div>
                                                <div class="relative group/qr">
                                                    <div class="absolute -inset-2 bg-brand-primary/5 rounded-2xl scale-0 group-hover/qr:scale-100 transition-transform duration-500"></div>
                                                    <div class="p-2.5 bg-white rounded-2xl shadow-sm border border-slate-100 relative z-10">
                                                        {!! QrCode::size(55)->generate($ticket->uuid ?? $ticket->id) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div
                                    class="col-span-2 py-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                    <i class="fas fa-lock mb-2 opacity-50"></i>
                                    <p class="text-xs font-bold uppercase tracking-widest">Tiket dikunci - Selesaikan
                                        pembayaran</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Payment Summary -->
            <div class="card p-8 mb-12 animate-slide-up" style="animation-delay: 200ms">
                <h3 class="text-xl font-black text-slate-900 mb-8 flex items-center">
                    <i class="fas fa-receipt text-brand-yellow mr-3"></i> Ringkasan Pembayaran
                </h3>

                <div class="space-y-4 mb-8">
                    @foreach ($order->orderItems as $item)
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500 font-bold text-xs">
                                    {{ $item->quantity }}x
                                </div>
                                <span class="font-bold text-slate-700">{{ $item->ticketType->name }}</span>
                            </div>
                            <span class="font-black text-slate-900 font-mono">Rp
                                {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-slate-100 pt-6 space-y-3">
                    <div class="flex justify-between items-center text-slate-500">
                        <span class="text-xs font-bold uppercase tracking-widest">Subtotal</span>
                        <span class="font-bold font-mono">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>

                    @if ($order->total_biaya_layanan > 0)
                        <div class="flex justify-between items-center text-slate-500">
                            <span class="text-xs font-bold uppercase tracking-widest">Biaya Layanan</span>
                            <span class="font-bold font-mono">Rp
                                {{ number_format($order->total_biaya_layanan, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if ($order->total_biaya_admin_payment > 0)
                        <div class="flex justify-between items-center text-slate-500">
                            <span class="text-xs font-bold uppercase tracking-widest">Biaya Admin</span>
                            <span class="font-bold font-mono">Rp
                                {{ number_format($order->total_biaya_admin_payment, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($order->discount_amount > 0)
                        <div class="flex justify-between items-center text-emerald-600">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold uppercase tracking-widest">Diskon</span>
                                @if ($order->promo_usage)
                                    <span
                                        class="text-[9px] font-black bg-emerald-100 px-1.5 py-0.5 rounded mt-1 border border-emerald-200 w-fit">KODE:
                                        {{ $order->promo_usage->promoCode->code }}</span>
                                @endif
                            </div>
                            <span class="font-bold font-mono">- Rp
                                {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                        <span class="text-lg font-black text-slate-900 tracking-tight uppercase">Total Paid</span>
                        <span class="text-3xl font-black text-brand-primary font-mono tracking-tighter">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @if ($order->payment_method)
                    <div class="mt-8 grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Metode
                                Pembayaran</p>
                            <p class="text-sm font-black text-slate-900">{{ strtoupper($order->payment_method) }}</p>
                        </div>
                        @if ($order->paid_at)
                            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-right">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Waktu
                                    Pembayaran</p>
                                <p class="text-sm font-black text-slate-900">{{ $order->paid_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-slide-up" style="animation-delay: 300ms">
                <a href="{{ route('home') }}"
                    class="flex-1 max-w-xs group bg-slate-900 hover:bg-slate-800 text-white font-black py-4 px-8 rounded-2xl transition shadow-xl text-center flex items-center justify-center gap-3">
                    <i class="fas fa-home text-brand-yellow group-hover:-translate-y-1 transition"></i>
                    Beranda
                </a>
                <a href="{{ route('events.index') }}"
                    class="flex-1 max-w-xs group bg-white border-2 border-slate-200 hover:border-brand-primary text-slate-900 font-black py-4 px-8 rounded-2xl transition shadow-lg text-center flex items-center justify-center gap-3">
                    <i class="fas fa-calendar text-brand-primary group-hover:rotate-12 transition"></i>
                    Lihat Event Lain
                </a>
                @if ($order->isPaid())
                    <a href="{{ route('orders.invoice', $order->order_token) }}"
                        class="flex-1 max-w-xs group bg-white border-2 border-slate-200 hover:border-emerald-500 text-slate-900 font-black py-4 px-8 rounded-2xl transition shadow-lg text-center flex items-center justify-center gap-3">
                        <i class="fas fa-file-pdf text-red-500 group-hover:scale-110 transition"></i>
                        Download PDF
                    </a>
                @endif
            </div>

            <p class="text-center mt-12 text-[10px] text-slate-400 uppercase tracking-widest font-black">
                Tiketin © {{ date('Y') }} • Secured Transaction System
            </p>
        </div>
    </div>
@endsection
