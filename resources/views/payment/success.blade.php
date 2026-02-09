@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <div class="mb-6">
            <svg class="w-20 h-20 text-green-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Successful!</h1>
        <p class="text-gray-600 mb-8">Your tickets have been confirmed and sent to your email.</p>
        
        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <p class="text-sm text-gray-600 mb-2">Order Number</p>
            <p class="text-2xl font-bold text-gray-900 mb-4">{{ $order->order_number }}</p>
            
            <p class="text-sm text-gray-600 mb-2">Total Tickets</p>
            <p class="text-xl font-semibold text-gray-900">{{ $order->tickets->count() }}</p>
        </div>

        <div class="space-y-3">
            <a href="{{ route('orders.show', $order->order_token) }}" 
               class="block w-full bg-indigo-600 text-white py-3 px-6 rounded-md hover:bg-indigo-700 transition font-semibold">
                View Order Details
            </a>
            <a href="{{ route('events.index') }}" 
               class="block w-full bg-gray-200 text-gray-700 py-3 px-6 rounded-md hover:bg-gray-300 transition font-semibold">
                Browse More Events
            </a>
        </div>
    </div>
</div>
@endsection
