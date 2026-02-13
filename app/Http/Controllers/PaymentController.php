<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Initiate payment
     */
    public function initiate(string $orderToken)
    {
        $order = Order::where('order_token', $orderToken)->firstOrFail();

        // Check if order is valid for payment
        if ($order->payment_status !== 'pending') {
            return redirect()->route('orders.show', $orderToken)
                ->with('error', 'Order is not pending payment');
        }

        if ($order->expires_at < now()) {
            return redirect()->route('orders.show', $orderToken)
                ->with('error', 'Order has expired');
        }

        try {
            $snapToken = $this->paymentService->createSnapToken($order);

            return view('payment.initiate', compact('order', 'snapToken'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to initiate payment: '.$e->getMessage());
        }
    }

    /**
     * Handle Midtrans callback
     */
    public function callback(Request $request)
    {
        try {
            $this->paymentService->handleCallback($request->all());

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Payment finish page (redirect from Midtrans)
     */
    public function finish(string $orderToken)
    {
        $order = Order::where('order_token', $orderToken)
            ->with(['event', 'tickets'])
            ->firstOrFail();

        if ($order->isPaid()) {
            return view('payment.success', compact('order'));
        }

        if ($order->payment_status === 'pending') {
            return view('payment.pending', compact('order'));
        }

        return view('payment.failed', compact('order'));
    }
}
