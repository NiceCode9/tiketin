<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PromoService;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected PromoService $promoService,
        protected InvoiceService $invoiceService,
        protected PaymentService $paymentService
    ) {}

    /**
     * Show ticket selection page
     */
    public function create(string $eventSlug)
    {
        $event = Event::where('slug', $eventSlug)
            ->where('status', 'published')
            ->with(['ticketCategories.venueSection.seats'])
            ->firstOrFail();

        // Check if event has seated tickets
        $hasSeatedTickets = $event->ticketCategories->where('is_seated', true)->count() > 0;

        // Fetch all cities for the dropdown
        $cities = \App\Models\City::orderBy('name')->get();

        if ($hasSeatedTickets) {
            return view('orders.create-seated', compact('event', 'cities'));
        }

        return view('orders.create', compact('event', 'cities'));
    }

    /**
     * Store a new order
     */
    public function store(Request $request, string $eventSlug)
    {
        $event = Event::where('slug', $eventSlug)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'consumer_name' => 'required|string|max:255',
            'consumer_email' => 'required|email|max:255',
            'consumer_city' => 'required|string|max:255',
            'consumer_birth_date' => 'required|date',
            'consumer_whatsapp' => 'required|string|max:255',
            'consumer_identity_type' => 'required|in:KTP,SIM,Student Card,Passport',
            'consumer_identity_number' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.ticket_category_id' => 'required|exists:ticket_categories,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.seat_id' => 'nullable|exists:seats,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $order = $this->orderService->createOrder([
                'event_id' => $event->id,
                'consumer_name' => $request->consumer_name,
                'consumer_email' => $request->consumer_email,
                'consumer_city' => $request->consumer_city,
                'consumer_birth_date' => $request->consumer_birth_date,
                'consumer_whatsapp' => $request->consumer_whatsapp,
                'consumer_identity_type' => $request->consumer_identity_type,
                'consumer_identity_number' => $request->consumer_identity_number,
                'items' => $request->items,
            ]);

            return redirect()->route('orders.checkout', $order->order_token);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Show checkout page
     */
    public function checkout(string $orderToken)
    {
        $order = Order::where('order_token', $orderToken)
            ->with(['event', 'orderItems.ticketCategory', 'orderItems.seat'])
            ->firstOrFail();

        // Check if order is still valid
        if ($order->payment_status !== 'pending') {
            return redirect()->route('orders.show', $orderToken);
        }

        if ($order->expires_at < now()) {
            return redirect()->route('orders.show', $orderToken)
                ->with('error', 'Order has expired');
        }

        return view('orders.checkout', compact('order'));
    }

    /**
     * Apply promo code
     */
    public function applyPromo(Request $request, string $orderToken)
    {
        $order = Order::where('order_token', $orderToken)->firstOrFail();

        $request->validate([
            'promo_code' => 'required|string',
        ]);

        try {
            $result = $this->promoService->calculateDiscount(
                $request->promo_code,
                $order->event,
                $order->subtotal
            );

            if (! $result['valid']) {
                if ($request->expectsJson()) {
                    return response()->json(['valid' => false, 'message' => $result['message']]);
                }

                return back()->with('error', $result['message']);
            }

            $promo = $result['promo_code'];
            $this->promoService->applyPromoCode($order, $promo);

            if ($request->expectsJson()) {
                return response()->json([
                    'valid' => true,
                    'message' => 'Promo code applied successfully',
                    'discount_amount' => $order->discount_amount,
                    'total_amount' => $order->total_amount,
                    'formatted_discount' => number_format($order->discount_amount, 0, ',', '.'),
                    'formatted_total' => number_format($order->total_amount, 0, ',', '.'),
                ]);
            }

            return back()->with('success', 'Promo code applied successfully');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['valid' => false, 'message' => $e->getMessage()]);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function show(string $orderToken)
    {
        $order = Order::where('order_token', $orderToken)
            ->with(['event', 'orderItems.ticketCategory', 'tickets'])
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }

    /**
     * Refresh payment (Change Payment Method)
     */
    public function refreshPayment(string $orderToken)
    {
        $order = Order::where('order_token', $orderToken)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        // Just clear the snap token to allow a new one to be generated
        $order->update(['snap_token' => null]);

        return redirect()->route('orders.show', $orderToken)
            ->with('success', 'Silakan pilih kembali metode pembayaran Anda.');
    }

    /**
     * Cancel an order manually
     */
    public function cancel(string $orderToken)
    {
        $order = Order::where('order_token', $orderToken)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        try {
            DB::transaction(function () use ($order) {
                // Sync with Midtrans (Cancel the transaction)
                $this->paymentService->cancelTransaction($order);

                // Release reserved seats
                foreach ($order->orderItems as $item) {
                    if ($item->seat_id) {
                        \App\Models\Seat::where('id', $item->seat_id)
                            ->update(['status' => 'available']);
                    }
                    // Decrement sold count
                    $item->ticketCategory->decrement('sold_count', $item->quantity);
                }

                $order->update([
                    'payment_status' => 'canceled',
                    'snap_token' => null
                ]);
            });

            return redirect()->route('orders.show', $orderToken)->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel order: '.$e->getMessage());
        }
    }

    /**
     * Get payment status for polling
     */
    public function status(string $orderToken)
    {
        $order = Order::where('order_token', $orderToken)->firstOrFail();

        return response()->json([
            'status' => $order->payment_status,
            'is_paid' => $order->isPaid(),
        ]);
    }

    /**
     * Download or stream invoice PDF
     */
    public function downloadInvoice(string $orderToken)
    {
        $order = Order::where('order_token', $orderToken)
            ->with(['event', 'orderItems.ticketCategory', 'promoCodeUsages.promoCode'])
            ->firstOrFail();

        if (! $order->isPaid()) {
            return back()->with('error', 'Invoice is only available for paid orders.');
        }

        return $this->invoiceService->downloadInvoice($order);
    }
}
