<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Show the tracking search form
     */
    public function index()
    {
        return view('tracking.index');
    }

    /**
     * Track orders by identity number
     */
    public function track(Request $request)
    {
        $request->validate([
            'identity_number' => 'required|string',
        ]);

        $identityNumber = $request->identity_number;
        
        $orders = Order::where('consumer_identity_number', $identityNumber)
            ->with(['event'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('tracking.results', [
            'orders' => $orders,
            'identityNumber' => $identityNumber
        ]);
    }

    /**
     * Show order details
     */
    public function show($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['event', 'orderItems.ticketCategory', 'tickets'])
            ->firstOrFail();

        return view('tracking.show', compact('order'));
    }

    /**
     * Download invoice placeholder
     */
    public function downloadInvoice($orderNumber)
    {
        // Placeholder for PDF download
        return response()->json(['message' => 'Invoice download coming soon']);
    }
}
