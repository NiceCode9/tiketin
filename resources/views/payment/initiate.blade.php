@extends('layouts.app')

@section('title', 'Payment - Order #' . $order->order_number)

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Complete Your Payment</h1>
        
        <div class="mb-6">
            <p class="text-gray-600">Order Number: <strong>{{ $order->order_number }}</strong></p>
            <p class="text-gray-600">Total Amount: <strong class="text-2xl text-indigo-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></p>
        </div>

        <button id="pay-button" class="w-full bg-indigo-600 text-white py-3 px-6 rounded-md hover:bg-indigo-700 transition font-semibold">
            Pay Now
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.getElementById('pay-button').addEventListener('click', function() {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                window.location.href = '{{ route("payment.finish", $order->order_token) }}';
            },
            onPending: function(result) {
                window.location.href = '{{ route("payment.finish", $order->order_token) }}';
            },
            onError: function(result) {
                alert('Payment failed. Please try again.');
            },
            onClose: function() {
                alert('You closed the payment popup without completing the payment.');
            }
        });
    });
</script>
@endpush
