@extends('layouts.app')

@section('title', 'Complete Payment - Order #' . $order->order_number)

@section('content')
    <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
        <div class="max-w-2xl w-full">
            <div class="card overflow-hidden animate-scale-in">
                {{-- Header --}}
                <div class="bg-slate-900 p-8 text-center relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10"
                        style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 24px 24px;">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="w-16 h-16 bg-brand-yellow rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg rotate-3 group-hover:rotate-0 transition duration-300">
                            <i class="fas fa-credit-card text-2xl text-slate-900 border-none"></i>
                        </div>
                        <h1 class="text-2xl font-black text-white tracking-tight">Complete Payment</h1>
                        <p class="text-slate-400 text-sm mt-1">Please finish your transaction to secure your tickets</p>
                    </div>
                </div>

                <div class="p-8">
                    {{-- Loading State --}}
                    <div id="payment-loading" class="text-center py-12 animate-pulse">
                        <div
                            class="inline-block w-12 h-12 border-4 border-brand-yellow border-t-transparent rounded-full animate-spin mb-4">
                        </div>
                        <p class="text-slate-600 font-bold">Opening secure payment gateway...</p>
                        <p class="text-slate-400 text-xs mt-2">Please do not refresh or close this window</p>
                    </div>

                    {{-- Order Summary --}}
                    <div class="bg-slate-50 rounded-3xl p-6 mb-8 border border-slate-100">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-receipt mr-2 text-brand-yellow"></i>
                            Order Details
                        </h3>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Event</span>
                                <span class="font-bold text-slate-900">{{ $order->event->name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Order Number</span>
                                <span class="font-mono font-bold text-brand-primary">{{ $order->order_number }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Customer</span>
                                <span class="font-bold text-slate-900">{{ $order->consumer_name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Tickets</span>
                                <span class="font-bold text-slate-900">{{ $order->orderItems->sum('quantity') }}
                                    Items</span>
                            </div>
                            <div class="pt-3 mt-3 border-t border-slate-200 flex justify-between items-center">
                                <span class="text-slate-500 font-bold uppercase text-xs">Total Amount</span>
                                <span class="text-2xl font-black text-brand-primary">Rp
                                    {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Expiry Notice --}}
                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 mb-8 flex items-start gap-4">
                        <i class="fas fa-clock text-amber-500 mt-1"></i>
                        <div>
                            <p class="text-xs text-amber-800 font-bold uppercase tracking-wider mb-0.5">Payment Deadline</p>
                            <p class="text-sm text-amber-900 font-medium">Valid until: <span
                                    class="font-bold">{{ $order->expires_at->format('d M Y, H:i') }}</span></p>
                        </div>
                    </div>

                    {{-- Actions (Initialy Hidden) --}}
                    <div id="payment-actions" class="hidden space-y-4 animate-slide-up">
                        <button id="retry-button"
                            class="block w-full bg-brand-primary hover:bg-slate-800 text-white font-bold py-4 rounded-xl transition transform hover:scale-105 shadow-lg flex items-center justify-center gap-2">
                            <i class="fas fa-redo"></i>
                            Try Paying Again
                        </button>
                        <a href="{{ route('home') }}"
                            class="block w-full bg-white border-2 border-slate-200 hover:border-slate-300 text-slate-700 font-bold py-4 rounded-xl transition text-center">
                            Cancel & Return Home
                        </a>
                        <p class="text-center text-[10px] text-slate-400 px-8">
                            If the payment window didn't open automatically, please click the button above.
                        </p>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="bg-slate-50 py-4 px-8 border-t border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/Midtrans.png" alt="Midtrans"
                            class="h-4 grayscale hover:grayscale-0 transition opacity-50">
                    </div>
                    <div class="text-[10px] text-slate-400 flex items-center">
                        <i class="fas fa-lock mr-1 text-emerald-400"></i> Secure 256-bit SSL
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ config('midtrans.snap_url') }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
        const snapToken = '{{ $snapToken }}';
        const loadingEl = document.getElementById('payment-loading');
        const actionsEl = document.getElementById('payment-actions');
        const retryBtn = document.getElementById('retry-button');

        function startPayment() {
            if (!snapToken) {
                alert('Payment token is missing. Please refresh the page.');
                return;
            }

            snap.pay(snapToken, {
                onSuccess: function(result) {
                    window.location.href = '{{ route('payment.finish', $order->order_token) }}';
                },
                onPending: function(result) {
                    window.location.href = '{{ route('payment.finish', $order->order_token) }}';
                },
                onError: function(result) {
                    loadingEl.classList.add('hidden');
                    actionsEl.classList.remove('hidden');
                    console.error('Payment error:', result);
                },
                onClose: function() {
                    loadingEl.classList.add('hidden');
                    actionsEl.classList.remove('hidden');
                    console.log('User closed the popup');
                }
            });
        }

        // Auto-start on load
        window.onload = function() {
            setTimeout(startPayment, 1500); // Small delay for visual effect
        };

        retryBtn.addEventListener('click', function() {
            startPayment();
        });
    </script>
@endpush
