@extends('layouts.app')

@section('title', 'Payment Failed - Tiketin')

@section('content')
    <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="card animate-scale-in text-center overflow-hidden border-t-4 border-red-500">
                {{-- Failed Header --}}
                <div class="h-32 bg-red-50 flex items-center justify-center relative">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-xl z-10">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
                    </div>
                </div>

                <div class="p-8 text-center">
                    <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Payment Failed</h1>
                    <p class="text-slate-600 mb-8">
                        Oops! Something went wrong with your transaction. No worries, your tickets aren't gone yet!
                    </p>

                    <div class="bg-red-50 rounded-2xl p-6 mb-8 border border-red-100">
                        <p class="text-sm text-red-600 font-semibold uppercase tracking-wider mb-1">Status</p>
                        <p class="text-xl font-bold text-slate-900">{{ strtoupper($order->payment_status) }}</p>
                        <p class="text-xs text-slate-500 mt-2 font-mono">{{ $order->order_number }}</p>
                    </div>

                    <div class="space-y-4">
                        <a href="{{ route('orders.checkout', $order->order_token) }}"
                            class="block w-full bg-brand-primary hover:bg-slate-800 text-white font-bold py-4 rounded-xl transition transform hover:scale-105 shadow-lg">
                            <i class="fas fa-redo mr-2"></i>
                            Try Again
                        </a>
                        <a href="{{ route('events.index') }}"
                            class="block w-full bg-white border-2 border-slate-200 hover:border-slate-300 text-slate-700 font-semibold py-4 rounded-xl transition font-bold">
                            Back to Events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
