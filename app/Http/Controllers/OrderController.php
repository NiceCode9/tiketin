<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PromoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected PromoService $promoService
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

        if ($hasSeatedTickets) {
            return view('orders.create-seated', compact('event'));
        }

        return view('orders.create', compact('event'));
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
            'consumer_whatsapp' => 'required|string|max:255',
            'consumer_identity_type' => 'required|in:ktp,sim,passport',
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

            if (!$result['valid']) {
                return back()->with('error', $result['message']);
            }

            $promo = $result['promo_code'];
            $this->promoService->applyPromoCode($order, $promo);

            return back()->with('success', 'Promo code applied successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show order details
     */
    public function show(string $orderToken)
    {
        $order = Order::where('order_token', $orderToken)
            ->with(['event', 'orderItems.ticketCategory', 'tickets'])
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }
}
