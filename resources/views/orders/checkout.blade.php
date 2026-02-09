@extends('layouts.app')

@section('title', 'Checkout - Order #' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Checkout</h1>
    <p class="text-gray-600 mb-8">Order #{{ $order->order_number }}</p>

    <!-- Expiration Timer -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <p class="text-yellow-800">
            <strong>⏰ Complete your payment before:</strong> 
            {{ $order->expires_at->format('d M Y, H:i') }} WIB
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Summary -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-8 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>
                
                <div class="space-y-4">
                    @foreach($order->orderItems as $item)
                        <div class="flex justify-between items-start pb-4 border-b">
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $item->ticketCategory->name }}</h3>
                                <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                @if($item->seat)
                                    <p class="text-sm text-gray-600">Seat: {{ $item->seat->full_seat }}</p>
                                @endif
                            </div>
                            <p class="font-semibold text-gray-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Promo Code -->
            <div class="bg-white rounded-lg shadow-md p-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Promo Code</h2>
                
                <form action="{{ route('orders.applyPromo', $order->order_token) }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" 
                           name="promo_code" 
                           placeholder="Enter promo code"
                           class="flex-1 px-4 py-2 border rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    <button type="submit" 
                            class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition">
                        Apply
                    </button>
                </form>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Summary</h2>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    
                    <div class="border-t pt-3">
                        <div class="flex justify-between text-xl font-bold text-gray-900">
                            <span>Total</span>
                            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('payment.initiate', $order->order_token) }}" 
                   class="block w-full text-center bg-indigo-600 text-white py-3 px-6 rounded-md hover:bg-indigo-700 transition font-semibold">
                    Proceed to Payment
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
