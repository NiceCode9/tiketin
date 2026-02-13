@extends('layouts.app')

@section('title', 'Payment Pending - Tiketin')

@section('content')
    <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="card animate-scale-in text-center overflow-hidden">
                {{-- Pending Header --}}
                <div class="h-32 bg-slate-100 flex items-center justify-center relative">
                    <div
                        class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-xl animate-pulse z-10">
                        <i class="fas fa-clock text-4xl text-brand-yellow"></i>
                    </div>
                </div>

                <div class="p-8">
                    <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Payment Pending</h1>
                    <p class="text-slate-600 mb-8">
                        We're waiting for your payment confirmation. Your order will be automatically confirmed once payment
                        is received.
                    </p>

                    <div class="bg-slate-50 rounded-2xl p-6 mb-8 border border-slate-100">
                        <p class="text-sm text-slate-500 mb-1 text-center font-semibold uppercase tracking-wider">Order
                            Number</p>
                        <p class="text-2xl font-mono font-bold text-slate-900 text-center">{{ $order->order_number }}</p>
                    </div>

                    <div class="space-y-4">
                        <a href="{{ route('orders.show', $order->order_token) }}"
                            class="block w-full bg-brand-primary hover:bg-slate-800 text-white font-bold py-4 rounded-xl transition transform hover:scale-105 shadow-lg">
                            <i class="fas fa-search mr-2"></i>
                            Check Order Status
                        </a>
                        <p class="text-xs text-slate-400">
                            Updates automatically every few minutes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
