@extends('layouts.app')

@section('title', 'Selesaikan Pembayaran')

@section('content')
    <div class="py-20 bg-gray-50 min-h-[80vh] flex items-center justify-center">
        <div class="container mx-auto px-4 max-w-2xl">
            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-white">
                
                <!-- Brand Header -->
                <div class="bg-brand-yellow px-8 py-10 text-center relative">
                    <div class="absolute inset-0 opacity-10">
                        <div class="grid grid-cols-6 gap-4 rotate-12 scale-150">
                            @for($i=0; $i<24; $i++) <i class="fas fa-credit-card text-black text-xl"></i> @endfor
                        </div>
                    </div>
                    <div class="relative z-10">
                        <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg rotate-3">
                            <i class="fas fa-wallet text-black text-3xl"></i>
                        </div>
                        <h1 class="text-3xl font-black text-black mb-2 tracking-tight">Selesaikan Pembayaran</h1>
                        <p class="text-black/60 font-bold text-sm">Pesanan #{{ $order->order_number }}</p>
                    </div>
                </div>

                <div class="p-8 md:p-12">
                    <!-- Progress Bar (Fake but UX-friendly) -->
                    <div id="loading-indicator" class="text-center mb-10">
                        <div class="relative w-20 h-20 mx-auto mb-6">
                            <div class="absolute inset-0 border-4 border-gray-100 rounded-full"></div>
                            <div class="absolute inset-0 border-4 border-brand-yellow rounded-full border-t-transparent animate-spin"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-lock text-gray-300"></i>
                            </div>
                        </div>
                        <p class="text-slate-900 font-black text-lg">Membuka Gerbang Pembayaran...</p>
                        <p class="text-gray-400 text-sm mt-1">Harap jangan menutup halaman ini</p>
                    </div>

                    <!-- Status Messages (Success/Error) -->
                    <div id="status-message" class="hidden mb-8"></div>

                    <!-- Order Summary Card -->
                    <div class="bg-slate-50 rounded-3xl p-8 mb-8 border border-slate-100">
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <i class="fas fa-receipt text-slate-400"></i> Ringkasan Pesanan
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-start gap-4">
                                <span class="text-gray-500 text-sm">Event</span>
                                <span class="font-bold text-slate-900 text-right">{{ $order->event->name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-sm">Pembeli</span>
                                <span class="font-bold text-slate-700 text-sm">{{ $order->consumer_name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-sm">Total Tiket</span>
                                <span class="font-bold text-slate-700 text-sm">{{ $order->orderItems->sum('quantity') }} Tiket</span>
                            </div>
                            
                            <div class="pt-6 mt-6 border-t border-slate-200 flex justify-between items-center">
                                <span class="font-black text-slate-900 text-lg uppercase tracking-tight">Total Bayar</span>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-brand-yellow">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expiry Info -->
                    <div class="flex items-center gap-4 bg-red-50 rounded-2xl p-4 border border-red-100 mb-10">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                            <i class="fas fa-clock text-red-500"></i>
                        </div>
                        <p class="text-xs text-red-700 leading-relaxed">
                            Batas waktu pembayaran berkahir pada <span class="font-black uppercase">{{ $order->expires_at->format('H:i') }} WIB</span> hari ini. Mohon selesaikan sebelum waktu habis.
                        </p>
                    </div>

                    <!-- Action Buttons (Shown when needed) -->
                    <div id="action-buttons" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <button onclick="retryPayment()"
                            class="bg-brand-yellow hover:bg-yellow-400 text-black font-black py-4 px-6 rounded-2xl transition transform active:scale-95 shadow-lg shadow-yellow-500/20">
                            <i class="fas fa-redo mr-2 text-xs"></i> COBA LAGI
                        </button>
                        <form action="{{ route('orders.cancel', $order->order_token) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit"
                                class="w-full bg-white border-2 border-slate-100 text-slate-400 font-bold py-4 px-6 rounded-2xl hover:bg-slate-50 hover:text-slate-600 transition">
                                BATALKAN
                            </button>
                        </form>
                    </div>

                    <div class="mt-10 pt-8 border-t border-gray-50 text-center">
                        <div class="flex items-center justify-center gap-6 opacity-40 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/d/d1/Midtrans.png" alt="Midtrans" class="h-6">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Visa_2021.svg/512px-Visa_2021.svg.png" alt="Visa" class="h-4">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/1280px-Mastercard-logo.svg.png" alt="Mastercard" class="h-6">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Midtrans Snap Script --}}
    @if(config('midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif

    <script type="text/javascript">
        const snapToken = "{{ $order->snap_token }}";
        const orderToken = "{{ $order->order_token }}";
        const checkStatusUrl = "{{ route('orders.status-check', $order->order_token) }}";
        const successUrl = "{{ route('orders.show', $order->order_token) }}";

        let statusCheckInterval;

        window.onload = function() {
            if (snapToken) {
                setTimeout(() => {
                    openPaymentPopup();
                }, 1000);
            } else {
                showStatusMessage('Token pembayaran tidak valid. Silakan hubungi dukungan.', 'error');
            }
        };

        function openPaymentPopup() {
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Success:', result);
                    window.location.href = successUrl;
                },
                onPending: function(result) {
                    console.log('Pending:', result);
                    hideLoading();
                    showStatusMessage('Pembayaran sedang dalam proses. Harap segera selesaikan di aplikasi pembayaran Anda.', 'info');
                    startStatusCheck();
                },
                onError: function(result) {
                    console.error('Error:', result);
                    hideLoading();
                    showActionButtons();
                    showStatusMessage('Terjadi kesalahan pada pembayaran. Silakan coba kembali.', 'error');
                },
                onClose: function() {
                    console.log('Popup closed');
                    hideLoading();
                    showActionButtons();
                    showStatusMessage('Anda menutup jendela pembayaran. Klik tombol di bawah untuk mencoba lagi.', 'warning');
                }
            });
        }

        function retryPayment() {
            hideActionButtons();
            showLoading();
            openPaymentPopup();
        }

        function startStatusCheck() {
            if (statusCheckInterval) return;
            
            statusCheckInterval = setInterval(async () => {
                try {
                    const response = await fetch(checkStatusUrl);
                    const data = await response.json();

                    if (data.is_paid) {
                        clearInterval(statusCheckInterval);
                        window.location.href = successUrl;
                    }
                } catch (e) {
                    console.error('Failed to check status');
                }
            }, 5000);
        }

        function showStatusMessage(message, type) {
            const el = document.getElementById('status-message');
            const classes = {
                'info': 'bg-blue-50 text-blue-700 border-blue-100',
                'error': 'bg-red-50 text-red-700 border-red-100',
                'warning': 'bg-yellow-50 text-yellow-700 border-yellow-100'
            };
            const icons = {
                'info': 'fa-info-circle',
                'error': 'fa-times-circle',
                'warning': 'fa-exclamation-triangle'
            };

            el.innerHTML = `
                <div class="px-6 py-4 rounded-2xl border ${classes[type]} flex items-center gap-4">
                    <i class="fas ${icons[type]} text-lg"></i>
                    <p class="text-sm font-bold">${message}</p>
                </div>
            `;
            el.classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loading-indicator').classList.add('hidden');
        }

        function showLoading() {
            document.getElementById('loading-indicator').classList.remove('hidden');
            document.getElementById('status-message').classList.add('hidden');
        }

        function showActionButtons() {
            document.getElementById('action-buttons').classList.remove('hidden');
        }

        function hideActionButtons() {
            document.getElementById('action-buttons').classList.add('hidden');
        }

        // Clean up
        window.onbeforeunload = function() {
            if (statusCheckInterval) clearInterval(statusCheckInterval);
        };
    </script>
@endpush
