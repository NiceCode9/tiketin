@extends('layouts.app')

@section('title', 'Checkout - Order #' . $order->order_number)

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Page Header --}}
            <div class="mb-8 animate-fade-in">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h1 class="text-4xl font-bold text-slate-900 mb-2">Checkout</h1>
                        <p class="text-gray-600">Order #{{ $order->order_number }}</p>
                    </div>
                    <div class="text-right">
                        <span class="badge-warning text-sm">
                            <i class="fas fa-clock mr-1"></i> Pending Payment
                        </span>
                    </div>
                </div>
            </div>

            {{-- Expiration Timer --}}
            <div x-data="countdownTimer('{{ $order->expires_at->toIso8601String() }}')"
                class="card bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-300 mb-8 animate-pulse-slow">
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-hourglass-half text-2xl text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Complete Your Payment</h3>
                            <p class="text-gray-700">
                                Your order will expire on
                                <span class="font-bold text-red-600">{{ $order->expires_at->format('d M Y, H:i') }}
                                    WIB</span>
                            </p>
                            <div class="text-2xl font-bold text-red-600 mt-2">
                                <template x-if="!expired">
                                    <span><i class="fas fa-clock mr-2"></i><span x-text="displayText"></span>
                                        remaining</span>
                                </template>
                                <template x-if="expired">
                                    <span class="text-red-600">EXPIRED</span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- LEFT: Order Details --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Event Info Card --}}
                    <div class="card animate-slide-up">
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-slate-900 mb-4">
                                <i class="fas fa-calendar-check text-brand-primary mr-2"></i>
                                Event Information
                            </h2>
                            <div class="flex items-start gap-4">
                                @if ($order->event->banner_image)
                                    <img src="{{ Storage::url($order->event->banner_image) }}"
                                        alt="{{ $order->event->name }}" class="w-32 h-32 object-cover rounded-lg">
                                @else
                                    <div
                                        class="w-32 h-32 bg-gradient-to-r from-brand-primary to-brand-secondary rounded-lg">
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-slate-900 mb-2">{{ $order->event->name }}</h3>
                                    <div class="flex flex-col gap-2 text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar-alt text-brand-primary mr-2 w-5"></i>
                                            {{ $order->event->event_date->format('l, d F Y - H:i') }} WIB
                                        </div>
                                        @if ($order->event->venue)
                                            <div class="flex items-center">
                                                <i class="fas fa-map-marker-alt text-brand-primary mr-2 w-5"></i>
                                                {{ $order->event->venue->name }}, {{ $order->event->venue->city }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Order Items --}}
                    <div class="card animate-slide-up">
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-slate-900 mb-6">
                                <i class="fas fa-ticket-alt text-brand-yellow mr-2"></i>
                                Order Items
                            </h2>

                            <div class="space-y-4">
                                @foreach ($order->orderItems as $item)
                                    <div
                                        class="border-2 border-gray-200 rounded-xl p-5 hover:border-brand-primary transition-all duration-300">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-start gap-3">
                                                    <div
                                                        class="w-12 h-12 bg-brand-yellow rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <i class="fas fa-ticket-alt text-xl text-gray-900"></i>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h3 class="text-lg font-bold text-slate-900 mb-1">
                                                            {{ $item->ticketCategory->name }}
                                                        </h3>
                                                        <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                                            <span class="flex items-center">
                                                                <i class="fas fa-hashtag mr-1"></i>
                                                                Quantity: <strong
                                                                    class="ml-1">{{ $item->quantity }}</strong>
                                                            </span>
                                                            @if ($item->seat)
                                                                <span class="flex items-center">
                                                                    <i class="fas fa-chair mr-1"></i>
                                                                    Seat: <strong
                                                                        class="ml-1">{{ $item->seat->full_seat }}</strong>
                                                                </span>
                                                            @endif
                                                            <span class="flex items-center">
                                                                <i class="fas fa-tag mr-1"></i>
                                                                Rp {{ number_format($item->price, 0, ',', '.') }} / ticket
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-2xl font-bold text-brand-primary">
                                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Customer Information --}}
                    <div class="card animate-slide-up">
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-slate-900 mb-6">
                                <i class="fas fa-user-circle text-brand-yellow mr-2"></i>
                                Customer Information
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Full Name</p>
                                        <p class="font-semibold text-gray-900">{{ $order->consumer_name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-envelope text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Email</p>
                                        <p class="font-semibold text-gray-900">{{ $order->consumer_email }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fab fa-whatsapp text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">WhatsApp</p>
                                        <p class="font-semibold text-gray-900">{{ $order->consumer_whatsapp }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-id-card text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Identity</p>
                                        <p class="font-semibold text-gray-900">
                                            {{ strtoupper($order->consumer_identity_type) }}:
                                            {{ $order->consumer_identity_number }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Promo Code --}}
                    <div class="card animate-slide-up" x-data="promoForm('{{ route('orders.applyPromo', $order->order_token) }}')">
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-slate-900 mb-4">
                                <i class="fas fa-gift text-brand-yellow mr-2"></i>
                                Have a Promo Code?
                            </h2>

                            <div class="flex gap-3">
                                <input type="text" x-model="promoCode" placeholder="Enter your promo code"
                                    class="input flex-1" :disabled="loading || applied">
                                <button type="button" @click="applyPromo"
                                    class="bg-gray-700 hover:bg-gray-800 text-white px-8 py-3 rounded-xl font-semibold transition disabled:opacity-50"
                                    :disabled="loading || !promoCode || applied">
                                    <i class="fas mr-2" :class="loading ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                                    <span x-text="applied ? 'Applied' : 'Apply'"></span>
                                </button>
                            </div>

                            <p x-show="message" :class="valid ? 'text-green-500' : 'text-red-500'"
                                class="text-sm mt-2 animate-fade-in" style="display: none;">
                                <i class="fas mr-1" :class="valid ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                                <span x-text="message"></span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Payment Summary Sidebar --}}
                <div class="lg:col-span-1" x-data="{
                    discount: {{ $order->discount_amount }},
                    total: {{ $order->total_amount }}
                }"
                    @promo-applied.window="discount = $event.detail.discount; total = $event.detail.total">
                    <div class="card sticky top-28 animate-scale-in">
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-slate-900 mb-6">
                                <i class="fas fa-receipt text-brand-yellow mr-2"></i>
                                Payment Summary
                            </h2>

                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between text-gray-700">
                                    <span>Subtotal</span>
                                    <span class="font-semibold">Rp
                                        {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                                </div>

                                @if ($order->total_biaya_layanan > 0)
                                    <div class="flex justify-between text-gray-700">
                                        <span>Biaya Layanan</span>
                                        <span class="font-semibold">Rp
                                            {{ number_format($order->total_biaya_layanan, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                @if ($order->total_biaya_admin_payment > 0)
                                    <div class="flex justify-between text-gray-700">
                                        <span>Biaya Admin</span>
                                        <span class="font-semibold">Rp
                                            {{ number_format($order->total_biaya_admin_payment, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                <div x-show="discount > 0" class="flex justify-between text-green-600 animate-slide-up"
                                    style="display: none;">
                                    <span class="flex items-center">
                                        <i class="fas fa-tag mr-2"></i> Discount
                                    </span>
                                    <span class="font-semibold">-Rp <span x-text="formatRupiah(discount)"></span></span>
                                </div>

                                <div class="border-t-2 border-gray-200 pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-gray-900">Total</span>
                                        <span class="text-3xl font-bold text-brand-yellow">
                                            Rp <span x-text="formatRupiah(total)"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('payment.initiate', $order->order_token) }}"
                                class="block w-full text-center bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 rounded-xl transition transform hover:scale-105 shadow-lg mb-4">
                                <i class="fas fa-credit-card mr-2"></i> Proceed to Payment
                            </a>

                            <p class="text-xs text-gray-500 text-center">
                                <i class="fas fa-shield-alt mr-1"></i> Secure payment powered by Midtrans
                            </p>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <p class="text-xs text-gray-600 mb-3 font-semibold">Accepted Payment Methods:</p>
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="bg-gray-100 rounded p-2 text-center">
                                        <i class="fas fa-credit-card text-2xl text-gray-600"></i>
                                        <p class="text-xs mt-1">Cards</p>
                                    </div>
                                    <div class="bg-gray-100 rounded p-2 text-center">
                                        <i class="fas fa-university text-2xl text-gray-600"></i>
                                        <p class="text-xs mt-1">Bank</p>
                                    </div>
                                    <div class="bg-gray-100 rounded p-2 text-center">
                                        <i class="fas fa-wallet text-2xl text-gray-600"></i>
                                        <p class="text-xs mt-1">E-Wallet</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function countdownTimer(expiresAt) {
            return {
                expiresTime: new Date(expiresAt).getTime(),
                displayText: '',
                expired: false,

                init() {
                    this.update();
                    setInterval(() => this.update(), 1000);
                },

                update() {
                    const now = new Date().getTime();
                    const distance = this.expiresTime - now;

                    if (distance < 0) {
                        this.expired = true;
                        return;
                    }

                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    this.displayText = `${hours}h ${minutes}m ${seconds}s`;
                }
            }
        }

        function promoForm(url) {
            return {
                promoCode: '',
                loading: false,
                message: '',
                valid: false,
                applied: false,

                async applyPromo() {
                    this.loading = true;
                    this.message = '';

                    try {
                        const response = await axios.post(url, {
                            promo_code: this.promoCode
                        });

                        const data = response.data;
                        this.valid = data.valid;
                        this.message = data.message;

                        if (data.valid) {
                            this.applied = true;
                            // Update total UI by dispatching event
                            window.dispatchEvent(new CustomEvent('promo-applied', {
                                detail: {
                                    discount: data.discount_amount,
                                    total: data.total_amount
                                }
                            }));
                        }
                    } catch (error) {
                        this.valid = false;
                        this.message = error.response?.data?.message || 'Something went wrong';
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }

        function formatRupiah(amount) {
            return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }
    </script>
@endpush
