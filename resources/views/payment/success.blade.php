@extends('layouts.app')

@section('title', 'Payment Successful - Tiketin')

@section('content')
    <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            {{-- Success Card --}}
            <div class="card animate-scale-in text-center overflow-hidden">
                {{-- Success Header with Gradient --}}
                <div class="h-32 bg-gradient-to-r from-green-400 to-emerald-500 flex items-center justify-center relative">
                    <div class="absolute inset-0 opacity-20"
                        style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px;">
                    </div>
                    <div
                        class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-xl animate-bounce-subtle z-10">
                        <i class="fas fa-check text-4xl text-emerald-500"></i>
                    </div>
                </div>

                <div class="p-8">
                    <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Payment Successful!</h1>
                    <p class="text-slate-600 mb-8">
                        Woot! Your payment has been confirmed. We've sent your tickets and receipt to
                        <span class="font-semibold text-slate-900">{{ $order->consumer_email }}</span>.
                    </p>

                    {{-- Order Quick Info --}}
                    <div class="bg-slate-50 rounded-2xl p-6 mb-8 border border-slate-100">
                        <div class="flex flex-col space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Order Number</span>
                                <span class="font-mono font-bold text-slate-900">{{ $order->order_number }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-slate-500">Total Tickets</span>
                                <span class="font-bold text-slate-900">{{ $order->tickets->count() }} Tickets</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-slate-200">
                                <span class="text-sm text-slate-500">Amount Paid</span>
                                <span class="text-xl font-bold text-emerald-600">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Primary Actions --}}
                    <div class="space-y-4">
                        <a href="{{ route('orders.show', $order->order_token) }}"
                            class="block w-full bg-brand-primary hover:bg-slate-800 text-white font-bold py-4 rounded-xl transition transform hover:scale-105 shadow-lg group">
                            <i class="fas fa-ticket-alt mr-2 group-hover:rotate-12 transition"></i>
                            View My Tickets
                        </a>
                        <a href="{{ route('events.index') }}"
                            class="block w-full bg-white border-2 border-slate-200 hover:border-brand-yellow text-slate-700 font-semibold py-4 rounded-xl transition">
                            <i class="fas fa-search mr-2 text-slate-400"></i>
                            Browse More Events
                        </a>
                    </div>
                </div>

                {{-- Trust Footer --}}
                <div class="bg-slate-50 py-4 border-t border-slate-100 flex items-center justify-center gap-6">
                    <div class="flex items-center text-xs text-slate-400">
                        <i class="fas fa-shield-alt mr-1 text-emerald-400"></i> Secure Order
                    </div>
                    <div class="flex items-center text-xs text-slate-400">
                        <i class="fas fa-check-circle mr-1 text-emerald-400"></i> Verified Info
                    </div>
                </div>
            </div>

            {{-- Support Link --}}
            <p class="text-center mt-8 text-slate-500 text-sm">
                Need help? <a href="#" class="text-brand-primary font-semibold hover:underline">Contact Support</a>
            </p>
        </div>
    </div>
@endsection
