@extends('layouts.app')

@section('title', 'Order Details - #' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Details</h1>
    <p class="text-gray-600 mb-8">Order #{{ $order->order_number }}</p>

    <!-- Order Status -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Order Status</h2>
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                @if($order->payment_status === 'paid') bg-green-100 text-green-800
                @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                @else bg-red-100 text-red-800
                @endif">
                {{ ucfirst($order->payment_status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600">Event</p>
                <p class="font-semibold text-gray-900">{{ $order->event->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Order Date</p>
                <p class="font-semibold text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Amount</p>
                <p class="font-semibold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>
            @if($order->payment_status === 'pending')
                <div>
                    <p class="text-sm text-gray-600">Expires At</p>
                    <p class="font-semibold text-gray-900">{{ $order->expires_at->format('d M Y, H:i') }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Items</h2>
        
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

    <!-- Tickets (if paid) -->
    @if($order->isPaid() && $order->tickets->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Your Tickets</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($order->tickets as $ticket)
                    <div class="border rounded-lg p-4">
                        <p class="font-semibold text-gray-900 mb-2">{{ $ticket->ticketCategory->name }}</p>
                        <p class="text-sm text-gray-600">Ticket #{{ $ticket->id }}</p>
                        @if($ticket->seat)
                            <p class="text-sm text-gray-600">Seat: {{ $ticket->seat->full_seat }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-2">Status: {{ ucfirst($ticket->status) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
