<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    /**
     * Display the tracking search form.
     */
    public function index()
    {
        return view('tracking.index');
    }

    /**
     * Search for orders by identity number.
     */
    public function track(Request $request)
    {
        $request->validate([
            'identity_number' => 'required|string|min:5',
        ]);

        $identityNumber = $request->identity_number;
        $ordersCount = Order::where('consumer_identity_number', $identityNumber)->count();

        if ($ordersCount === 0) {
            return back()->with('error', 'Pesanan tidak ditemukan untuk nomor identitas tersebut.')->withInput();
        }

        return redirect()->route('tracking.results', ['id' => $identityNumber]);
    }

    /**
     * Display the list of orders found.
     */
    public function results(Request $request)
    {
        $identityNumber = $request->id;

        if (!$identityNumber) {
            return redirect()->route('tracking.index');
        }

        $orders = Order::with(['event', 'orderItems.ticketCategory'])
            ->where('consumer_identity_number', $identityNumber)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('tracking.index')->with('error', 'Pesanan tidak ditemukan.');
        }

        // Get customer data from the first order
        $firstOrder = $orders->first();
        $customer = (object) [
            'full_name' => $firstOrder->consumer_name,
            'identity_number' => $firstOrder->consumer_identity_number,
        ];

        return view('tracking.results', compact('orders', 'customer'));
    }

    /**
     * Display specific order details.
     */
    public function show($order_number)
    {
        $order = Order::with(['event.venue', 'orderItems.ticketCategory'])
            ->where('order_number', $order_number)
            ->firstOrFail();

        return view('tracking.detail', compact('order'));
    }
}
